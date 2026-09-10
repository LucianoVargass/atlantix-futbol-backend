<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\Player;

class TournamentPlayer extends Model
{
    protected $fillable = [
        'tournament_id',
        'division_id',
        'team_id',
        'player_id',
        'status',
        'shirt_number',
        'rules_accepted',
        'documentation_status',
        'goals',
        'yellow_cards',
        'red_cards',
        'appearances',
    ];

    protected $casts = [
        'rules_accepted' => 'boolean',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
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
