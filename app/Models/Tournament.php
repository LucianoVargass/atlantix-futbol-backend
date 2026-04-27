<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Models\TeamTournamentRegistration;
use App\Models\Matchday;
use App\Models\FootballMatch;
use App\Models\TournamentNews;

class Tournament extends Model
{
    protected $fillable = [
        'admin_user_id',
        'name',
        'description',
        'image_url',
        'status',
        'format',
        'sport_type',
        'players_per_team',
        'max_teams',
        'registered_teams',
        'start_date',
        'end_date',
        'rules_version',
        'rules',
        'competition_format',
        'registration_fee',
        'matchday_fee',
        'currency',
        'contact_email',
        'contact_phone',
        'venue',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rules' => 'array',
        'competition_format' => 'array',
        'registration_fee' => 'decimal:2',
        'matchday_fee' => 'decimal:2',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tournament_admins');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(TeamTournamentRegistration::class);
    }

    public function matchdays(): HasMany
    {
        return $this->hasMany(Matchday::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(FootballMatch::class);
    }

    public function news(): HasMany
    {
        return $this->hasMany(TournamentNews::class);
    }

    public function terms(): HasMany
    {
        return $this->hasMany(TournamentTerm::class);
    }
}
