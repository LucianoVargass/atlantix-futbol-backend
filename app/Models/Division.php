<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    protected $fillable = ['tournament_id', 'name', 'gender', 'born_from', 'born_to', 'order'];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function registrations(): HasMany
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

    /** ¿Un jugador es elegible para esta división? (género + banda de edad) */
    public function accepts(?string $gender, ?string $birthDate): bool
    {
        if ($this->gender !== 'mixed' && $gender && $gender !== $this->gender) {
            return false;
        }
        if (($this->born_from || $this->born_to) && $birthDate) {
            $year = (int) substr($birthDate, 0, 4);
            if ($this->born_from && $year < $this->born_from) {
                return false;
            }
            if ($this->born_to && $year > $this->born_to) {
                return false;
            }
        }
        return true;
    }
}
