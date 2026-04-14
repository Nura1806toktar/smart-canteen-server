<?php

namespace App\Models;
use App\Models\Order;
use App\Models\Food;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $casts = [
        'price' => 'float',
    ];

    protected $fillable = [
        'order_id',
        'food_id',
        'quantity',
        'price',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}
