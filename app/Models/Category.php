<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Food;

class Category extends Model
{


    protected $fillable = [
        'name',
    ];
    public function foods()
    {
        return $this->hasMany(Food::class);
    }
}
