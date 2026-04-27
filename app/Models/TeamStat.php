<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Team;
use App\Models\Tournament;

class TeamStat extends Model
{
    protected $fillable = [
        'team_id',
        'tournament_id',
        'played',
        'won',
        'draw',
        'lost',
        'goals_for',
        'goals_against',
        'points',
        'yellow_cards',
        'red_cards',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }
}
