<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\MatchLineup;
use App\Models\Player;

class MatchLineupPlayer extends Model
{
    protected $fillable = [
        'match_lineup_id',
        'player_id',
        'position',
        'shirt_number',
        'is_starter',
    ];

    protected $casts = [
        'is_starter' => 'boolean',
    ];

    public function lineup(): BelongsTo
    {
        return $this->belongsTo(MatchLineup::class, 'match_lineup_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
