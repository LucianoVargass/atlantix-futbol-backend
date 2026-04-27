<?php

namespace App\Http\Controllers\Api;

use App\Models\MatchEvent;
use App\Models\FootballMatch;
use Illuminate\Http\Request;

class MatchEventController extends BaseApiController
{
    protected string $modelClass = MatchEvent::class;

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'match_id' => 'required|integer|exists:football_matches,id',
            'team_id' => 'required|integer|exists:teams,id',
            'type' => 'required|string|max:50',
            'minute' => 'nullable|integer|min:0',
            'player_id' => 'nullable|integer|exists:players,id',
            'player_name' => 'nullable|string|max:255',
            'period' => 'nullable|string|max:50',
            'assists' => 'nullable|string|max:255',
            'related_player_id' => 'nullable|integer|exists:players,id',
        ];
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request, false);
        $user = $request->user();

        if ($user && $user->role === 'referee') {
            $match = FootballMatch::find($data['match_id']);
            $referee = $match?->referee;
            $userMatches = $referee && $referee->user_id && $user->id
                ? (int) $referee->user_id === (int) $user->id
                : false;
            $emailMatches = $referee && $referee->email && $user->email
                ? strtolower($referee->email) === strtolower($user->email)
                : false;

            if (!$userMatches && !$emailMatches) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        }

        $record = MatchEvent::create($data);

        return response()->json($record, 201);
    }
}
