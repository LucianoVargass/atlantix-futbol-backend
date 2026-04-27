<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlayerTermAcceptance;
use App\Models\Tournament;
use App\Models\TournamentTerm;
use Illuminate\Http\Request;

class PlayerTermAcceptanceController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'player_id' => ['required', 'integer'],
            'tournament_id' => ['required', 'integer'],
            'tournament_term_id' => ['required', 'integer'],
            'accepted_at' => ['nullable', 'date'],
        ]);

        $term = TournamentTerm::where('id', $data['tournament_term_id'])
            ->where('tournament_id', $data['tournament_id'])
            ->firstOrFail();

        $acceptance = PlayerTermAcceptance::updateOrCreate(
            [
                'player_id' => $data['player_id'],
                'tournament_id' => $data['tournament_id'],
                'tournament_term_id' => $term->id,
            ],
            [
                'accepted_at' => $data['accepted_at'] ?? now(),
                'ip_address' => $request->ip(),
            ]
        );

        return response()->json(['data' => $acceptance], 201);
    }
}
