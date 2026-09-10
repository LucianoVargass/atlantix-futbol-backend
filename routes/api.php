<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MercadoPagoController;
use App\Http\Controllers\Api\FootballMatchController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MeController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\GlobalRoleController;
use App\Http\Controllers\Api\MatchEventController;
use App\Http\Controllers\Api\MatchLineupController;
use App\Http\Controllers\Api\MatchLineupPlayerController;
use App\Http\Controllers\Api\MatchdayController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PaymentLogController;
use App\Http\Controllers\Api\PaymentReceiptController;
use App\Http\Controllers\Api\PhaseTeamController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\PlayerDocumentController;
use App\Http\Controllers\Api\PlayerTermAcceptanceController;
use App\Http\Controllers\Api\PlayerStatController;
use App\Http\Controllers\Api\RefereeController;
use App\Http\Controllers\Api\RuleAcceptanceController;
use App\Http\Controllers\Api\RulesVersionController;
use App\Http\Controllers\Api\SanctionController;
use App\Http\Controllers\Api\TeamAdminController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TeamPlayerController;
use App\Http\Controllers\Api\TeamStatController;
use App\Http\Controllers\Api\TeamTournamentRegistrationController;
use App\Http\Controllers\Api\TournamentAdminController;
use App\Http\Controllers\Api\TournamentCategoryController;
use App\Http\Controllers\Api\TournamentController;
use App\Http\Controllers\Api\TournamentConfigController;
use App\Http\Controllers\Api\TournamentReviewController;
use App\Http\Controllers\Api\TournamentGroupController;
use App\Http\Controllers\Api\TournamentNewsController;
use App\Http\Controllers\Api\TournamentPhaseController;
use App\Http\Controllers\Api\TournamentPlayerController;
use App\Http\Controllers\Api\TournamentSettingController;
use App\Http\Controllers\Api\UserController;

$authMiddleware = class_exists(\Laravel\Sanctum\Sanctum::class) ? 'auth:sanctum' : null;

Route::post('payments/mercadopago/preference', [MercadoPagoController::class, 'createPreference']);
Route::post('payments/mercadopago/webhook', [MercadoPagoController::class, 'webhook']);

Route::post('auth/login', [AuthController::class, 'login']);
if ($authMiddleware) {
	Route::middleware($authMiddleware)->get('me', [MeController::class, 'me']);
	Route::middleware($authMiddleware)->get('me/tournaments', [MeController::class, 'tournaments']);
	Route::middleware($authMiddleware)->get('me/player', [MeController::class, 'player']);
	Route::middleware($authMiddleware)->get('me/matches', [MeController::class, 'matches']);
	Route::middleware($authMiddleware)->get('me/organizations', [MeController::class, 'organizations']);
	Route::middleware($authMiddleware)->get('me/club', [MeController::class, 'club']);
	Route::middleware($authMiddleware)->get('me/mercadopago', [MeController::class, 'mercadoPago']);
	Route::middleware($authMiddleware)->put('me/mercadopago', [MeController::class, 'updateMercadoPago']);
} else {
	Route::get('me', [MeController::class, 'me']);
	Route::get('me/tournaments', [MeController::class, 'tournaments']);
	Route::get('me/player', [MeController::class, 'player']);
	Route::get('me/matches', [MeController::class, 'matches']);
	Route::get('me/organizations', [MeController::class, 'organizations']);
	Route::get('me/club', [MeController::class, 'club']);
}

($authMiddleware ? Route::middleware($authMiddleware) : Route::middleware([]))->group(function () {
	Route::get('tournaments/{tournament}/settings', [TournamentConfigController::class, 'settings']);
	Route::put('tournaments/{tournament}/settings', [TournamentConfigController::class, 'updateSettings']);
	Route::get('tournaments/{tournament}/rules', [TournamentConfigController::class, 'rules']);
	Route::post('tournaments/{tournament}/rules', [TournamentConfigController::class, 'updateRules']);
	Route::get('tournaments/{tournament}/terms', [TournamentConfigController::class, 'terms']);
	Route::post('tournaments/{tournament}/terms', [TournamentConfigController::class, 'updateTerms']);

	Route::post('tournaments/{tournament}/teams/{registration}/approve', [TournamentReviewController::class, 'approveTeam']);
	Route::post('tournaments/{tournament}/teams/{registration}/reject', [TournamentReviewController::class, 'rejectTeam']);
	Route::post('tournaments/{tournament}/players/{tournamentPlayer}/approve', [TournamentReviewController::class, 'approvePlayer']);
	Route::post('tournaments/{tournament}/players/{tournamentPlayer}/reject', [TournamentReviewController::class, 'rejectPlayer']);
});
Route::post('uploads/teams/{team}/logo', [UploadController::class, 'teamLogo']);
Route::post('uploads/players/{player}/photo', [UploadController::class, 'playerPhoto']);
Route::post('uploads/players/{player}/documents', [UploadController::class, 'playerDocuments']);
Route::post('uploads/tournaments/{tournament}/image', [UploadController::class, 'tournamentImage']);

if ($authMiddleware) {
	Route::apiResource('users', UserController::class)->middleware($authMiddleware);
} else {
	Route::apiResource('users', UserController::class);
}

Route::get('tournaments', [TournamentController::class, 'index']);
Route::get('tournaments/{tournament}', [TournamentController::class, 'show']);
if ($authMiddleware) {
	Route::post('tournaments/{tournament}/generate-fixture', [TournamentController::class, 'generateFixture'])
		->middleware($authMiddleware);
	Route::apiResource('tournaments', TournamentController::class)
		->middleware($authMiddleware)
		->except(['index', 'show']);
} else {
	Route::post('tournaments/{tournament}/generate-fixture', [TournamentController::class, 'generateFixture']);
	Route::apiResource('tournaments', TournamentController::class)
		->except(['index', 'show']);
}

Route::get('teams', [TeamController::class, 'index']);
Route::get('teams/{team}', [TeamController::class, 'show']);
if ($authMiddleware) {
	Route::apiResource('teams', TeamController::class)
		->middleware($authMiddleware)
		->except(['index', 'show']);
} else {
	Route::apiResource('teams', TeamController::class)
		->except(['index', 'show']);
}

Route::get('players', [PlayerController::class, 'index']);
Route::get('players/{player}', [PlayerController::class, 'show']);
if ($authMiddleware) {
	Route::apiResource('players', PlayerController::class)
		->middleware($authMiddleware)
		->except(['index', 'show']);
} else {
	Route::apiResource('players', PlayerController::class)
		->except(['index', 'show']);
}

Route::get('football-matches', [FootballMatchController::class, 'index']);
Route::get('football-matches/{football_match}', [FootballMatchController::class, 'show']);

// --- Lecturas públicas para la app (hincha / perfil de jugador) ---
Route::get('players/{player}/stats', [PlayerController::class, 'stats']);
Route::get('matchdays', [MatchdayController::class, 'index']);
Route::get('matchdays/{matchday}', [MatchdayController::class, 'show']);
Route::get('match-lineups', [MatchLineupController::class, 'index']);
Route::get('match-lineups/{match_lineup}', [MatchLineupController::class, 'show']);
Route::get('match-lineup-players', [MatchLineupPlayerController::class, 'index']);
Route::get('match-events', [MatchEventController::class, 'index']);
Route::get('team-stats', [TeamStatController::class, 'index']);
Route::get('player-stats', [PlayerStatController::class, 'index']);
Route::get('tournament-news', [TournamentNewsController::class, 'index']);

if ($authMiddleware) {
	Route::post('football-matches/{football_match}/finalize', [FootballMatchController::class, 'finalize'])
		->middleware($authMiddleware);
	Route::apiResource('football-matches', FootballMatchController::class)
		->middleware($authMiddleware)
		->except(['index', 'show']);
} else {
	Route::post('football-matches/{football_match}/finalize', [FootballMatchController::class, 'finalize']);
	Route::apiResource('football-matches', FootballMatchController::class)
		->except(['index', 'show']);
}

if ($authMiddleware) {
	Route::apiResource('matches', FootballMatchController::class)->middleware($authMiddleware);
	Route::apiResource('matchdays', MatchdayController::class)->middleware($authMiddleware);
	Route::apiResource('payments', PaymentController::class)->middleware($authMiddleware);
	Route::apiResource('payment-logs', PaymentLogController::class)->middleware($authMiddleware);
	Route::apiResource('payment-receipts', PaymentReceiptController::class)->middleware($authMiddleware);
	Route::apiResource('global-roles', GlobalRoleController::class)->middleware($authMiddleware);
	Route::apiResource('rules-versions', RulesVersionController::class)->middleware($authMiddleware);
	Route::apiResource('rule-acceptances', RuleAcceptanceController::class)->middleware($authMiddleware);
	Route::apiResource('referees', RefereeController::class)->middleware($authMiddleware);
	Route::apiResource('sanctions', SanctionController::class)->middleware($authMiddleware);
	Route::apiResource('team-admins', TeamAdminController::class)->middleware($authMiddleware);
	Route::apiResource('team-players', TeamPlayerController::class)->middleware($authMiddleware);
	Route::apiResource('team-stats', TeamStatController::class)->middleware($authMiddleware);
	Route::apiResource('team-tournament-registrations', TeamTournamentRegistrationController::class)->middleware($authMiddleware);
	Route::apiResource('tournament-admins', TournamentAdminController::class)->middleware($authMiddleware);
	Route::apiResource('tournament-categories', TournamentCategoryController::class)->middleware($authMiddleware);
	Route::apiResource('tournament-groups', TournamentGroupController::class)->middleware($authMiddleware);
	Route::apiResource('tournament-news', TournamentNewsController::class)->middleware($authMiddleware);
	Route::apiResource('tournament-phases', TournamentPhaseController::class)->middleware($authMiddleware);
	Route::apiResource('tournament-players', TournamentPlayerController::class)->middleware($authMiddleware);
	Route::apiResource('tournament-settings', TournamentSettingController::class)->middleware($authMiddleware);
	Route::apiResource('phase-teams', PhaseTeamController::class)->middleware($authMiddleware);
	Route::apiResource('match-events', MatchEventController::class)->middleware($authMiddleware);
	Route::apiResource('match-lineups', MatchLineupController::class)->middleware($authMiddleware);
	Route::apiResource('match-lineup-players', MatchLineupPlayerController::class)->middleware($authMiddleware);
	Route::apiResource('player-documents', PlayerDocumentController::class)->middleware($authMiddleware);
	Route::apiResource('player-stats', PlayerStatController::class)->middleware($authMiddleware);
	Route::post('player-term-acceptances', [PlayerTermAcceptanceController::class, 'store'])->middleware($authMiddleware);
} else {
	Route::apiResource('matches', FootballMatchController::class);
	Route::apiResource('matchdays', MatchdayController::class);
	Route::apiResource('payments', PaymentController::class);
	Route::apiResource('payment-logs', PaymentLogController::class);
	Route::apiResource('payment-receipts', PaymentReceiptController::class);
	Route::apiResource('global-roles', GlobalRoleController::class);
	Route::apiResource('rules-versions', RulesVersionController::class);
	Route::apiResource('rule-acceptances', RuleAcceptanceController::class);
	Route::apiResource('referees', RefereeController::class);
	Route::apiResource('sanctions', SanctionController::class);
	Route::apiResource('team-admins', TeamAdminController::class);
	Route::apiResource('team-players', TeamPlayerController::class);
	Route::apiResource('team-stats', TeamStatController::class);
	Route::apiResource('team-tournament-registrations', TeamTournamentRegistrationController::class);
	Route::apiResource('tournament-admins', TournamentAdminController::class);
	Route::apiResource('tournament-categories', TournamentCategoryController::class);
	Route::apiResource('tournament-groups', TournamentGroupController::class);
	Route::apiResource('tournament-news', TournamentNewsController::class);
	Route::apiResource('tournament-phases', TournamentPhaseController::class);
	Route::apiResource('tournament-players', TournamentPlayerController::class);
	Route::apiResource('tournament-settings', TournamentSettingController::class);
	Route::apiResource('phase-teams', PhaseTeamController::class);
	Route::apiResource('match-events', MatchEventController::class);
	Route::apiResource('match-lineups', MatchLineupController::class);
	Route::apiResource('match-lineup-players', MatchLineupPlayerController::class);
	Route::apiResource('player-documents', PlayerDocumentController::class);
	Route::apiResource('player-stats', PlayerStatController::class);
	Route::post('player-term-acceptances', [PlayerTermAcceptanceController::class, 'store']);
}
