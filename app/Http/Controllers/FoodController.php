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
            'image' => 'required|string',
            'cuisine_id' => 'required|integer|exists:cuisine,id',
        ]);

        $imageData = $request->image;
        $imageInfo = explode(",", $imageData);

        $extension = str_replace(["data:image/", ";base64"], "", $imageInfo[0]);
        $imageName = "images/" . uniqid() . "." . $extension;

        try {
            file_put_contents(public_path($imageName), base64_decode($imageInfo[1]));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Image upload failed: ' . $e->getMessage(),
            ], 500);
        }



        $food = FoodModel::create([
            'name' => $request['name'],
            'description' => $request['description'],
            'price' => $request['price'],
            'cuisine_id' => $request['cuisine_id'],
            'discount_price' => $request['discount_price'],
            'vat_percentage' => $request['vat_percentage'],
            'stock_quantity' => $request['stock_quantity'],
            'status' => $request['status'],
            'image' => $imageName,
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
                "cuisine.name as cuisine_name",
                "food.discount_price",
                "food.vat_percentage",
                "food.stock_quantity",
                "food.status",
                "food.image",
                "food.created_at"
            )
            ->get();

        return response()->json([
            'food' => $food,
        ]);
    }


    public function update(Request $request, $id)
    {
        $food = FoodModel::find($id);
        if (!$food) {
            return response()->json(['message' => 'Food not found'], 404);
        }

        $food->update($request->only([
            'name',
            'description',
            'price',
            'discount_price',
            'vat_percentage',
            'stock_quantity',
            'status',
            'cuisine_id'
        ]));

        return response()->json(['message' => 'Food updated successfully', 'food' => $food]);
    }

    public function destroy($id)
    {
        $food = FoodModel::find($id);
        if (!$food) {
            return response()->json(['message' => 'Food not found'], 404);
        }

        $food->delete();

        return response()->json(['message' => 'Food deleted successfully']);
    }

    public function toggleStatus($id)
    {
        $food = FoodModel::find($id);
        if (!$food) {
            return response()->json(['message' => 'Food not found'], 404);
        }


        $food->status = ($food->status === 'active') ? 'inactive' : 'active';
        $food->save();

        return response()->json(['message' => 'Food status updated successfully', 'status' => $food->status]);
    }

    public function activeFoods()
    {
        $foods = FoodModel::leftjoin("cuisine", "food.cuisine_id", "=", "cuisine.id")
            ->select(
                "food.id",
                "food.name",
                "food.description",
                "food.price",
                "cuisine.name as cuisine_name",
                "food.discount_price",
                "food.vat_percentage",
                "food.stock_quantity",
                "food.status",
                "food.image",
                "food.created_at"
            )
            ->where('food.status', 'active')
            ->get();

        return response()->json([
            'foods' => $foods,
        ]);
    }
}
