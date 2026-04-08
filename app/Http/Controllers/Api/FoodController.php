<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Food;

class FoodController extends Controller
{
    public function index()
    {
        return Food::with('category')->paginate(15);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required |string | max:255',
            'description' => 'nullable | string ',
            'price' => 'required |numeric | min:0',
            'category_id' => 'required | exists:categories,id',
            'image' => 'nullable | image |max:2048',
        ]);

        $food = Food::create($validated);

        return response()->json($food, 201);
    }

    public function show(Food $food)
    {
        return response()->json($food->load('category'));
    }

    public function update(Request $request, $id)
    {
        $food = Food::findOrfail($id);

        $validated = $request->validate([
            'name' => 'sometimes |string | max:255',
            'description' => 'sometimes |nullable | string ',
            'price' => 'sometimes |numeric | min:0',
            'category_id' => 'sometimes |exists:categories,id',
            'image' => 'sometimes |nullable|image |max:2048',
        ]);

        $food->update($validated);

        return response()->json($food->fresh());

    }

    public function destroy($id)
    {
        $food = Food::findOrfail($id);
        $food->delete();

        return response()->json(null, 204);
    }

}
