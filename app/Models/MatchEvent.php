<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\FootballMatch;
use App\Models\Team;
use App\Models\Player;

class MatchEvent extends Model
{
    protected $fillable = [
        'match_id',
        'team_id',
        'player_id',
        'related_player_id',
        'type',
        'minute',
        'detail',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(FootballMatch::class, 'match_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function relatedPlayer(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'related_player_id');
    }
}
