<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Tournament;
use App\Models\RulesVersion;

class RuleAcceptance extends Model
{
    protected $fillable = [
        'user_id',
        'tournament_id',
        'rules_version_id',
        'accepted_at',
        'ip_address',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function rulesVersion(): BelongsTo
    {
        return $this->belongsTo(RulesVersion::class);
    }
}
