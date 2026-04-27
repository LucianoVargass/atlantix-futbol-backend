<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TournamentPlayer;
use App\Models\PlayerDocument;
use App\Models\TeamTournamentRegistration;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TournamentReviewController extends Controller
{
    use AuthorizesRequests;

    public function approvePlayer(Request $request, Tournament $tournament, TournamentPlayer $tournamentPlayer)
    {
        $this->authorize('update', $tournament);

        if ($tournamentPlayer->tournament_id !== $tournament->id) {
            return response()->json(['message' => 'Jugador no pertenece al torneo.'], 422);
        }

        $tournamentPlayer->update([
            'status' => 'confirmed',
            'documentation_status' => 'approved',
        ]);

        return response()->json(['data' => $tournamentPlayer]);
    }

    public function rejectPlayer(Request $request, Tournament $tournament, TournamentPlayer $tournamentPlayer)
    {
        $this->authorize('update', $tournament);

        if ($tournamentPlayer->tournament_id !== $tournament->id) {
            return response()->json(['message' => 'Jugador no pertenece al torneo.'], 422);
        }

        $tournamentPlayer->update([
            'status' => 'rejected',
            'documentation_status' => 'rejected',
        ]);

        return response()->json(['data' => $tournamentPlayer]);
    }

    public function approveTeam(Request $request, Tournament $tournament, TeamTournamentRegistration $registration)
    {
        $this->authorize('update', $tournament);

        if ($registration->tournament_id !== $tournament->id) {
            return response()->json(['message' => 'Equipo no pertenece al torneo.'], 422);
        }

        $registration->update([
            'subscription_status' => 'confirmed',
        ]);

        return response()->json(['data' => $registration]);
    }

    public function rejectTeam(Request $request, Tournament $tournament, TeamTournamentRegistration $registration)
    {
        $this->authorize('update', $tournament);

        if ($registration->tournament_id !== $tournament->id) {
            return response()->json(['message' => 'Equipo no pertenece al torneo.'], 422);
        }

        $registration->update([
            'subscription_status' => 'rejected',
        ]);

        return response()->json(['data' => $registration]);
    }
}
