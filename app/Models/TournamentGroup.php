<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Tournament;
use App\Models\TournamentPhase;
use App\Models\PhaseTeam;

class TournamentGroup extends Model
{
    protected $fillable = [
        'tournament_id',
        'phase_id',
        'name',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(TournamentPhase::class, 'phase_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(PhaseTeam::class, 'group_id');
    }
}
