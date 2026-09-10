<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Tournament;
use App\Models\TournamentAdmin;
use App\Models\Division;
use App\Models\Team;
use App\Models\TeamAdmin;

/**
 * Lleva el esquema plano al modelo multi-tenant. Idempotente: se puede correr
 * muchas veces sin duplicar nada. Se invoca desde la migración de backfill y
 * desde el DatabaseSeeder (para instalaciones frescas).
 */
class MultitenantBackfill
{
    public static function run(): void
    {
        self::normalizeGender();
        self::organizations();
        self::divisions();
        self::teamOwners();
    }

    /** players.gender: texto libre -> enum male | female | mixed. */
    protected static function normalizeGender(): void
    {
        DB::table('players')
            ->whereNotNull('gender')
            ->whereNotIn('gender', ['male', 'female', 'mixed'])
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $g = mb_strtolower(trim($row->gender));
                    $val = str_starts_with($g, 'f') || str_contains($g, 'mujer') || str_contains($g, 'women')
                        ? 'female'
                        : 'male';
                    DB::table('players')->where('id', $row->id)->update(['gender' => $val]);
                }
            });

        DB::table('players')->whereNull('gender')->update(['gender' => 'male']);
    }

    /** Una organización por cada admin de torneo; los torneos heredan organization_id. */
    protected static function organizations(): void
    {
        $orgByUser = [];

        $ensureOrg = function (?int $userId) use (&$orgByUser): ?Organization {
            if (!$userId) {
                return null;
            }
            if (isset($orgByUser[$userId])) {
                return $orgByUser[$userId];
            }

            // ¿ya es owner de una organización?
            $org = Organization::whereHas('members', fn ($q) => $q->where('user_id', $userId)->where('role', 'owner'))->first();

            if (!$org) {
                $user = User::find($userId);
                if (!$user) {
                    return null;
                }
                $name = trim((string) $user->name) !== '' ? $user->name : ('Organización ' . $userId);
                $slug = Str::slug($name) ?: ('org-' . $userId);
                if (Organization::where('slug', $slug)->exists()) {
                    $slug .= '-' . $userId;
                }

                $org = Organization::create(['name' => $name, 'slug' => $slug, 'plan' => 'free']);
                OrganizationMember::create([
                    'organization_id' => $org->id,
                    'user_id' => $userId,
                    'role' => 'owner',
                ]);
            }

            $orgByUser[$userId] = $org;
            return $org;
        };

        Tournament::whereNull('organization_id')->orderBy('id')->chunkById(200, function ($tournaments) use ($ensureOrg) {
            foreach ($tournaments as $tournament) {
                $ownerId = $tournament->admin_user_id
                    ?? TournamentAdmin::where('tournament_id', $tournament->id)->value('user_id')
                    ?? User::where('role', 'super_admin')->value('id')
                    ?? User::orderBy('id')->value('id');

                $org = $ensureOrg($ownerId);
                if ($org) {
                    $tournament->update(['organization_id' => $org->id]);

                    // los co-admins del torneo entran como admin de la org
                    TournamentAdmin::where('tournament_id', $tournament->id)->pluck('user_id')->each(function ($uid) use ($org) {
                        OrganizationMember::firstOrCreate(
                            ['organization_id' => $org->id, 'user_id' => $uid],
                            ['role' => 'admin'],
                        );
                    });
                }
            }
        });
    }

    /** Cada torneo sin divisiones recibe una "General" y se le asignan sus filas. */
    protected static function divisions(): void
    {
        Tournament::doesntHave('divisions')->orderBy('id')->chunkById(200, function ($tournaments) {
            foreach ($tournaments as $tournament) {
                $division = Division::create([
                    'tournament_id' => $tournament->id,
                    'name' => 'General',
                    'gender' => 'male',
                    'order' => 1,
                ]);

                foreach (['team_tournament_registrations', 'tournament_players', 'matchdays', 'football_matches'] as $table) {
                    DB::table($table)
                        ->where('tournament_id', $tournament->id)
                        ->whereNull('division_id')
                        ->update(['division_id' => $division->id]);
                }
            }
        });
    }

    /** teams.owner_user_id desde el primer team_admin. */
    protected static function teamOwners(): void
    {
        Team::whereNull('owner_user_id')->orderBy('id')->chunkById(300, function ($teams) {
            foreach ($teams as $team) {
                $ownerId = TeamAdmin::where('team_id', $team->id)->orderBy('id')->value('user_id');
                if ($ownerId) {
                    $team->update(['owner_user_id' => $ownerId]);
                }
            }
        });
    }
}
