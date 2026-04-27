<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Tournament;
use App\Models\User;
use App\Models\RuleAcceptance;

class RulesVersion extends Model
{
    protected $fillable = [
        'tournament_id',
        'version',
        'rules',
        'created_by',
    ];

    protected $casts = [
        'rules' => 'array',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function acceptances(): HasMany
    {
        return $this->hasMany(RuleAcceptance::class);
    }
}
