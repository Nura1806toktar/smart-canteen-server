<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Rating;

class Food extends Model
{
    protected $casts = [
        'price' => 'float',
        'available' => 'boolean',
    ];
    protected $table = 'foods';
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'image',
        'available',
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
     public function averageRating(){
        return $this->ratings()->avg('rating');
     }
     public function ratingsCount(){
        return $this->ratings()->count();
     }

     public function getAverageRatingAttribute(){
        return round($this->ratings()->avg('rating'), 2);
     }

     public function getRatingCountAttribute()
     {
         return $this->ratings()->count();
     }
}
