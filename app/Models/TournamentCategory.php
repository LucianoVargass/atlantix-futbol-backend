<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tournament;

class TournamentCategory extends Model
{
    protected $fillable = [
        'tournament_id',
        'name',
        'position_from',
        'position_to',
        'format',
        'prize',
        'order',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }
}
