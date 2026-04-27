<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Food;
use App\Models\Category;
use App\Models\Rating;

class FoodController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'string|min:2|max:100',
            'category_id' => 'exists:categories,id',
            'available' => 'boolean',
            'min_price' => 'numeric|min:0',
            'max_price' => 'numeric|min:0',
            'sort_price' => 'in:asc,desc',
        ]);

        $query = Food::with(['category:id,name'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings');

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('available')) {
            $query->where('available', $request->available);
        } else {
            $query->where('available', true);
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('sort_price')) {
            $query->orderBy('price', $request->sort_price === 'desc' ? 'desc' : 'asc');
        } else {
            $query->latest();
        }

        $perPage = $request->get('per_page', 10);

        $foods = $query->paginate($perPage);

        return response()->json($foods);
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

    public function show($id)
    {
        $food = Food::with('category')
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->findOrFail($id);

        $food->load('ratings.user');

        return response()->json($food);
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
