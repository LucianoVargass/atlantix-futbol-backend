<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Player;
use App\Models\Tournament;
use App\Models\Team;

class PlayerStat extends Model
{
    protected $fillable = [
        'player_id',
        'tournament_id',
        'team_id',
        'goals',
        'assists',
        'yellow_cards',
        'red_cards',
        'appearances',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
