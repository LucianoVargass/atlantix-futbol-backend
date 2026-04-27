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
        Schema::create('football_matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tournament_id')->index();
            $table->unsignedBigInteger('matchday_id')->nullable()->index();
            $table->unsignedBigInteger('home_team_id')->nullable()->index();
            $table->unsignedBigInteger('away_team_id')->nullable()->index();
            $table->unsignedBigInteger('referee_id')->nullable()->index();
            $table->string('status')->default('upcoming');
            $table->string('period')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->string('venue')->nullable();
            $table->unsignedInteger('score_home')->default(0);
            $table->unsignedInteger('score_away')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('football_matches');
    }
};
