<?php

namespace App\Http\Controllers;

use App\Models\CartItemModel;
use App\Models\CartModel;
use Illuminate\Http\Request;

class CartItemController extends Controller
{

    public function addItem(Request $request)
    {
        $request->validate([
            'cart_token' => 'required|string|exists:carts,cart_token',
            'food_id' => 'required|exists:food,id',
            'quantity' => 'required|integer|min:1',
        ]);


        $cart = CartModel::where('cart_token', $request->cart_token)->first();

        $existingItem = CartItemModel::where('cart_id', $cart->id)
            ->where('food_id', $request->food_id)
            ->first();

        if ($existingItem) {

            $existingItem->quantity += $request->quantity;
            $existingItem->save();
        } else {

            CartItemModel::create([
                'cart_id' => $cart->id,
                'food_id' => $request->food_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json(['message' => 'Item added to cart successfully']);
    }
}
