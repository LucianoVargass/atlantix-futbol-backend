<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tournament_players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tournament_id')->index();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->unsignedBigInteger('player_id')->index();
            $table->string('status')->default('registered');
            $table->unsignedInteger('shirt_number')->nullable();
            $table->boolean('rules_accepted')->default(false);
            $table->string('documentation_status')->default('pending');
            $table->unsignedInteger('goals')->default(0);
            $table->unsignedInteger('yellow_cards')->default(0);
            $table->unsignedInteger('red_cards')->default(0);
            $table->unsignedInteger('appearances')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_players');
    }
};
