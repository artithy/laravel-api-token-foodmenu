<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'token';

    protected $fillable = [
        'token',
        'user_id',
        'is_active',
    ];
}
