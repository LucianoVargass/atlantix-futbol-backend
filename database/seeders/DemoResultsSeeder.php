<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Tournament;
use App\Models\TeamTournamentRegistration;
use App\Models\TournamentPlayer;
use App\Models\Matchday;
use App\Models\FootballMatch;
use App\Models\MatchEvent;
use App\Models\MatchLineup;
use App\Models\MatchLineupPlayer;
use App\Models\TeamStat;
use App\Models\PlayerStat;
use App\Models\Referee;
use App\Models\Player;

/**
 * Genera fixture + resultados demo para los torneos de liga:
 * juega las primeras fechas (con goles, tarjetas, alineaciones y stats)
 * y deja el resto programado. Así posiciones / goleadores / incidencias /
 * alineación tienen datos reales para mostrar.
 */
class DemoResultsSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(20260910);
        $now = Carbon::now();
        $referees = Referee::pluck('id')->all();

        $tournaments = Tournament::whereIn('format', ['league', 'league_playoffs'])->get();

        foreach ($tournaments as $tournament) {
            if (FootballMatch::where('tournament_id', $tournament->id)->exists()) {
                continue;
            }

            $teamIds = TeamTournamentRegistration::where('tournament_id', $tournament->id)
                ->where('subscription_status', 'confirmed')
                ->pluck('team_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            if (count($teamIds) < 2) {
                continue;
            }

            // planteles por equipo en este torneo
            $rosters = [];
            foreach ($teamIds as $teamId) {
                $rosters[$teamId] = TournamentPlayer::where('tournament_id', $tournament->id)
                    ->where('team_id', $teamId)
                    ->orderBy('shirt_number')
                    ->get(['player_id', 'shirt_number'])
                    ->all();
            }

            $schedule = $this->roundRobin($teamIds);
            $totalRounds = count($schedule);
            $playRounds = max(1, min(5, (int) floor($totalRounds * 0.6)));

            DB::transaction(function () use ($tournament, $schedule, $totalRounds, $playRounds, $rosters, $referees, $now) {
                foreach ($schedule as $roundIdx => $pairs) {
                    $number = $roundIdx + 1;
                    $isPlayed = $number <= $playRounds;
                    $offsetDays = ($number - $playRounds) * 7;

                    $matchday = Matchday::create([
                        'tournament_id' => $tournament->id,
                        'number' => $number,
                        'name' => "Fecha {$number}",
                        'status' => $isPlayed ? 'finished' : 'scheduled',
                        'start_date' => $now->copy()->addDays($offsetDays)->toDateString(),
                    ]);

                    foreach ($pairs as $slot => [$home, $away]) {
                        if (!$home || !$away) {
                            continue;
                        }
                        $kickoff = $now->copy()->addDays($offsetDays)->setTime(20 + ($slot % 2), 0);
                        $refId = $referees ? $referees[($number + $slot) % count($referees)] : null;

                        $match = FootballMatch::create([
                            'tournament_id' => $tournament->id,
                            'matchday_id' => $matchday->id,
                            'home_team_id' => $home,
                            'away_team_id' => $away,
                            'referee_id' => $refId,
                            'status' => $isPlayed ? 'finished' : 'scheduled',
                            'scheduled_at' => $kickoff,
                            'ended_at' => $isPlayed ? $kickoff->copy()->addMinutes(95) : null,
                            'venue' => $tournament->venue ?? 'Cancha Principal',
                            'score_home' => 0,
                            'score_away' => 0,
                        ]);

                        $this->buildLineups($match, $rosters, $slot);

                        if ($isPlayed) {
                            $this->playMatch($tournament, $match, $rosters);
                        }
                    }
                }
            });
        }
    }

    /** Fixture todos-contra-todos (método del círculo). */
    private function roundRobin(array $teams): array
    {
        $list = $teams;
        if (count($list) % 2 === 1) {
            $list[] = null; // bye
        }
        $n = count($list);
        $rounds = [];
        for ($r = 0; $r < $n - 1; $r++) {
            $pairs = [];
            for ($i = 0; $i < $n / 2; $i++) {
                $a = $list[$i];
                $b = $list[$n - 1 - $i];
                $pairs[] = ($r % 2 === 0) ? [$a, $b] : [$b, $a];
            }
            $rounds[] = $pairs;
            $fixed = array_shift($list);
            $last = array_pop($list);
            array_unshift($list, $fixed);
            array_splice($list, 1, 0, [$last]);
        }
        return $rounds;
    }

    private function buildLineups(FootballMatch $match, array $rosters, int $slot): void
    {
        foreach ([['team' => $match->home_team_id, 'f' => '4-3-3'], ['team' => $match->away_team_id, 'f' => $slot % 2 ? '4-4-2' : '4-3-3']] as $spec) {
            $roster = $rosters[$spec['team']] ?? [];
            if (empty($roster)) {
                continue;
            }
            $lineup = MatchLineup::create([
                'match_id' => $match->id,
                'team_id' => $spec['team'],
                'formation' => $spec['f'],
            ]);
            foreach (array_slice($roster, 0, 16) as $idx => $tp) {
                MatchLineupPlayer::create([
                    'match_lineup_id' => $lineup->id,
                    'player_id' => $tp->player_id,
                    'shirt_number' => $tp->shirt_number,
                    'is_starter' => $idx < 11,
                ]);
            }
        }
    }

    private function playMatch(Tournament $tournament, FootballMatch $match, array $rosters): void
    {
        $goals = [
            $match->home_team_id => $this->randomGoals(),
            $match->away_team_id => $this->randomGoals(),
        ];

        $match->update([
            'score_home' => $goals[$match->home_team_id],
            'score_away' => $goals[$match->away_team_id],
        ]);

        $scorers = [];
        foreach ($goals as $teamId => $count) {
            $roster = $rosters[$teamId] ?? [];
            if (empty($roster)) {
                continue;
            }
            $attackers = array_slice($roster, -6); // últimos dorsales = ofensivos
            for ($g = 0; $g < $count; $g++) {
                $tp = $attackers[mt_rand(0, count($attackers) - 1)];
                MatchEvent::create([
                    'match_id' => $match->id,
                    'team_id' => $teamId,
                    'player_id' => $tp->player_id,
                    'type' => 'goal',
                    'minute' => mt_rand(3, 92),
                ]);
                $scorers[$tp->player_id] = ($scorers[$tp->player_id] ?? 0) + 1;
                $this->bumpPlayerStat($tournament->id, $teamId, $tp->player_id, 'goals');
                Player::where('id', $tp->player_id)->increment('goals');
            }

            // 0-2 amarillas por equipo
            $cards = mt_rand(0, 2);
            for ($c = 0; $c < $cards; $c++) {
                $tp = $roster[mt_rand(0, count($roster) - 1)];
                MatchEvent::create([
                    'match_id' => $match->id,
                    'team_id' => $teamId,
                    'player_id' => $tp->player_id,
                    'type' => 'yellow_card',
                    'minute' => mt_rand(10, 90),
                ]);
                $this->bumpPlayerStat($tournament->id, $teamId, $tp->player_id, 'yellow_cards');
                Player::where('id', $tp->player_id)->increment('yellow_cards');
            }
        }

        // apariciones para los 11 titulares de cada equipo
        foreach ([$match->home_team_id, $match->away_team_id] as $teamId) {
            foreach (array_slice($rosters[$teamId] ?? [], 0, 11) as $tp) {
                $this->bumpPlayerStat($tournament->id, $teamId, $tp->player_id, 'appearances');
            }
        }

        // figura del partido: el máximo goleador
        if ($scorers) {
            arsort($scorers);
            $bestId = array_key_first($scorers);
            $best = Player::find($bestId);
            $match->update([
                'player_of_match_id' => $bestId,
                'player_of_match_name' => $best ? trim("{$best->name} {$best->surname}") : null,
            ]);
            Player::where('id', $bestId)->update(['is_figura' => true]);
        }

        $this->applyTeamStats($tournament->id, $match, $goals);
    }

    private function randomGoals(): int
    {
        $r = mt_rand(1, 100);
        return $r <= 22 ? 0 : ($r <= 55 ? 1 : ($r <= 80 ? 2 : ($r <= 94 ? 3 : 4)));
    }

    private function bumpPlayerStat(int $tournamentId, int $teamId, int $playerId, string $field, int $by = 1): void
    {
        $stat = PlayerStat::firstOrCreate(
            ['player_id' => $playerId, 'tournament_id' => $tournamentId, 'team_id' => $teamId],
            ['goals' => 0, 'assists' => 0, 'yellow_cards' => 0, 'red_cards' => 0, 'appearances' => 0],
        );
        $stat->increment($field, $by);
    }

    private function applyTeamStats(int $tournamentId, FootballMatch $match, array $goals): void
    {
        $home = $match->home_team_id;
        $away = $match->away_team_id;
        $hg = $goals[$home];
        $ag = $goals[$away];

        $ensure = fn ($teamId) => TeamStat::firstOrCreate(
            ['team_id' => $teamId, 'tournament_id' => $tournamentId],
            ['played' => 0, 'won' => 0, 'draw' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0, 'yellow_cards' => 0, 'red_cards' => 0],
        );

        $hs = $ensure($home);
        $as = $ensure($away);

        $hs->increment('played');
        $as->increment('played');
        $hs->increment('goals_for', $hg);
        $hs->increment('goals_against', $ag);
        $as->increment('goals_for', $ag);
        $as->increment('goals_against', $hg);

        if ($hg > $ag) {
            $hs->increment('won');
            $hs->increment('points', 3);
            $as->increment('lost');
        } elseif ($hg < $ag) {
            $as->increment('won');
            $as->increment('points', 3);
            $hs->increment('lost');
        } else {
            $hs->increment('draw');
            $as->increment('draw');
            $hs->increment('points', 1);
            $as->increment('points', 1);
        }

        $yellow = MatchEvent::where('match_id', $match->id)->where('type', 'yellow_card')->get()->groupBy('team_id');
        if (isset($yellow[$home])) {
            $hs->increment('yellow_cards', $yellow[$home]->count());
        }
        if (isset($yellow[$away])) {
            $as->increment('yellow_cards', $yellow[$away]->count());
        }
    }
}
