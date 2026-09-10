<?php

namespace App\Http\Controllers\Api;

use App\Models\Player;
use App\Models\PlayerStat;
use App\Models\Sanction;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PlayerController extends BaseApiController
{
    use AuthorizesRequests;

    protected string $modelClass = Player::class;

    /**
     * Estadísticas de un jugador: totales globales + desglose por torneo.
     * Público (vista de hincha / perfil del jugador).
     */
    public function stats(Request $request, Player $player)
    {
        $stats = PlayerStat::where('player_id', $player->id)
            ->with('tournament:id,name,status,format')
            ->get();

        $byTournament = $stats->map(fn ($s) => [
            'tournament_id' => $s->tournament_id,
            'tournament' => $s->tournament,
            'goals' => (int) $s->goals,
            'assists' => (int) $s->assists,
            'yellow_cards' => (int) $s->yellow_cards,
            'red_cards' => (int) $s->red_cards,
            'appearances' => (int) $s->appearances,
        ])->values();

        $totals = [
            'goals' => (int) $stats->sum('goals'),
            'assists' => (int) $stats->sum('assists'),
            'yellow_cards' => (int) $stats->sum('yellow_cards'),
            'red_cards' => (int) $stats->sum('red_cards'),
            'appearances' => (int) $stats->sum('appearances'),
            'tournaments' => $stats->pluck('tournament_id')->unique()->count(),
        ];

        // fallback a los contadores planos del jugador si todavía no hay player_stats
        if ($byTournament->isEmpty()) {
            $totals['goals'] = (int) $player->goals;
            $totals['yellow_cards'] = (int) $player->yellow_cards;
            $totals['red_cards'] = (int) $player->red_cards;
            $totals['appearances'] = (int) $player->appearances;
        }

        $sanctions = Sanction::where('player_id', $player->id)
            ->where('status', 'active')
            ->get(['id', 'type', 'reason', 'matches', 'status', 'tournament_id']);

        return response()->json([
            'data' => [
                'player' => $player->load('team:id,name,logo_url'),
                'totals' => $totals,
                'by_tournament' => $byTournament,
                'sanctions' => $sanctions,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Player::class);
        return parent::store($request);
    }

    public function update(Request $request, string $id)
    {
        $record = Player::findOrFail($id);
        $this->authorize('update', $record);
        $data = $this->validateRequest($request, true);
        $record->fill($data);
        $record->save();

        return response()->json($record);
    }

    public function destroy(string $id)
    {
        $record = Player::findOrFail($id);
        $this->authorize('delete', $record);
        $record->delete();

        return response()->json(['deleted' => true]);
    }

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'user_id' => 'nullable|integer',
            'team_id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:50',
            'shirt_number' => 'nullable|integer|min:0',
            'profile_photo' => 'nullable|url',
            'goals' => 'nullable|integer|min:0',
            'yellow_cards' => 'nullable|integer|min:0',
            'red_cards' => 'nullable|integer|min:0',
            'is_figura' => 'nullable|boolean',
            'appearances' => 'nullable|integer|min:0',
        ];
    }

    // Sobrescribimos el método show para incluir documentos solo para organizador
    public function show(Request $request, $id)
    {
        $player = Player::with('documents')->findOrFail($id);

        $user = $request->user();
        $isOrganizer = false;
        // Si el usuario es organizador de algún torneo del equipo del jugador
        if ($user && method_exists($user, 'tournaments')) {
            $teamId = $player->team_id;
            $tournaments = $user->tournaments ? $user->tournaments->pluck('id')->toArray() : [];
            // Aquí podrías necesitar ajustar la lógica según tu modelo de relación
            $isOrganizer = in_array($teamId, $tournaments);
        }

        $data = $player->toArray();
        if ($isOrganizer) {
            $data['documents'] = $player->documents->map(function($doc) {
                return [
                    'id' => $doc->id,
                    'document_type' => $doc->document_type,
                    'document_number' => $doc->document_number,
                    'front_url' => $doc->front_url,
                    'back_url' => $doc->back_url,
                    'status' => $doc->status,
                ];
            });
        } else {
            $data['documents'] = [];
        }
        return response()->json($data);
    }
}
