<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('team_id')->references('id')->on('teams')->nullOnDelete();
        });

        Schema::table('player_documents', function (Blueprint $table) {
            $table->foreign('player_id')->references('id')->on('players')->cascadeOnDelete();
        });

        Schema::table('rules_versions', function (Blueprint $table) {
            $table->foreign('tournament_id')->references('id')->on('tournaments')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('team_admins', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('team_players', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->foreign('player_id')->references('id')->on('players')->cascadeOnDelete();
        });

        Schema::table('team_tournament_registrations', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->foreign('tournament_id')->references('id')->on('tournaments')->cascadeOnDelete();
        });

        Schema::table('tournament_admins', function (Blueprint $table) {
            $table->foreign('tournament_id')->references('id')->on('tournaments')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('tournament_categories', function (Blueprint $table) {
            $table->foreign('tournament_id')->references('id')->on('tournaments')->cascadeOnDelete();
        });

        Schema::table('tournament_players', function (Blueprint $table) {
            $table->foreign('tournament_id')->references('id')->on('tournaments')->cascadeOnDelete();
            $table->foreign('team_id')->references('id')->on('teams')->nullOnDelete();
            $table->foreign('player_id')->references('id')->on('players')->cascadeOnDelete();
        });

        Schema::table('tournament_groups', function (Blueprint $table) {
            $table->foreign('tournament_id')->references('id')->on('tournaments')->cascadeOnDelete();
            $table->foreign('phase_id')->references('id')->on('tournament_phases')->nullOnDelete();
        });

        Schema::table('phase_teams', function (Blueprint $table) {
            $table->foreign('phase_id')->references('id')->on('tournament_phases')->cascadeOnDelete();
            $table->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->foreign('group_id')->references('id')->on('tournament_groups')->nullOnDelete();
        });

        Schema::table('match_lineup_players', function (Blueprint $table) {
            $table->foreign('match_lineup_id')->references('id')->on('match_lineups')->cascadeOnDelete();
            $table->foreign('player_id')->references('id')->on('players')->cascadeOnDelete();
        });

        Schema::table('football_matches', function (Blueprint $table) {
            $table->foreign('tournament_id')->references('id')->on('tournaments')->cascadeOnDelete();
            $table->foreign('matchday_id')->references('id')->on('matchdays')->nullOnDelete();
            $table->foreign('home_team_id')->references('id')->on('teams')->nullOnDelete();
            $table->foreign('away_team_id')->references('id')->on('teams')->nullOnDelete();
            $table->foreign('referee_id')->references('id')->on('referees')->nullOnDelete();
        });

        // Duplicates removed: sanctions, tournament_news, tournament_settings, rule_acceptances
    }

    public function down(): void
    {
        // Duplicates removed: sanctions, tournament_news, tournament_settings, rule_acceptances

        Schema::table('football_matches', function (Blueprint $table) {
            $table->dropForeign(['tournament_id']);
            $table->dropForeign(['matchday_id']);
            $table->dropForeign(['home_team_id']);
            $table->dropForeign(['away_team_id']);
            $table->dropForeign(['referee_id']);
        });

        Schema::table('match_lineup_players', function (Blueprint $table) {
            $table->dropForeign(['match_lineup_id']);
            $table->dropForeign(['player_id']);
        });

        Schema::table('phase_teams', function (Blueprint $table) {
            $table->dropForeign(['phase_id']);
            $table->dropForeign(['team_id']);
            $table->dropForeign(['group_id']);
        });

        Schema::table('tournament_groups', function (Blueprint $table) {
            $table->dropForeign(['tournament_id']);
            $table->dropForeign(['phase_id']);
        });

        Schema::table('tournament_players', function (Blueprint $table) {
            $table->dropForeign(['tournament_id']);
            $table->dropForeign(['team_id']);
            $table->dropForeign(['player_id']);
        });

        Schema::table('tournament_categories', function (Blueprint $table) {
            $table->dropForeign(['tournament_id']);
        });

        Schema::table('tournament_admins', function (Blueprint $table) {
            $table->dropForeign(['tournament_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('team_tournament_registrations', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropForeign(['tournament_id']);
        });

        Schema::table('team_players', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropForeign(['player_id']);
        });

        Schema::table('team_admins', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('rules_versions', function (Blueprint $table) {
            $table->dropForeign(['tournament_id']);
            $table->dropForeign(['created_by']);
        });

        Schema::table('player_documents', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['team_id']);
        });
    }
};
