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
        Schema::create('phase_teams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('phase_id')->index();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('group_id')->nullable()->index();
            $table->unsignedInteger('position')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phase_teams');
    }
};
