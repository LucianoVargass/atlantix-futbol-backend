<?php

namespace App\Http\Controllers\Api;

use App\Models\Tournament;
use App\Models\Matchday;
use App\Models\FootballMatch;
use App\Models\TeamTournamentRegistration;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TournamentController extends BaseApiController
{
    use AuthorizesRequests;

    protected string $modelClass = Tournament::class;

    public function store(Request $request)
    {
        $this->authorize('create', Tournament::class);
        $data = $this->validateRequest($request, false);
        $user = $request->user();
        if ($user && $user->role === 'tournament_admin') {
            $data['admin_user_id'] = $user->id;
        }

        $record = Tournament::create($data);
        if ($user && $user->role === 'tournament_admin') {
            $record->admins()->syncWithoutDetaching([$user->id]);
        }

        return response()->json($record, 201);
    }

    public function update(Request $request, string $id)
    {
        $record = Tournament::findOrFail($id);
        $this->authorize('update', $record);
        $data = $this->validateRequest($request, true);
        $record->fill($data);
        $record->save();

        return response()->json($record);
    }

    public function destroy(string $id)
    {
        $record = Tournament::findOrFail($id);
        $this->authorize('delete', $record);
        $record->delete();

        return response()->json(['deleted' => true]);
    }

    public function generateFixture(Request $request, Tournament $tournament)
    {
        $this->authorize('update', $tournament);

        // División objetivo: la pedida, o la única del torneo.
        $divisionId = $request->integer('division_id') ?: null;
        if (!$divisionId) {
            $divisions = $tournament->divisions()->pluck('id');
            if ($divisions->count() === 1) {
                $divisionId = (int) $divisions->first();
            } elseif ($divisions->count() > 1) {
                return response()->json(['message' => 'El torneo tiene varias divisiones — indicá division_id.'], 422);
            }
        }

        $matchQuery = FootballMatch::where('tournament_id', $tournament->id);
        if ($divisionId) {
            $matchQuery->where('division_id', $divisionId);
        }
        if ($matchQuery->exists()) {
            return response()->json(['message' => 'Esta división ya tiene partidos creados.'], 409);
        }

        $regQuery = TeamTournamentRegistration::where('tournament_id', $tournament->id)
            ->where('subscription_status', 'confirmed');
        if ($divisionId) {
            $regQuery->where('division_id', $divisionId);
        }
        $registrations = $regQuery->pluck('team_id')->map(fn ($id) => (int) $id)->values();

        if ($registrations->isEmpty()) {
            return response()->json(['message' => 'No hay equipos confirmados para generar fixture.'], 422);
        }

        $teamIds = $registrations->all();
        if (count($teamIds) < 2) {
            return response()->json(['message' => 'Se necesitan al menos 2 equipos para generar fixture.'], 422);
        }

        $competitionFormat = $tournament->competition_format ?? [];
        $leagueRounds = (int) ($competitionFormat['leagueRounds'] ?? 1);
        if ($leagueRounds < 1) {
            $leagueRounds = 1;
        }

        $hasBye = count($teamIds) % 2 === 1;
        if ($hasBye) {
            $teamIds[] = null;
        }

        $totalTeams = count($teamIds);
        $roundsPerLeg = $totalTeams - 1;

        DB::transaction(function () use ($tournament, $teamIds, $totalTeams, $roundsPerLeg, $leagueRounds, $divisionId) {
            $del = Matchday::where('tournament_id', $tournament->id);
            if ($divisionId) {
                $del->where('division_id', $divisionId);
            }
            $del->delete();

            $teams = $teamIds;
            $matchdayNumber = 1;

            for ($leg = 1; $leg <= $leagueRounds; $leg++) {
                for ($round = 0; $round < $roundsPerLeg; $round++) {
                    $matchday = Matchday::create([
                        'tournament_id' => $tournament->id,
                        'division_id' => $divisionId,
                        'number' => $matchdayNumber,
                        'name' => 'Fecha ' . $matchdayNumber,
                        'status' => 'scheduled',
                    ]);

                    for ($i = 0; $i < $totalTeams / 2; $i++) {
                        $home = $teams[$i];
                        $away = $teams[$totalTeams - 1 - $i];

                        if (!$home || !$away) {
                            continue;
                        }

                        $swap = $leg % 2 === 0;
                        $homeId = $swap ? $away : $home;
                        $awayId = $swap ? $home : $away;

                        FootballMatch::create([
                            'tournament_id' => $tournament->id,
                            'division_id' => $divisionId,
                            'matchday_id' => $matchday->id,
                            'home_team_id' => $homeId,
                            'away_team_id' => $awayId,
                            'status' => 'scheduled',
                        ]);
                    }

                    $matchdayNumber++;

                    $fixed = array_shift($teams);
                    $last = array_pop($teams);
                    array_unshift($teams, $fixed);
                    array_splice($teams, 1, 0, [$last]);
                }
            }
        });

        return response()->json(['created' => true]);
    }

    protected function query(Request $request): Builder
    {
        $query = parent::query($request);
        $user = $request->user();

        if (!$user) {
            return $query;
        }

        if ($user->role === 'tournament_admin') {
            $query->where(function (Builder $inner) use ($user) {
                $inner->where('admin_user_id', $user->id)
                    ->orWhereHas('admins', function (Builder $admins) use ($user) {
                        $admins->where('users.id', $user->id);
                    });
            });
        }

        return $query;
    }

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'admin_user_id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'format' => 'nullable|string|max:50',
            'sport_type' => 'nullable|string|max:50',
            'players_per_team' => 'nullable|integer|min:0',
            'max_teams' => 'nullable|integer|min:0',
            'registered_teams' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'rules_version' => 'nullable|string|max:50',
            'rules' => 'nullable|array',
            'competition_format' => 'nullable|array',
            'registration_fee' => 'nullable|numeric|min:0',
            'matchday_fee' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'venue' => 'nullable|string|max:255',
        ];
    }
}
