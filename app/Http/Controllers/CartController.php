<?php

namespace App\Http\Controllers;

use App\Models\CartModel;
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
}
