<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Rating;
use App\Models\Food;

class RatingController extends Controller
{
    public function store(Request $request, $foodId)
    {
        $validated = $request->validate([
            'rating' => 'required | integer | min:1 | max:5',
            'comment' => 'nullable | string |max:500',
        ]);

        $food = Food::findOrFail($foodId);

        $rating = Rating::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'food_id' => $foodId,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return response()->json([
            'id' => $rating->id,
            'rating' => $rating->rating,
            'comment' => $rating->comment,
            'food_id' => $food->food_id,
            'user_id' => $rating->user_id,
        ], 201);
    }

    public function index($foodId)
    {
        $food = Food::findOrFail($foodId);

        $ratings = Rating::with('user')
            ->where('food_id', $food->id)
            ->latest()
            ->get();

        return response()->json(
            $ratings->map(function ($rating) {
                return [
                    'id' => $rating->id,
                    'rating' => $rating->rating,
                    'comment' => $rating->comment,
                    'user' => [
                        'id' => $rating->user->id,
                        'name' => $rating->user->name,
                        ],
                    'created_at' => $rating->created_at?->format('Y-m-d H:i:s'),
                ];
            })
        );
    }

    public function destroy($foodId)
    {
        $rating = Rating::where('food_id', $foodId)
         ->where('user_id', Auth::id())
         ->firstOrFail();

        $rating->delete();

        return response()->json([
            'message' => 'Rating deleted successfully'
        ]);
    }
}
