<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['team_tournament_registrations', 'tournament_players', 'matchdays', 'football_matches'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->unsignedBigInteger('division_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        foreach (['team_tournament_registrations', 'tournament_players', 'matchdays', 'football_matches'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->dropColumn('division_id');
            });
        }
    }
};
