<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referee extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'level',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
