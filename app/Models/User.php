<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\TournamentAdmin;
use App\Models\TeamAdmin;
use App\Models\Payment;
use App\Models\Team;
use App\Models\Tournament;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'phone',
    'avatar_url',
    'mp_access_token',
    'mp_public_key',
    'mp_mode',
    'mp_notification_url',
])]
#[Hidden(['password', 'remember_token', 'mp_access_token', 'mp_public_key'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $appends = [
        'team_admin_for',
        'tournament_admin_for',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tournamentAdmins(): HasMany
    {
        return $this->hasMany(TournamentAdmin::class);
    }

    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'tournament_admins');
    }

    public function ownedTournaments(): HasMany
    {
        return $this->hasMany(Tournament::class, 'admin_user_id');
    }

    public function teamAdmins(): HasMany
    {
        return $this->hasMany(TeamAdmin::class);
    }

    public function getTeamAdminForAttribute(): array
    {
        return $this->teamAdmins()->pluck('team_id')->unique()->values()->all();
    }

    public function getTournamentAdminForAttribute(): array
    {
        $adminIds = $this->tournamentAdmins()->pluck('tournament_id');
        $owned = Tournament::where('admin_user_id', $this->id)->pluck('id');

        return $adminIds->merge($owned)->unique()->values()->all();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'payer_user_id');
    }
}
