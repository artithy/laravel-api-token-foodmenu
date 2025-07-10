<?php

namespace App\Http\Controllers;

use App\Models\FoodModel;
use Illuminate\Http\Request;

class FoodController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'required|numeric|min:0',
            'vat_percentage' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $food = FoodModel::create([
            'name' => $request['name'],
            'description' => $request['description'],
            'price' => $request['price'],
            'cuisine_id' => $request['cuisine_id'],
            'discount_price' => $request['discount_price'],
            'vat_percentage' => $request['vat_percentage'],
            'stock_quantity' => $request['stock_quantity'],
            'status',
        ]);

        if (!$food) {
            return response()->json([
                'message' => 'food creation failed'
            ], 500);
        }

        return response()->json([
            'message' => 'food created successfully',
            'food' => $food,
        ], 201);
    }

    public function foodWithCuisine(Request $request)
    {
        $food = FoodModel::leftjoin("cuisine", "food.cuisine_id", "=", "cuisine.id")
            ->select(
                "food.id",
                "food.name",
                "food.description",
                "food.price",
                "cuisine.name as cuisine_name ",
                "food.discount_price",
                "food.vat_percentage",
                "food.stock_quantity",
                "food.status",
                "food.created_at"
            )
            ->get();

        return response()->json([
            'food' => $food,
        ]);
    }
}
