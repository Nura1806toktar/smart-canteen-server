<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Rating;

class Food extends Model
{
    protected $table = 'foods';
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'image',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function orderitems()
    {
        return $this->hasMany(OrderItem::class);
    }
     public function ratings(){
        return $this->hasMany(Rating::class);
     }
}
