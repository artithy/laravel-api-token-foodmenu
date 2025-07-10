<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuisineModel extends Model
{
    use HasFactory;

    protected $table = 'cuisine';


    protected $fillable = [
        'name'
    ];
}
