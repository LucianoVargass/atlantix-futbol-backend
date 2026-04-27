<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Tournament;
use App\Models\PhaseTeam;

class TournamentPhase extends Model
{
    protected $fillable = [
        'tournament_id',
        'name',
        'type',
        'order',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(PhaseTeam::class, 'phase_id');
    }
}
