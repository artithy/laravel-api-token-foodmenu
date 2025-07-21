<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodModel extends Model
{
    protected $table = 'food';

    protected $fillable = [
        'name',
        'description',
        'price',
        'cuisine_id',
        'discount_price',
        'vat_percentage',
        'stock_quantity',
        'status',
        'image',

    ];

    public function cuisine()
    {
        return $this->belongsTo(CuisineModel::class, 'cuisine_id', 'id');
    }
}
