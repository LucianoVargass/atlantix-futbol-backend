<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('football_matches', function (Blueprint $table) {
            $table->unsignedBigInteger('player_of_match_id')->nullable()->after('referee_id');
            $table->string('player_of_match_name')->nullable()->after('player_of_match_id');
            $table->foreign('player_of_match_id')->references('id')->on('players')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('football_matches', function (Blueprint $table) {
            $table->dropForeign(['player_of_match_id']);
            $table->dropColumn(['player_of_match_id', 'player_of_match_name']);
        });
    }
};
