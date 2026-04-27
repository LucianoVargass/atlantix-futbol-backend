<?php

namespace App\Http\Controllers\Api;

use App\Models\FootballMatch;
use App\Models\TeamStat;
use App\Models\PlayerStat;
use App\Models\Player;
use App\Models\Sanction;
use App\Models\TournamentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FootballMatchController extends BaseApiController
{
    protected string $modelClass = FootballMatch::class;

    protected function canRefereeManage(Request $request, FootballMatch $match): bool
    {
        $user = $request->user();
        if (!$user || $user->role !== 'referee') {
            return true;
        }

        $referee = $match->referee;
        if (!$referee) {
            return false;
        }

        if ($referee->user_id && $user->id) {
            return (int) $referee->user_id === (int) $user->id;
        }

        if (!$referee->email || !$user->email) {
            return false;
        }

        return strtolower($referee->email) === strtolower($user->email);
    }

    protected function rules(bool $isUpdate = false): array
    {
        return [
            'tournament_id' => 'required|integer',
            'matchday_id' => 'nullable|integer',
            'home_team_id' => 'nullable|integer',
            'away_team_id' => 'nullable|integer',
            'referee_id' => 'nullable|integer',
            'player_of_match_id' => 'nullable|integer|exists:players,id',
            'player_of_match_name' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'period' => 'nullable|string|max:50',
            'scheduled_at' => 'nullable|date',
            'started_at' => 'nullable|date',
            'ended_at' => 'nullable|date',
            'venue' => 'nullable|string|max:255',
            'score_home' => 'nullable|integer|min:0',
            'score_away' => 'nullable|integer|min:0',
        ];
    }

    public function update(Request $request, string $id)
    {
        $record = FootballMatch::findOrFail($id);
        if (!$this->canRefereeManage($request, $record)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $data = $this->validateRequest($request, true);
        $record->fill($data);
        $record->save();

        if (array_key_exists('player_of_match_id', $data) && $data['player_of_match_id']) {
            Player::where('id', $data['player_of_match_id'])->update(['is_figura' => true]);
        }

        return response()->json($record);
    }

    public function finalize(Request $request, FootballMatch $footballMatch)
    {
        if (!$this->canRefereeManage($request, $footballMatch)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $data = $request->validate([
            'score_home' => 'required|integer|min:0',
            'score_away' => 'required|integer|min:0',
        ]);

        $alreadyFinalized = $footballMatch->status === 'finished' && $footballMatch->ended_at !== null;

        return DB::transaction(function () use ($footballMatch, $data, $alreadyFinalized) {
            $footballMatch->fill([
                'status' => 'finished',
                'score_home' => $data['score_home'],
                'score_away' => $data['score_away'],
                'ended_at' => $footballMatch->ended_at ?? now(),
            ]);
            $footballMatch->save();

            if (!$alreadyFinalized && $footballMatch->tournament_id && $footballMatch->home_team_id && $footballMatch->away_team_id) {
                $homeStat = TeamStat::firstOrCreate(
                    [
                        'team_id' => $footballMatch->home_team_id,
                        'tournament_id' => $footballMatch->tournament_id,
                    ],
                    [
                        'played' => 0,
                        'won' => 0,
                        'draw' => 0,
                        'lost' => 0,
                        'goals_for' => 0,
                        'goals_against' => 0,
                        'points' => 0,
                        'yellow_cards' => 0,
                        'red_cards' => 0,
                    ]
                );

                $awayStat = TeamStat::firstOrCreate(
                    [
                        'team_id' => $footballMatch->away_team_id,
                        'tournament_id' => $footballMatch->tournament_id,
                    ],
                    [
                        'played' => 0,
                        'won' => 0,
                        'draw' => 0,
                        'lost' => 0,
                        'goals_for' => 0,
                        'goals_against' => 0,
                        'points' => 0,
                        'yellow_cards' => 0,
                        'red_cards' => 0,
                    ]
                );

                $homeGoals = (int) $data['score_home'];
                $awayGoals = (int) $data['score_away'];

                $homeStat->increment('played');
                $awayStat->increment('played');

                $homeStat->increment('goals_for', $homeGoals);
                $homeStat->increment('goals_against', $awayGoals);
                $awayStat->increment('goals_for', $awayGoals);
                $awayStat->increment('goals_against', $homeGoals);

                if ($homeGoals > $awayGoals) {
                    $homeStat->increment('won');
                    $homeStat->increment('points', 3);
                    $awayStat->increment('lost');
                } elseif ($homeGoals < $awayGoals) {
                    $awayStat->increment('won');
                    $awayStat->increment('points', 3);
                    $homeStat->increment('lost');
                } else {
                    $homeStat->increment('draw');
                    $awayStat->increment('draw');
                    $homeStat->increment('points', 1);
                    $awayStat->increment('points', 1);
                }

                $events = $footballMatch->events()->get();
                $discipline = TournamentSetting::where('tournament_id', $footballMatch->tournament_id)
                    ->value('settings') ?? [];
                $disciplineRules = $discipline['discipline'] ?? [];
                $redRules = $disciplineRules['red'] ?? [];
                $yellowRules = $disciplineRules['yellow'] ?? [];
                $homeYellow = 0;
                $homeRed = 0;
                $awayYellow = 0;
                $awayRed = 0;
                $redPlayers = [];
                $yellowPlayers = [];
                foreach ($events as $event) {
                    if ($event->team_id === $footballMatch->home_team_id) {
                        if ($event->type === 'yellow_card') $homeYellow += 1;
                        if ($event->type === 'red_card') $homeRed += 1;
                    }
                    if ($event->team_id === $footballMatch->away_team_id) {
                        if ($event->type === 'yellow_card') $awayYellow += 1;
                        if ($event->type === 'red_card') $awayRed += 1;
                    }
                    if (!$event->player_id) continue;
                    if ($event->type === 'red_card') {
                        $redPlayers[$event->player_id] = $event->team_id;
                    }
                    if ($event->type === 'yellow_card') {
                        $yellowPlayers[$event->player_id] = $event->team_id;
                    }
                    $playerStat = PlayerStat::firstOrCreate(
                        [
                            'player_id' => $event->player_id,
                            'tournament_id' => $footballMatch->tournament_id,
                            'team_id' => $event->team_id,
                        ],
                        [
                            'goals' => 0,
                            'assists' => 0,
                            'yellow_cards' => 0,
                            'red_cards' => 0,
                            'appearances' => 0,
                        ]
                    );

                    if ($event->type === 'goal') {
                        $playerStat->increment('goals');
                        if ($event->related_player_id) {
                            $assistStat = PlayerStat::firstOrCreate(
                                [
                                    'player_id' => $event->related_player_id,
                                    'tournament_id' => $footballMatch->tournament_id,
                                    'team_id' => $event->team_id,
                                ],
                                [
                                    'goals' => 0,
                                    'assists' => 0,
                                    'yellow_cards' => 0,
                                    'red_cards' => 0,
                                    'appearances' => 0,
                                ]
                            );
                            $assistStat->increment('assists');
                        }
                    }

                    if ($event->type === 'yellow_card') {
                        $playerStat->increment('yellow_cards');
                    }

                    if ($event->type === 'red_card') {
                        $playerStat->increment('red_cards');
                    }

                    if ($event->type === 'yellow_card' && !empty($yellowRules['limit'])) {
                        $limit = (int) $yellowRules['limit'];
                        if ($limit > 0 && ($playerStat->yellow_cards % $limit) === 0) {
                            $ruleKey = 'yellow_limit_' . $limit;
                            Sanction::firstOrCreate(
                                [
                                    'tournament_id' => $footballMatch->tournament_id,
                                    'player_id' => $event->player_id,
                                    'rule_key' => $ruleKey,
                                    'source_match_id' => $footballMatch->id,
                                ],
                                [
                                    'team_id' => $event->team_id,
                                    'type' => 'yellow_accumulation',
                                    'reason' => $yellowRules['reason'] ?? 'Acumulación de amarillas',
                                    'matches' => (int) ($yellowRules['suspension_matches'] ?? 1),
                                    'fine_amount' => $yellowRules['fine_amount'] ?? null,
                                    'fine_currency' => $yellowRules['fine_currency'] ?? 'ARS',
                                    'clear_on_payment' => (bool) ($yellowRules['clear_on_payment'] ?? false),
                                    'is_permanent' => (bool) ($yellowRules['is_permanent'] ?? false),
                                    'status' => 'active',
                                    'issued_at' => now(),
                                ]
                            );
                        }
                    }
                }

                if (!empty($redRules['suspension_matches'])) {
                    foreach ($redPlayers as $playerId => $teamId) {
                        Sanction::firstOrCreate(
                            [
                                'tournament_id' => $footballMatch->tournament_id,
                                'player_id' => $playerId,
                                'rule_key' => 'red_card',
                                'source_match_id' => $footballMatch->id,
                            ],
                            [
                                'team_id' => $teamId,
                                'type' => 'red_card',
                                'reason' => $redRules['reason'] ?? 'Tarjeta roja',
                                'matches' => (int) ($redRules['suspension_matches'] ?? 1),
                                'fine_amount' => $redRules['fine_amount'] ?? null,
                                'fine_currency' => $redRules['fine_currency'] ?? 'ARS',
                                'clear_on_payment' => (bool) ($redRules['clear_on_payment'] ?? false),
                                'is_permanent' => (bool) ($redRules['is_permanent'] ?? false),
                                'status' => 'active',
                                'issued_at' => now(),
                            ]
                        );
                    }
                }

                if ($homeYellow > 0) {
                    $homeStat->increment('yellow_cards', $homeYellow);
                }
                if ($homeRed > 0) {
                    $homeStat->increment('red_cards', $homeRed);
                }
                if ($awayYellow > 0) {
                    $awayStat->increment('yellow_cards', $awayYellow);
                }
                if ($awayRed > 0) {
                    $awayStat->increment('red_cards', $awayRed);
                }
            }

            return response()->json($footballMatch->fresh());
        });
    }
}
