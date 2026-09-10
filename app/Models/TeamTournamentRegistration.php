<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Team;
use App\Models\Tournament;

class TeamTournamentRegistration extends Model
{
    protected $fillable = [
        'team_id',
        'tournament_id',
        'division_id',
        'subscription_status',
        'subscription_date',
        'payment_status',
        'payment_amount',
        'payment_method',
        'payment_reference',
        'payment_date',
        'rules_accepted',
        'rules_accepted_version',
        'rules_accepted_at',
    ];

    protected $casts = [
        'subscription_date' => 'datetime',
        'payment_date' => 'datetime',
        'rules_accepted' => 'boolean',
        'rules_accepted_at' => 'datetime',
        'payment_amount' => 'decimal:2',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }
}
