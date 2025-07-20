<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItemModel extends Model
{
    protected $table = 'cart_items';

    protected $fillable = [
        'cart_id',
        'food_id',
        'quantity',
    ];
}
