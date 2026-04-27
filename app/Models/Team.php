<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Models\Player;
use App\Models\TeamTournamentRegistration;
use App\Models\TeamStat;

class Team extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'city',
        'founded_year',
        'contact_email',
        'contact_phone',
        'color',
        'logo_url',
    ];

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(TeamTournamentRegistration::class);
    }

    public function stats(): HasMany
    {
        return $this->hasMany(TeamStat::class);
    }

    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_admins');
    }
}
