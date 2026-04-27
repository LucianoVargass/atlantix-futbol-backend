<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_term_acceptances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->foreignId('tournament_id')->constrained('tournaments')->cascadeOnDelete();
            $table->foreignId('tournament_term_id')->constrained('tournament_terms')->cascadeOnDelete();
            $table->timestamp('accepted_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->unique(['player_id', 'tournament_id', 'tournament_term_id'], 'player_term_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_term_acceptances');
    }
};
