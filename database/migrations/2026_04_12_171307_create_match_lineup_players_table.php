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
        Schema::create('match_lineup_players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_lineup_id')->index();
            $table->unsignedBigInteger('player_id')->index();
            $table->string('position')->nullable();
            $table->unsignedInteger('shirt_number')->nullable();
            $table->boolean('is_starter')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_lineup_players');
    }
};
