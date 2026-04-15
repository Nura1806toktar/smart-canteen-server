<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Food;

class Rating extends Model
{
    protected $casts = [
        'rating' => 'integer'
    ];
    protected $fillable = [
        'food_id',
        'user_id',
        'rating',
        'comment',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}
