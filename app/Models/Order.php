<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\OrderItem;



class Order extends Model
{
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }
    public function isPreparing()
    {
        return $this->status === self::STATUS_PREPARING;
    }

    public function isReady()
    {
        return $this->status === self::STATUS_READY;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public static function statuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PREPARING,
            self::STATUS_READY,

            self::STATUS_CANCELLED,
        ];
    }

    protected $casts = [
        'total_price' => 'float',
        'pickup_time' => 'datetime',
    ];
    protected $fillable = [
        'user_id',
        'status',
        'total_price',
        'pickup_time'
    ];
    public const STATUS_PENDING = 'pending';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_READY = 'ready';
    public const STATUS_CANCELLED = 'cancelled';
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
