<?php

namespace App\Http\Controllers\Api;

use App\Models\TeamTournamentRegistration;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class TeamTournamentRegistrationController extends BaseApiController
{
    protected string $modelClass = TeamTournamentRegistration::class;

    public function store(Request $request)
    {
        $data = $this->validateRequest($request, false);
        $user = $request->user();

        $team = Team::findOrFail($data['team_id']);
        $tournament = Tournament::findOrFail($data['tournament_id']);

        if ($tournament->status !== 'registration_open') {
            return response()->json(['message' => 'Inscripciones cerradas.'], 422);
        }

        $already = TeamTournamentRegistration::where('team_id', $team->id)
            ->where('tournament_id', $tournament->id)
            ->exists();
        if ($already) {
            return response()->json(['message' => 'El equipo ya está inscripto.'], 409);
        }

        if ($tournament->max_teams > 0) {
            $count = TeamTournamentRegistration::where('tournament_id', $tournament->id)->count();
            if ($count >= $tournament->max_teams) {
                return response()->json(['message' => 'No hay cupos disponibles.'], 422);
            }
        }

        if ($user) {
            if ($user->role === 'team_admin') {
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

        $record = TeamTournamentRegistration::create($data);
        $tournament->update([
            'registered_teams' => TeamTournamentRegistration::where('tournament_id', $tournament->id)->count(),
        ]);

        return response()->json($record, 201);
    }

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'team_id' => 'required|integer',
            'tournament_id' => 'required|integer',
            'subscription_status' => 'nullable|string|max:50',
            'subscription_date' => 'nullable|date',
            'payment_status' => 'nullable|string|max:50',
            'payment_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'payment_reference' => 'nullable|string|max:255',
            'payment_date' => 'nullable|date',
            'rules_accepted' => 'nullable|boolean',
            'rules_accepted_version' => 'nullable|string|max:50',
            'rules_accepted_at' => 'nullable|date',
        ];
    }
}
