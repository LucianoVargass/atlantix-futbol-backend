<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Tournament;
use App\Models\Matchday;
use App\Models\Team;
use App\Models\Referee;
use App\Models\MatchEvent;

class FootballMatch extends Model
{
    protected $fillable = [
        'tournament_id',
        'matchday_id',
        'home_team_id',
        'away_team_id',
        'referee_id',
        'player_of_match_id',
        'player_of_match_name',
        'status',
        'period',
        'scheduled_at',
        'started_at',
        'ended_at',
        'venue',
        'score_home',
        'score_away',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function matchday(): BelongsTo
    {
        return $this->belongsTo(Matchday::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function referee(): BelongsTo
    {
        return $this->belongsTo(Referee::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(MatchEvent::class, 'match_id');
    }
}
