<?php

namespace App\Http\Controllers\Api;

use App\Models\TournamentGroup;

class TournamentGroupController extends BaseApiController
{
    protected string $modelClass = TournamentGroup::class;

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'tournament_id' => 'required|integer',
            'phase_id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
        ];
    }
}
