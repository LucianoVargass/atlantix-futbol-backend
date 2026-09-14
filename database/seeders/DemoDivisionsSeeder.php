<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Tournament;
use App\Models\Division;
use App\Models\Team;
use App\Models\TeamAdmin;
use App\Models\TeamTournamentRegistration;
use App\Models\Player;
use App\Models\TeamPlayer;
use App\Models\TournamentPlayer;
use App\Models\Matchday;
use App\Models\FootballMatch;
use App\Models\MatchEvent;
use App\Models\MatchLineup;
use App\Models\MatchLineupPlayer;
use App\Models\TeamStat;
use App\Models\PlayerStat;
use App\Models\TournamentNews;
use App\Models\Sanction;
use App\Models\Referee;
use App\Models\User;

/**
 * Prueba de verdad el modelo de divisiones: parte "Liga + Playoff Futbol 7"
 * en una división Masculina (la que ya jugó DemoResultsSeeder) y crea una
 * división Femenina nueva de punta a punta — equipos, planteles, fixture,
 * resultados. Además agrega noticias y un par de sanciones activas.
 *
 * Idempotente: si el torneo ya tiene divisiones, no hace nada.
 */
class DemoDivisionsSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(20260914);

        $tournament = Tournament::where('name', 'Liga + Playoff Futbol 7')->first();
        if (!$tournament) {
            return;
        }
        // Ya corrió antes (fresh install o sobre un servidor ya deployado): no duplicar.
        if ($tournament->divisions()->where('name', 'Femenina')->exists()) {
            return;
        }

        // Si el backfill multi-tenant ya corrió alguna vez, el torneo tiene una
        // división "General" con todo el fixture masculino ya asignado —
        // la renombramos en vez de crear una nueva.
        $masculina = $tournament->divisions()->where('name', 'General')->first();
        if ($masculina) {
            $masculina->update(['name' => 'Masculina', 'gender' => 'male', 'order' => 1]);
        } else {
            $masculina = Division::create([
                'tournament_id' => $tournament->id,
                'name' => 'Masculina',
                'gender' => 'male',
                'order' => 1,
            ]);
            // Instalación fresca: lo que generó DemoResultsSeeder todavía no tiene división.
            foreach (['team_tournament_registrations', 'tournament_players', 'matchdays', 'football_matches'] as $table) {
                DB::table($table)
                    ->where('tournament_id', $tournament->id)
                    ->whereNull('division_id')
                    ->update(['division_id' => $masculina->id]);
            }
        }

        $femenina = Division::create([
            'tournament_id' => $tournament->id,
            'name' => 'Femenina',
            'gender' => 'female',
            'order' => 2,
        ]);

        $this->buildFemaleDivision($tournament, $femenina);
        $this->news($tournament);
        $this->sanctions($tournament, $masculina);
    }

    private function buildFemaleDivision(Tournament $tournament, Division $division): void
    {
        $now = Carbon::now();
        $teamNames = ['Atlántix Femenino Norte', 'Atlántix Femenino Sur', 'Atlántix Femenino Este', 'Atlántix Femenino Oeste'];
        $positions = ['Arquero', 'Defensor', 'Mediocampista', 'Delantero'];
        $referees = Referee::pluck('id')->all();

        $teamIds = [];
        $rosters = [];

        foreach ($teamNames as $index => $name) {
            $shortName = strtoupper(Str::substr(Str::slug($name, ''), -3));

            $team = Team::updateOrCreate(
                ['name' => $name],
                [
                    'short_name' => $shortName,
                    'city' => 'Buenos Aires',
                    'founded_year' => 2021 + $index,
                    'contact_email' => 'femenino' . ($index + 1) . '@atlantix.com',
                    'color' => null,
                ],
            );
            $teamIds[] = $team->id;

            $delegado = User::updateOrCreate(
                ['email' => 'delegada.femenino.' . ($index + 1) . '@atlantix.com'],
                ['name' => 'Delegada ' . $name, 'password' => Hash::make('secret123'), 'role' => 'team_admin'],
            );
            $team->update(['owner_user_id' => $delegado->id]);
            TeamAdmin::updateOrCreate(['team_id' => $team->id, 'user_id' => $delegado->id], []);

            TeamTournamentRegistration::updateOrCreate(
                ['team_id' => $team->id, 'tournament_id' => $tournament->id],
                [
                    'division_id' => $division->id,
                    'subscription_status' => 'confirmed',
                    'subscription_date' => $now,
                    'payment_status' => 'paid',
                    'payment_amount' => $tournament->registration_fee,
                    'payment_method' => 'transferencia',
                    'rules_accepted' => true,
                    'rules_accepted_version' => $tournament->rules_version,
                    'rules_accepted_at' => $now,
                ],
            );

            TeamStat::updateOrCreate(
                ['team_id' => $team->id, 'tournament_id' => $tournament->id],
                ['played' => 0, 'won' => 0, 'draw' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0, 'yellow_cards' => 0, 'red_cards' => 0],
            );

            $roster = [];
            for ($i = 1; $i <= 12; $i++) {
                $playerUser = User::updateOrCreate(
                    ['email' => 'jugadora' . ($index + 1) . '_' . $i . '@atlantix.com'],
                    ['name' => 'Jugadora ' . $i . ' ' . $shortName, 'password' => Hash::make('secret123'), 'role' => 'player'],
                );

                $player = Player::updateOrCreate(
                    ['team_id' => $team->id, 'shirt_number' => $i],
                    [
                        'user_id' => $playerUser->id,
                        'name' => 'Jugadora' . $i,
                        'surname' => $shortName,
                        'gender' => 'female',
                        'birth_date' => $now->copy()->subYears(18 + ($i % 12))->toDateString(),
                        'position' => $positions[$i % count($positions)],
                    ],
                );

                TeamPlayer::updateOrCreate(
                    ['team_id' => $team->id, 'player_id' => $player->id],
                    ['status' => 'active', 'joined_at' => $now],
                );

                $tp = TournamentPlayer::updateOrCreate(
                    ['tournament_id' => $tournament->id, 'player_id' => $player->id],
                    [
                        'division_id' => $division->id,
                        'team_id' => $team->id,
                        'status' => 'confirmed',
                        'shirt_number' => $i,
                        'rules_accepted' => true,
                        'documentation_status' => 'approved',
                    ],
                );

                $roster[] = (object) ['player_id' => $player->id, 'shirt_number' => $i];
            }
            $rosters[$team->id] = $roster;
        }

        $schedule = $this->roundRobin($teamIds);
        $playRounds = min(2, count($schedule));

        foreach ($schedule as $roundIdx => $pairs) {
            $number = $roundIdx + 1;
            $isPlayed = $number <= $playRounds;
            $offsetDays = ($number - $playRounds) * 7 + 1;

            $matchday = Matchday::create([
                'tournament_id' => $tournament->id,
                'division_id' => $division->id,
                'number' => $number,
                'name' => "Fecha {$number}",
                'status' => $isPlayed ? 'finished' : 'scheduled',
                'start_date' => $now->copy()->addDays($offsetDays)->toDateString(),
            ]);

            foreach ($pairs as $slot => [$home, $away]) {
                if (!$home || !$away) {
                    continue;
                }
                $kickoff = $now->copy()->addDays($offsetDays)->setTime(18 + ($slot % 2), 30);
                // Solo designamos árbitro a la fecha jugada + la próxima (mezcla realista).
                $refId = ($isPlayed || $number === $playRounds + 1) && $referees
                    ? $referees[($number + $slot) % count($referees)]
                    : null;

                $match = FootballMatch::create([
                    'tournament_id' => $tournament->id,
                    'division_id' => $division->id,
                    'matchday_id' => $matchday->id,
                    'home_team_id' => $home,
                    'away_team_id' => $away,
                    'referee_id' => $refId,
                    'status' => $isPlayed ? 'finished' : 'scheduled',
                    'scheduled_at' => $kickoff,
                    'ended_at' => $isPlayed ? $kickoff->copy()->addMinutes(90) : null,
                    'venue' => $tournament->venue ?? 'Cancha Principal',
                    'score_home' => 0,
                    'score_away' => 0,
                ]);

                foreach ([$home, $away] as $teamId) {
                    $roster = $rosters[$teamId] ?? [];
                    if (empty($roster)) {
                        continue;
                    }
                    $lineup = MatchLineup::create(['match_id' => $match->id, 'team_id' => $teamId, 'formation' => '4-3-3']);
                    foreach ($roster as $idx => $rp) {
                        MatchLineupPlayer::create([
                            'match_lineup_id' => $lineup->id,
                            'player_id' => $rp->player_id,
                            'shirt_number' => $rp->shirt_number,
                            'is_starter' => $idx < 11,
                        ]);
                    }
                }

                if ($isPlayed) {
                    $this->playMatch($tournament, $match, $rosters);
                }
            }
        }
    }

    private function roundRobin(array $teams): array
    {
        $list = $teams;
        if (count($list) % 2 === 1) {
            $list[] = null;
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

    private function playMatch(Tournament $tournament, FootballMatch $match, array $rosters): void
    {
        $goals = [$match->home_team_id => mt_rand(0, 3), $match->away_team_id => mt_rand(0, 3)];
        $match->update(['score_home' => $goals[$match->home_team_id], 'score_away' => $goals[$match->away_team_id]]);

        foreach ($goals as $teamId => $count) {
            $roster = $rosters[$teamId] ?? [];
            if (empty($roster)) {
                continue;
            }
            for ($g = 0; $g < $count; $g++) {
                $rp = $roster[mt_rand(0, count($roster) - 1)];
                MatchEvent::create(['match_id' => $match->id, 'team_id' => $teamId, 'player_id' => $rp->player_id, 'type' => 'goal', 'minute' => mt_rand(3, 89)]);
                PlayerStat::firstOrCreate(
                    ['player_id' => $rp->player_id, 'tournament_id' => $tournament->id, 'team_id' => $teamId],
                    ['goals' => 0, 'assists' => 0, 'yellow_cards' => 0, 'red_cards' => 0, 'appearances' => 0],
                )->increment('goals');
                Player::where('id', $rp->player_id)->increment('goals');
            }
        }

        $home = $match->home_team_id;
        $away = $match->away_team_id;
        $hg = $goals[$home];
        $ag = $goals[$away];
        $ensure = fn ($teamId) => TeamStat::firstOrCreate(
            ['team_id' => $teamId, 'tournament_id' => $tournament->id],
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
            $hs->increment('points');
            $as->increment('points');
        }
    }

    private function news(Tournament $tournament): void
    {
        $now = Carbon::now();
        $items = [
            ['title' => 'Arrancó la Femenina', 'body' => 'Cuatro equipos debutan en la nueva división femenina del torneo, con fixture propio y la misma tabla de premios.', 'days' => 6],
            ['title' => 'Fecha confirmada', 'body' => 'Se confirmaron horarios y sedes para la próxima fecha. Recordá presentarte 15 minutos antes con la documentación al día.', 'days' => 2],
        ];
        foreach ($items as $n) {
            TournamentNews::updateOrCreate(
                ['tournament_id' => $tournament->id, 'title' => $n['title']],
                ['body' => $n['body'], 'is_published' => true, 'published_at' => $now->copy()->subDays($n['days'])],
            );
        }
    }

    private function sanctions(Tournament $tournament, Division $division): void
    {
        $now = Carbon::now();
        $players = PlayerStat::where('tournament_id', $tournament->id)
            ->where('yellow_cards', '>=', 2)
            ->orderByDesc('yellow_cards')
            ->limit(2)
            ->get();

        foreach ($players as $stat) {
            Sanction::updateOrCreate(
                ['tournament_id' => $tournament->id, 'player_id' => $stat->player_id, 'rule_key' => 'yellow_limit_2'],
                [
                    'team_id' => $stat->team_id,
                    'type' => 'yellow_accumulation',
                    'reason' => 'Acumulación de 2 tarjetas amarillas',
                    'matches' => 1,
                    'status' => 'active',
                    'issued_at' => $now,
                ],
            );
        }
    }
}
