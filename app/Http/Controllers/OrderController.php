<?php

namespace App\Http\Controllers;

use App\Models\CartModel;
use App\Models\OrderModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $request->validate([
            'guest_cart_token' => 'required|string|exists:carts,cart_token',
            'total_amount' => 'required|numeric|min:0',
            'customer_name' => 'required|string|max:255',
            'delivery_address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'order_notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.food_id' => 'required|integer|exists:food,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price_at_order' => 'required|numeric|min:0',
            'items.*.food_name' => 'required|string',
            'items.*.image' => 'nullable|string',
            'payment_method' => 'required|string|in:Cash on Delivery',
        ]);

        DB::beginTransaction();

        try {
            $cart = CartModel::where('cart_token', $request->guest_cart_token)->first();

            if (!$cart) {
                return response()->json(['message' => 'Cart not found or invalid token.'], 404);
            }

            $orderDetailsSnapshot = [];
            foreach ($request->items as $item) {
                $orderDetailsSnapshot[] = [
                    'food_id' => $item['food_id'],
                    'food_name' => $item['food_name'],
                    'quantity' => $item['quantity'],
                    'price_at_order' => $item['price_at_order'],
                    'image' => $item['image'],
                ];
            }

            $order = OrderModel::create([
                'cart_id' => $cart->id,
                'total_price' => $request->total_amount,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->phone_number,
                'customer_address' => $request->delivery_address,
                'payment_status' => 'pending',
                'order_notes' => $request->order_notes,
                'order_details' => $orderDetailsSnapshot,
            ]);

            $cart->status = 'ordered';
            $cart->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $order->id,
                'order' => $order,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order placement failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order. Please try again. Error: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function getAll()
    {
        $orders = OrderModel::with('cart')->orderBy('created_at', 'desc')->get();

        return response()->json(['orders' => $orders]);
    }

    public function updateStatus(Request $request, $id)
    {

        $validated = $request->validate([
            'status' => 'required|string|in:pending,processing,delivered,cancelled',
        ]);

        $order = OrderModel::find($id);

        if ($order) {
            $order->status = $validated['status'];
            $saved = $order->save();

            if ($saved) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Order status updated successfully.',
                    'order' => $order
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to save order status.'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found.'
            ], 404);
        }
    }
}
