<?php

namespace App\Http\Controllers;

use App\Models\CartItemModel; // Assuming this is your CartItem model
use App\Models\CartModel;    // Assuming this is your Cart model
use App\Models\FoodModel;   // Assuming this is your Food model

use Illuminate\Http\Request;

class CartController extends Controller
{
    // Existing method: Create a new cart (likely for authenticated users or initial setup)
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

    // Existing method: Add item to a cart (requires cart_id in URL, likely for authenticated users)
    public function addItem(Request $request, $cart_id)
    {
        $request->validate([
            'food_id' => 'required|exists:food,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // You might want to fetch food details here to get price for CartItemModel
        // $food = FoodModel::find($request->food_id);

        $item = CartItemModel::create([
            'cart_id' => $cart_id,
            'food_id' => $request->food_id,
            'quantity' => $request->quantity,
            // 'price' => $food->discount_price, // Add price if your CartItemModel has it
        ]);

        return response()->json(['message' => 'Item added to cart', 'item' => $item]);
    }

    // Existing method: Get cart details by token (likely for authenticated users)
    public function get($cart_token)
    {
        // Assuming 'items' is the relationship name in CartModel for CartItemModel
        $cart = CartModel::where('cart_token', $cart_token)->with('items')->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        return response()->json([
            'cart' => $cart
        ], 200);
    }

    // NEW Method: Get Guest Cart details by token (for public access)
    public function getGuestCart($cart_token)
    {
        $cart = CartModel::where('cart_token', $cart_token)
            ->with(['cartItems.food']) // Ensure 'cartItems' is the correct relationship name in CartModel
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
                    'price' => $item->food->discount_price, // Use discount_price for cart
                    'image' => $item->food->image,
                    'item_total' => ($item->quantity * $item->food->discount_price),
                ];
            }
            return null;
        })->filter()->values();

        return response()->json(['cart_items' => $formattedCartItems]);
    }

    // NEW Method: Add/Update item in Guest Cart (for public access)
    public function addGuestItem(Request $request)
    {
        $request->validate([
            'food_id' => 'required|integer|exists:food,id',
            'quantity' => 'required|integer|min:0', // Quantity can be 0 to remove item
            'cart_token' => 'required|string', // Guest cart token
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
            ['user_id' => null] // For guest carts, user_id is null
        );

        $cartItem = CartItemModel::where('cart_id', $cart->id)
            ->where('food_id', $food->id)
            ->first();

        if ($request->quantity > 0) {
            if ($cartItem) {
                $cartItem->quantity = $request->quantity;
                $cartItem->price = $food->discount_price; // Update price if it changed
                $cartItem->save();
            } else {
                CartItemModel::create([
                    'cart_id' => $cart->id,
                    'food_id' => $food->id,
                    'quantity' => $request->quantity,
                    'price' => $food->discount_price, // Price at the time of adding
                ]);
            }
            return response()->json(['message' => 'Item updated in cart successfully!'], 200);
        } else {
            // If quantity is 0, remove the item from cart
            if ($cartItem) {
                $cartItem->delete();
                return response()->json(['message' => 'Item removed from cart.'], 200);
            }
            return response()->json(['message' => 'Item not found in cart.'], 404);
        }
    }
}
