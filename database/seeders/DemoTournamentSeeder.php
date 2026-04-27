<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Tournament;
use App\Models\TournamentAdmin;
use App\Models\TournamentSetting;
use App\Models\TournamentTerm;
use App\Models\RulesVersion;
use App\Models\Referee;
use App\Models\Team;
use App\Models\TeamAdmin;
use App\Models\TeamTournamentRegistration;
use App\Models\TeamStat;
use App\Models\Player;
use App\Models\PlayerDocument;
use App\Models\TeamPlayer;
use App\Models\TournamentPlayer;

class DemoTournamentSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $playerProfileImages = [
            'players/1/MQOO9mDBtQ2u0EXoU0Af7xWMyD5tXsDweHv3SgFk.jpg',
            'players/1/yRef4qhbuJja7rZFO08xrxdoSObfAJWbjIzWC5mS.jpg',
            'EJEMPLOS/JUGADORES/MESSI.jpeg',
            'EJEMPLOS/JUGADORES/DIBU.jpeg',
            'EJEMPLOS/JUGADORES/NEYMAR.jpeg',
            'EJEMPLOS/JUGADORES/SUAREZ.jpeg',
            'EJEMPLOS/JUGADORES/JULIAN.jpeg',
            'EJEMPLOS/JUGADORES/PUPPY.jpeg',
            'EJEMPLOS/JUGADORES/PITY.webp',
            'EJEMPLOS/JUGADORES/DIEGOTE.jpg',
        ];

        $playerDocumentImages = [
            'players/1/documents/5IL5a0seStwNaBJwvK30PuxHnrvX2znsDBVAbz9x.jpg',
            'players/1/documents/693xyeQtsMGJQxeR2NiBQdgLXMtQdTibuiOJkI57.jpg',
            'players/1/documents/T1eicais2G6tDdxzIs26mAUWub704A9h0AtIBks6.jpg',
            'players/1/documents/yH863zexyDApNJOPo40G7vOf1frn63LaK2JgHsE2.jpg',
        ];

        $teamLogos = [
            'teams/1/sn5gUm1vLLRjN0b4YXnzOtgpwXHRt5TNnVoGujqJ.png',
            'EJEMPLOS/EQUIPOS/BARCA.png',
            'EJEMPLOS/EQUIPOS/INTER.png',
            'EJEMPLOS/EQUIPOS/MILA.png',
            'EJEMPLOS/EQUIPOS/REALMADRID.png',
        ];

        $tournamentBanners = [
            'tournaments/1/4hD6g7AmdIrSpILgnTJjDuEx1URvHcL23cCEgyTR.png',
            'tournaments/1/AVg8h8H0UlqYNvyVHaeINFPOblsDDJUrBMw7zxPg.png',
            'tournaments/1/EjX6GtehN8Y0HiWxDKFRGFYNPCUabKDuXNLI3Cm7.png',
            'EJEMPLOS/TORNEO/TORNEO.png',
        ];

        // Crear varios organizadores
        $organizers = [];

        for ($o = 1; $o <= 3; $o++) {
            $organizers[$o] = User::updateOrCreate(
                ['email' => "organizador$o@atlantix.com"],
                [
                    'name' => "Organizador $o",
                    'password' => Hash::make('secret123'),
                    'role' => 'tournament_admin',
                    'phone' => null,
                ]
            );
        }

        // Crear varios torneos con diferentes formatos
        $tournamentFormats = [
            [
                'name' => 'Liga Futbol 7',
                'format' => 'league',
                'competition_format' => [
                    'type' => 'league',
                    'leagueRounds' => 1,
                    'playoffEnabled' => false,
                ],
            ],
            [
                'name' => 'Liga + Playoff Futbol 7',
                'format' => 'league_playoffs',
                'competition_format' => [
                    'type' => 'league_playoffs',
                    'leagueRounds' => 1,
                    'playoffEnabled' => true,
                    'playoffTeamsCount' => 4,
                    'playoffFormat' => 'single',
                ],
            ],
            [
                'name' => 'Copa Futbol 7',
                'format' => 'knockout',
                'competition_format' => [
                    'type' => 'knockout',
                    'playoffEnabled' => false,
                ],
            ],
        ];

        $tournaments = [];

        foreach ($tournamentFormats as $i => $format) {
            $admin = $organizers[($i % count($organizers)) + 1];
            $banner = $tournamentBanners[$i % count($tournamentBanners)];

            $tournaments[$i] = Tournament::updateOrCreate(
                ['name' => $format['name']],
                [
                    'admin_user_id' => $admin->id,
                    'description' => $format['name'] . ' demo',
                    'status' => 'active',
                    'format' => $format['format'],
                    'sport_type' => 'futbol7',
                    'players_per_team' => 7,
                    'max_teams' => 8,
                    'registered_teams' => 8,
                    'start_date' => $now->copy()->addDays($i * 2)->startOfDay(),
                    'end_date' => $now->copy()->addMonths(2 + $i)->startOfDay(),
                    'rules_version' => '1',
                    'rules' => [
                        'max_substitutes' => 5,
                        'max_players_per_match' => 7,
                        'match_duration' => 40,
                        'halftime_duration' => 10,
                        'yellow_card_limit' => 2,
                        'terms_and_conditions' => 'Al inscribirse, los equipos aceptan el reglamento y el fair play.',
                        'disciplinary_policy' => 'Se aplican sanciones por acumulación de tarjetas y conducta antideportiva.',
                        'registration_policy' => 'La inscripción por equipo es de $100.000 ARS.',
                    ],
                    'competition_format' => $format['competition_format'],
                    'registration_fee' => 100000,
                    'matchday_fee' => 2500,
                    'currency' => 'ARS',
                    'contact_email' => 'organizacion@atlantix.com',
                    'contact_phone' => null,
                    'venue' => 'Cancha Principal',
                    'image_url' => $banner,
                ]
            );

            TournamentAdmin::updateOrCreate(
                [
                    'tournament_id' => $tournaments[$i]->id,
                    'user_id' => $admin->id,
                ],
                []
            );

            TournamentSetting::updateOrCreate(
                ['tournament_id' => $tournaments[$i]->id],
                [
                    'settings' => [
                        'prizes' => [
                            [
                                'position' => 1,
                                'label' => 'Campeón',
                                'amount' => 120000,
                                'description' => 'Copa + premio en efectivo',
                            ],
                            [
                                'position' => 2,
                                'label' => 'Subcampeón',
                                'amount' => 60000,
                                'description' => 'Medallas + premio en efectivo',
                            ],
                            [
                                'position' => 3,
                                'label' => 'Tercer puesto',
                                'amount' => 30000,
                                'description' => 'Medallas',
                            ],
                        ],
                    ],
                ]
            );

            TournamentTerm::updateOrCreate(
                [
                    'tournament_id' => $tournaments[$i]->id,
                    'title' => 'Estatutos y Reglamento',
                ],
                [
                    'body' => 'Los equipos deben aceptar los estatutos, presentar documentación, abonar la inscripción de $100.000 ARS y respetar el reglamento disciplinario.',
                    'required' => true,
                    'version' => 1,
                ]
            );

            RulesVersion::updateOrCreate(
                [
                    'tournament_id' => $tournaments[$i]->id,
                    'version' => 1,
                ],
                [
                    'rules' => [
                        'max_substitutes' => 5,
                        'max_players_per_match' => 7,
                        'match_duration' => 40,
                        'halftime_duration' => 10,
                        'yellow_card_limit' => 2,
                        'disciplinary_policy' => 'Sanciones por tarjetas acumuladas y conducta antideportiva.',
                        'registration_policy' => 'La inscripción por equipo es de $100.000 ARS.',
                    ],
                    'created_by' => $admin->id,
                ]
            );
        }

        // Crear equipos y asignarlos a torneos de forma masiva
        $teamNames = [
            'Atlántix Rojo',
            'Atlántix Azul',
            'Atlántix Verde',
            'Atlántix Negro',
            'Atlántix Oro',
            'Atlántix Plata',
            'Atlántix Blanco',
            'Atlántix Naranja',
            'Atlántix Violeta',
            'Atlántix Celeste',
            'Atlántix Marrón',
            'Atlántix Rosa',
            'Atlántix Gris',
            'Atlántix Lima',
        ];

        $positions = [
            'Arquero',
            'Defensor',
            'Mediocampista',
            'Delantero',
        ];

        foreach ($teamNames as $index => $teamName) {
            $shortName = strtoupper(Str::substr(Str::slug($teamName, ''), 0, 3));
            $logo = $teamLogos[$index % count($teamLogos)];

            $team = Team::updateOrCreate(
                ['name' => $teamName],
                [
                    'short_name' => $shortName,
                    'city' => 'Buenos Aires',
                    'founded_year' => 2020 + $index,
                    'contact_email' => 'equipo' . ($index + 1) . '@atlantix.com',
                    'contact_phone' => '11' . str_pad((string) ($index + 1), 8, '0', STR_PAD_LEFT),
                    'color' => null,
                    'logo_url' => $logo,
                ]
            );

            foreach ($tournaments as $tIndex => $tournament) {
                if (($index + $tIndex) % 2 !== 0) {
                    continue;
                }

                $teamAdminEmail = $index === 0 && $tIndex === 0
                    ? 'administradorequipo@atlantix.com'
                    : 'admin.' . ($index + 1) . '.' . ($tIndex + 1) . '@atlantix.com';

                $teamAdminPassword = $teamAdminEmail === 'administradorequipo@atlantix.com'
                    ? '12345678'
                    : 'secret123';

                $teamAdminUser = User::updateOrCreate(
                    ['email' => $teamAdminEmail],
                    [
                        'name' => 'Admin ' . $teamName . ' T' . ($tIndex + 1),
                        'password' => Hash::make($teamAdminPassword),
                        'role' => 'team_admin',
                        'phone' => null,
                    ]
                );

                TeamAdmin::updateOrCreate(
                    [
                        'team_id' => $team->id,
                        'user_id' => $teamAdminUser->id,
                    ],
                    []
                );

                TeamTournamentRegistration::updateOrCreate(
                    [
                        'team_id' => $team->id,
                        'tournament_id' => $tournament->id,
                    ],
                    [
                        'subscription_status' => 'confirmed',
                        'subscription_date' => $now,
                        'payment_status' => 'paid',
                        'payment_amount' => 100000,
                        'payment_method' => 'cash',
                        'rules_accepted' => $index % 3 !== 0,
                        'rules_accepted_version' => '1',
                        'rules_accepted_at' => $index % 3 !== 0 ? $now : null,
                    ]
                );

                TeamStat::updateOrCreate(
                    [
                        'team_id' => $team->id,
                        'tournament_id' => $tournament->id,
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

                for ($i = 1; $i <= 12; $i++) {
                    $docStatus = $i % 4 === 0 ? 'pending' : 'approved';
                    $rulesAccepted = $i % 5 !== 0;
                    $profileImage = $playerProfileImages[($i + $index) % count($playerProfileImages)];

                    $playerUser = User::updateOrCreate(
                        [
                            'email' => 'jugador' . ($index + 1) . '_' . $i . '.t' . ($tIndex + 1) . '@atlantix.com',
                        ],
                        [
                            'name' => 'Jugador ' . $i . ' ' . $shortName . ' T' . ($tIndex + 1),
                            'password' => Hash::make('secret123'),
                            'role' => 'player',
                            'phone' => null,
                        ]
                    );

                    $player = Player::updateOrCreate(
                        [
                            'team_id' => $team->id,
                            'shirt_number' => $i,
                        ],
                        [
                            'user_id' => $playerUser->id,
                            'name' => 'Jugador' . $i,
                            'surname' => $shortName,
                            'email' => null,
                            'phone' => null,
                            'birth_date' => null,
                            'gender' => 'Masculino',
                            'position' => $positions[$i % count($positions)],
                        ]
                    );

if ($i % 3 === 0) {
    PlayerDocument::updateOrCreate(
        [
            'player_id' => $player->id,
        ],
        [
            'document_type' => 'dni',
            'document_number' => (string) rand(30000000, 45000000),
            'front_url' => $playerDocumentImages[array_rand($playerDocumentImages)],
            'back_url' => $playerDocumentImages[array_rand($playerDocumentImages)],
            'status' => $docStatus,
        ]
    );
}

                    TeamPlayer::updateOrCreate(
                        [
                            'team_id' => $team->id,
                            'player_id' => $player->id,
                        ],
                        [
                            'status' => 'active',
                            'joined_at' => $now,
                        ]
                    );

                    TournamentPlayer::updateOrCreate(
                        [
                            'tournament_id' => $tournament->id,
                            'player_id' => $player->id,
                        ],
                        [
                            'team_id' => $team->id,
                            'status' => 'confirmed',
                            'shirt_number' => $i,
                            'rules_accepted' => $rulesAccepted,
                            'documentation_status' => $docStatus,
                        ]
                    );
                }
            }
        }

        $referees = [
            [
                'name' => 'Árbitro Juan Pérez',
                'email' => 'ref1@atlantix.com',
                'phone' => '111111111',
                'level' => 'A',
            ],
            [
                'name' => 'Árbitro Lucas Gómez',
                'email' => 'ref2@atlantix.com',
                'phone' => '222222222',
                'level' => 'B',
            ],
            [
                'name' => 'Árbitro Martín Díaz',
                'email' => 'ref3@atlantix.com',
                'phone' => '333333333',
                'level' => 'C',
            ],
        ];

        foreach ($referees as $referee) {
            $refereeUser = User::updateOrCreate(
                ['email' => $referee['email']],
                [
                    'name' => $referee['name'],
                    'password' => Hash::make('secret123'),
                    'role' => 'referee',
                    'phone' => $referee['phone'],
                ]
            );

            Referee::updateOrCreate(
                ['email' => $referee['email']],
                [
                    'user_id' => $refereeUser->id,
                    'name' => $referee['name'],
                    'phone' => $referee['phone'],
                    'level' => $referee['level'],
                    'active' => true,
                ]
            );
        }

        // Segundo bloque demo adicional, corregido para que no use $tournament suelto
        $extraTeamNames = [
            'Atlántix Rojo Reserva',
            'Atlántix Azul Reserva',
            'Atlántix Verde Reserva',
            'Atlántix Negro Reserva',
            'Atlántix Oro Reserva',
            'Atlántix Plata Reserva',
            'Atlántix Blanco Reserva',
        ];

        $mainTournament = $tournaments[0];

        foreach ($extraTeamNames as $index => $teamName) {
            $shortName = strtoupper(Str::substr(Str::slug($teamName, ''), 0, 3));

            $team = Team::updateOrCreate(
                ['name' => $teamName],
                [
                    'short_name' => $shortName,
                    'city' => 'Buenos Aires',
                    'founded_year' => 2020 + $index,
                    'contact_email' => 'reserva' . ($index + 1) . '@atlantix.com',
                    'contact_phone' => '11' . str_pad((string) ($index + 20), 8, '0', STR_PAD_LEFT),
                    'color' => null,
                    'logo_url' => $teamLogos[$index % count($teamLogos)],
                ]
            );

            $teamAdminUser = User::updateOrCreate(
                ['email' => 'admin.reserva.' . ($index + 1) . '@atlantix.com'],
                [
                    'name' => 'Admin ' . $teamName,
                    'password' => Hash::make('secret123'),
                    'role' => 'team_admin',
                    'phone' => null,
                ]
            );

            TeamAdmin::updateOrCreate(
                [
                    'team_id' => $team->id,
                    'user_id' => $teamAdminUser->id,
                ],
                []
            );

            TeamTournamentRegistration::updateOrCreate(
                [
                    'team_id' => $team->id,
                    'tournament_id' => $mainTournament->id,
                ],
                [
                    'subscription_status' => 'confirmed',
                    'subscription_date' => $now,
                    'payment_status' => 'paid',
                    'payment_amount' => 100000,
                    'payment_method' => 'cash',
                    'rules_accepted' => $index % 3 !== 0,
                    'rules_accepted_version' => '1',
                    'rules_accepted_at' => $index % 3 !== 0 ? $now : null,
                ]
            );

            TeamStat::updateOrCreate(
                [
                    'team_id' => $team->id,
                    'tournament_id' => $mainTournament->id,
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

            for ($i = 1; $i <= 12; $i++) {
                $docStatus = $i % 4 === 0 ? 'pending' : 'approved';
                $rulesAccepted = $i % 5 !== 0;

                $playerUser = User::updateOrCreate(
                    ['email' => 'jugador.reserva.' . ($index + 1) . '_' . $i . '@atlantix.com'],
                    [
                        'name' => 'Jugador Reserva ' . $i . ' ' . $shortName,
                        'password' => Hash::make('secret123'),
                        'role' => 'player',
                        'phone' => null,
                    ]
                );

                $player = Player::updateOrCreate(
                    [
                        'team_id' => $team->id,
                        'shirt_number' => $i,
                    ],
                    [
                        'user_id' => $playerUser->id,
                        'name' => 'JugadorReserva' . $i,
                        'surname' => $shortName,
                        'email' => null,
                        'phone' => null,
                        'birth_date' => null,
                        'gender' => 'Masculino',
                        'position' => $positions[$i % count($positions)],
                    ]
                );

                TeamPlayer::updateOrCreate(
                    [
                        'team_id' => $team->id,
                        'player_id' => $player->id,
                    ],
                    [
                        'status' => 'active',
                        'joined_at' => $now,
                    ]
                );

                TournamentPlayer::updateOrCreate(
                    [
                        'tournament_id' => $mainTournament->id,
                        'player_id' => $player->id,
                    ],
                    [
                        'team_id' => $team->id,
                        'status' => 'confirmed',
                        'shirt_number' => $i,
                        'rules_accepted' => $rulesAccepted,
                        'documentation_status' => $docStatus,
                    ]
                );
            }
        }
    }
}