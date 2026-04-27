<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_tournament_registrations', function (Blueprint $table) {
            $table->unique(['team_id', 'tournament_id'], 'team_tournament_unique');
        });

        Schema::table('tournament_players', function (Blueprint $table) {
            $table->unique(['tournament_id', 'player_id'], 'tournament_player_unique');
        });
    }

    public function down(): void
    {
        Schema::table('team_tournament_registrations', function (Blueprint $table) {
            $table->dropUnique('team_tournament_unique');
        });

        Schema::table('tournament_players', function (Blueprint $table) {
            $table->dropUnique('tournament_player_unique');
        });
    }
};
