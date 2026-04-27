<?php

namespace App\Http\Controllers\Api;

use App\Models\Payment;

class PaymentController extends BaseApiController
{
    protected string $modelClass = Payment::class;

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'tournament_id' => 'nullable|integer',
            'team_id' => 'nullable|integer',
            'payer_user_id' => 'nullable|integer',
            'type' => 'nullable|string|max:50',
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'status' => 'nullable|string|max:50',
            'method' => 'nullable|string|max:50',
            'paid_at' => 'nullable|date',
            'reference' => 'nullable|string|max:255',
            'external_id' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
        ];
    }
}
