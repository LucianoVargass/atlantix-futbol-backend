<?php

namespace App\Http\Controllers\Api;

use App\Models\TeamAdmin;

class TeamAdminController extends BaseApiController
{
    protected string $modelClass = TeamAdmin::class;

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'team_id' => 'required|integer',
            'user_id' => 'required|integer',
        ];
    }
}
