<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerTermAcceptance extends Model
{
    protected $fillable = [
        'player_id',
        'tournament_id',
        'tournament_term_id',
        'accepted_at',
        'ip_address',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(TournamentTerm::class, 'tournament_term_id');
    }
}
