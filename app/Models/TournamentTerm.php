<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentTerm extends Model
{
    protected $fillable = [
        'tournament_id',
        'title',
        'body',
        'required',
        'version',
    ];

    protected $casts = [
        'required' => 'boolean',
        'version' => 'integer',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }
}
