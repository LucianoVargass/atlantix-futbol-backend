<?php

namespace App\Http\Controllers\Api;

use App\Models\RulesVersion;

class RulesVersionController extends BaseApiController
{
    protected string $modelClass = RulesVersion::class;

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'tournament_id' => 'required|integer',
            'version' => 'nullable|integer|min:1',
            'rules' => 'nullable|array',
            'created_by' => 'nullable|integer',
        ];
    }
}
