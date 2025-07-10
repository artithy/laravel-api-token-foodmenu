<?php

namespace App\Http\Controllers;

use App\Models\CuisineModel;
use Illuminate\Http\Request;

class CuisineController extends Controller
{
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|unique:cuisine,name'
        ]);

        $cuisine = CuisineModel::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Cuisine created successfully',
            'cuisine' => $cuisine,
        ], 201);
    }

    public function getAllCuisine(Request $request)

    {
        $cuisine = CuisineModel::all();

        return response()->json([
            'cuisine' => $cuisine,
        ]);
    }
}
