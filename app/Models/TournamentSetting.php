<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tournament;

class TournamentSetting extends Model
{
    protected $fillable = [
        'tournament_id',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }
}
