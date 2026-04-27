<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Team;
use App\Models\PlayerStat;

class Player extends Model
{
    protected $fillable = [
        'user_id',
        'team_id',
        'name',
        'surname',
        'email',
        'phone',
        'birth_date',
        'gender',
        'position',
        'shirt_number',
        'profile_photo',
        'goals',
        'yellow_cards',
        'red_cards',
        'is_figura',
        'appearances',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_figura' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function stats(): HasMany
    {
        return $this->hasMany(PlayerStat::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(\App\Models\PlayerDocument::class);
    }
}
