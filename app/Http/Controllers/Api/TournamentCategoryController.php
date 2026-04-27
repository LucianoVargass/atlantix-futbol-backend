<?php

namespace App\Http\Controllers\Api;

use App\Models\TournamentCategory;

class TournamentCategoryController extends BaseApiController
{
    protected string $modelClass = TournamentCategory::class;

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'tournament_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'position_from' => 'nullable|integer|min:0',
            'position_to' => 'nullable|integer|min:0',
            'format' => 'nullable|string|max:50',
            'prize' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
        ];
    }
}
