<?php

namespace App\Http\Controllers;

use App\Models\OrderModel;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'total_price' => 'required|numeric',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'customer_address' => 'required|string',
        ]);

        $order = OrderModel::create([
            'cart_id' => $request->cart_id,
            'total_price' => $request->total_price,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'payment_status' => 'pending',
        ]);

        // Eager load cart with items and food
        $order->load('cart.items.food');

        return response()->json([
            'message' => 'Order placed successfully',
            'order' => $order,
        ], 201);
    }


    public function getAll()
    {
        $orders = OrderModel::with(['cart.items'])->get();

        return response()->json([
            'orders' => $orders
        ]);
    }
}
