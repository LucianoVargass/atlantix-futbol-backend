<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\Player;

class Sanction extends Model
{
    protected $fillable = [
        'tournament_id',
        'team_id',
        'player_id',
        'type',
        'reason',
        'matches',
        'fine_amount',
        'fine_currency',
        'paid_amount',
        'paid_at',
        'cleared_at',
        'cleared_reason',
        'clear_on_payment',
        'is_permanent',
        'rule_key',
        'source_match_id',
        'status',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'paid_at' => 'datetime',
        'cleared_at' => 'datetime',
        'clear_on_payment' => 'boolean',
        'is_permanent' => 'boolean',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
