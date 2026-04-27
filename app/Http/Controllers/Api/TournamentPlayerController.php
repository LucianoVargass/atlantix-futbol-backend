<?php

namespace App\Http\Controllers\Api;

use App\Models\TournamentPlayer;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\TournamentSetting;
use App\Models\TeamTournamentRegistration;
use Illuminate\Http\Request;

class TournamentPlayerController extends BaseApiController
{
    protected string $modelClass = TournamentPlayer::class;

    public function store(Request $request)
    {
        $data = $this->validateRequest($request, false);
        $user = $request->user();

        $tournament = Tournament::findOrFail($data['tournament_id']);
        $team = isset($data['team_id']) ? Team::find($data['team_id']) : null;

        if ($user) {
            if ($user->role === 'team_admin' && $team) {
                $isAdmin = $team->admins()->where('users.id', $user->id)->exists();
                if (!$isAdmin) {
                    return response()->json(['message' => 'No autorizado para este equipo.'], 403);
                }
            }
            if ($user->role === 'tournament_admin') {
                $isOwner = $tournament->admin_user_id === $user->id
                    || $tournament->admins()->where('users.id', $user->id)->exists();
                if (!$isOwner) {
                    return response()->json(['message' => 'No autorizado para este torneo.'], 403);
                }
            }
        }

        $settings = TournamentSetting::where('tournament_id', $tournament->id)->value('settings') ?? [];
        $maxPlayers = $settings['max_players_per_team'] ?? null;
        $requireDocs = Boolean($settings['require_docs'] ?? false);
        $requireTerms = Boolean($settings['require_terms'] ?? false);
        $requirePayment = Boolean($settings['require_payment'] ?? false);

        if ($requirePayment && $team) {
            $registration = TeamTournamentRegistration::where('team_id', $team->id)
                ->where('tournament_id', $tournament->id)
                ->first();
            if ($registration && $registration->payment_status !== 'paid') {
                return response()->json(['message' => 'Pago pendiente para habilitar jugadores.'], 422);
            }
        }
        if ($maxPlayers && $team) {
            $count = TournamentPlayer::where('tournament_id', $tournament->id)
                ->where('team_id', $team->id)
                ->count();
            if ($count >= (int) $maxPlayers) {
                return response()->json(['message' => 'Se alcanzó el límite de jugadores por equipo.'], 422);
            }
        }

        $record = TournamentPlayer::create(array_merge($data, [
            'documentation_status' => $requireDocs ? 'pending' : ($data['documentation_status'] ?? 'pending'),
            'rules_accepted' => $requireTerms ? false : ($data['rules_accepted'] ?? false),
        ]));

        return response()->json($record, 201);
    }

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'tournament_id' => 'required|integer',
            'team_id' => 'nullable|integer',
            'player_id' => 'required|integer',
            'status' => 'nullable|string|max:50',
            'shirt_number' => 'nullable|integer|min:0',
            'rules_accepted' => 'nullable|boolean',
            'documentation_status' => 'nullable|string|max:50',
            'goals' => 'nullable|integer|min:0',
            'yellow_cards' => 'nullable|integer|min:0',
            'red_cards' => 'nullable|integer|min:0',
            'appearances' => 'nullable|integer|min:0',
        ];
    }
}
