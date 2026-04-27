<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\FootballMatch;
use App\Models\Team;
use App\Models\MatchLineupPlayer;

class MatchLineup extends Model
{
    protected $fillable = [
        'match_id',
        'team_id',
        'formation',
        'notes',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(FootballMatch::class, 'match_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(MatchLineupPlayer::class);
    }
}
