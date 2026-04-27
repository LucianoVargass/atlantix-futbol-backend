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
        Schema::create('team_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('tournament_id')->constrained('tournaments')->cascadeOnDelete();
            $table->unsignedInteger('played')->default(0);
            $table->unsignedInteger('won')->default(0);
            $table->unsignedInteger('draw')->default(0);
            $table->unsignedInteger('lost')->default(0);
            $table->unsignedInteger('goals_for')->default(0);
            $table->unsignedInteger('goals_against')->default(0);
            $table->unsignedInteger('points')->default(0);
            $table->unsignedInteger('yellow_cards')->default(0);
            $table->unsignedInteger('red_cards')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_stats');
    }
};
