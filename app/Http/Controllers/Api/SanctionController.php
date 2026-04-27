<?php

namespace App\Http\Controllers\Api;

use App\Models\Sanction;

class SanctionController extends BaseApiController
{
    protected string $modelClass = Sanction::class;

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'tournament_id' => 'required|integer|exists:tournaments,id',
            'team_id' => 'nullable|integer|exists:teams,id',
            'player_id' => 'nullable|integer|exists:players,id',
            'type' => 'required|string|max:50',
            'reason' => 'nullable|string',
            'matches' => 'nullable|integer|min:0',
            'fine_amount' => 'nullable|numeric|min:0',
            'fine_currency' => 'nullable|string|max:10',
            'paid_amount' => 'nullable|numeric|min:0',
            'paid_at' => 'nullable|date',
            'cleared_at' => 'nullable|date',
            'cleared_reason' => 'nullable|string',
            'clear_on_payment' => 'nullable|boolean',
            'is_permanent' => 'nullable|boolean',
            'rule_key' => 'nullable|string|max:100',
            'source_match_id' => 'nullable|integer|exists:football_matches,id',
            'status' => 'nullable|string|max:50',
            'issued_at' => 'nullable|date',
        ];
    }
}
