<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\User;
use App\Models\PaymentLog;
use App\Models\PaymentReceipt;

class Payment extends Model
{
    protected $fillable = [
        'tournament_id',
        'team_id',
        'payer_user_id',
        'type',
        'amount',
        'currency',
        'status',
        'method',
        'paid_at',
        'reference',
        'external_id',
        'metadata',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'metadata' => 'array',
        'amount' => 'decimal:2',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_user_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(PaymentReceipt::class);
    }
}
