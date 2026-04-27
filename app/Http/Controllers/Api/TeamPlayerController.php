<?php

namespace App\Http\Controllers\Api;

use App\Models\TeamPlayer;

class TeamPlayerController extends BaseApiController
{
    protected string $modelClass = TeamPlayer::class;

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'team_id' => 'required|integer',
            'player_id' => 'required|integer',
            'status' => 'nullable|string|max:50',
            'joined_at' => 'nullable|date',
            'left_at' => 'nullable|date',
        ];
    }
}
