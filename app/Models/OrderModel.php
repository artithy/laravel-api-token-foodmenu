<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'cart_id',
        'total_price',
        'customer_name',
        'customer_phone',
        'customer_address',
        'payment_status',
        'order_details',
        'status',
    ];


    protected $casts = [
        'order_details' => 'array',
    ];

    public function cart()
    {
        return $this->belongsTo(CartModel::class, 'cart_id');
    }

    public static function booted()
    {
        static::addGlobalScope('orderAsc', function ($query) {
            $query->orderBy('created_at', 'asc');
        });
    }
}
