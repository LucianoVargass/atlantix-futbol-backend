<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalRole extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];
}
