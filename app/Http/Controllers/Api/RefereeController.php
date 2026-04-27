<?php

namespace App\Http\Controllers\Api;

use App\Models\Referee;

class RefereeController extends BaseApiController
{
    protected string $modelClass = Referee::class;

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'level' => 'nullable|string|max:100',
            'active' => 'nullable|boolean',
            'user_id' => 'nullable|integer|exists:users,id',
        ];
    }
}
