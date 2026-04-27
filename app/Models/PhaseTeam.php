<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\TournamentPhase;
use App\Models\Team;
use App\Models\TournamentGroup;

class PhaseTeam extends Model
{
    protected $fillable = [
        'phase_id',
        'team_id',
        'group_id',
        'position',
    ];

    public function phase(): BelongsTo
    {
        return $this->belongsTo(TournamentPhase::class, 'phase_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(TournamentGroup::class, 'group_id');
    }
}
