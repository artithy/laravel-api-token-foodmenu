<?php

namespace App\Http\Controllers;

use App\Models\CartItemModel;
use App\Models\CartModel;
use App\Models\FoodModel;

use Illuminate\Http\Request;

class CartController extends Controller
{

    public function create(Request $request)
    {
        $cart = CartModel::create([
            'cart_token' => uniqid('cart', true),
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Cart created successfully',
            'cart_token' => $cart->cart_token,
        ], 201);
    }


    public function addItem(Request $request, $cart_id)
    {
        $request->validate([
            'food_id' => 'required|exists:food,id',
            'quantity' => 'required|integer|min:1',
        ]);



        $item = CartItemModel::create([
            'cart_id' => $cart_id,
            'food_id' => $request->food_id,
            'quantity' => $request->quantity,

        ]);

        return response()->json(['message' => 'Item added to cart', 'item' => $item]);
    }


    public function get($cart_token)
    {

        $cart = CartModel::where('cart_token', $cart_token)->with('items')->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        return response()->json([
            'cart' => $cart
        ], 200);
    }


    public function getGuestCart($cart_token)
    {
        $cart = CartModel::where('cart_token', $cart_token)
            ->with(['cartItems.food'])
            ->first();

        if (!$cart) {
            return response()->json(['cart_items' => []]);
        }

        $formattedCartItems = $cart->cartItems->map(function ($item) {
            if ($item->food) {
                return [
                    'food_id' => $item->food_id,
                    'food_name' => $item->food->name,
                    'quantity' => $item->quantity,
                    'price' => $item->food->discount_price,
                    'image' => $item->food->image,
                    'item_total' => ($item->quantity * $item->food->discount_price),
                ];
            }
            return null;
        })->filter()->values();

        return response()->json(['cart_items' => $formattedCartItems]);
    }


    public function addGuestItem(Request $request)
    {
        $request->validate([
            'food_id' => 'required|integer|exists:food,id',
            'quantity' => 'required|integer|min:0',
            'cart_token' => 'required|string',
        ]);

        $food = FoodModel::find($request->food_id);
        if (!$food) {
            return response()->json(['message' => 'Food not found.'], 404);
        }

        if ($request->quantity > 0 && $food->stock_quantity < $request->quantity) {
            return response()->json(['message' => 'Not enough stock available.'], 400);
        }

        $cart = CartModel::firstOrCreate(
            ['cart_token' => $request->cart_token],
            ['user_id' => null]
        );

        $cartItem = CartItemModel::where('cart_id', $cart->id)
            ->where('food_id', $food->id)
            ->first();

        if ($request->quantity > 0) {
            if ($cartItem) {
                $cartItem->quantity = $request->quantity;
                $cartItem->price = $food->discount_price;
                $cartItem->save();
            } else {
                CartItemModel::create([
                    'cart_id' => $cart->id,
                    'food_id' => $food->id,
                    'quantity' => $request->quantity,
                    'price' => $food->discount_price,
                ]);
            }
            return response()->json(['message' => 'Item updated in cart successfully!'], 200);
        } else {

            if ($cartItem) {
                $cartItem->delete();
                return response()->json(['message' => 'Item removed from cart.'], 200);
            }
            return response()->json(['message' => 'Item not found in cart.'], 404);
        }
    }
}
