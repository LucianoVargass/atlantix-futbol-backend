<?php

namespace App\Http\Controllers\Api;

use App\Models\Matchday;

class MatchdayController extends BaseApiController
{
    protected string $modelClass = Matchday::class;

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'tournament_id' => 'required|integer',
            'number' => 'required|integer|min:1',
            'name' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|string|max:50',
        ];
    }
}
