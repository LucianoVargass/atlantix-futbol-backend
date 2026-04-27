<?php

namespace App\Http\Controllers\Api;

use App\Models\TournamentAdmin;

class TournamentAdminController extends BaseApiController
{
    protected string $modelClass = TournamentAdmin::class;

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'tournament_id' => 'required|integer',
            'user_id' => 'required|integer',
        ];
    }
}
