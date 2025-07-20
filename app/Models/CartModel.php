<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartModel extends Model
{
    protected $table = 'carts';

    protected $fillable = [
        'cart_token',
        'status',
    ];
}
