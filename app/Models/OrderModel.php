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
    ];
}
