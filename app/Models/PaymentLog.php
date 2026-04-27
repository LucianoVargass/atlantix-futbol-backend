<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Payment;

class PaymentLog extends Model
{
    protected $fillable = [
        'payment_id',
        'status',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
