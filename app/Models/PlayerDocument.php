<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Player;

class PlayerDocument extends Model
{
    protected $fillable = [
        'player_id',
        'document_type',
        'document_number',
        'front_url',
        'back_url',
        'status',
        'verified_at',
        'expires_at',
        'verified_by',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /** Documento verificado y todavía vigente. */
    public function isValid(): bool
    {
        return $this->status === 'approved'
            && $this->verified_at !== null
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }
}
