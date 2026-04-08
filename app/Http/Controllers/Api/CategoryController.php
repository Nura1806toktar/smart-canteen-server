<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return  Category::paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required |string | max:255',
        ]);

        $category = Category::create($validated);

        return response()->json($category, 201);
    }

    public function show(Category $category)
{
    return $category->load('foods');
}

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required |string | max:255',
        ]);

        $category->update($validated);

        return response()->json($category->fresh(), 200);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json(null, 204);
    }


}
