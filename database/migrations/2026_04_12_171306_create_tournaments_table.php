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
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('draft');
            $table->string('format')->default('league');
            $table->string('sport_type')->default('futbol5');
            $table->unsignedInteger('players_per_team')->default(5);
            $table->unsignedInteger('max_teams')->default(0);
            $table->unsignedInteger('registered_teams')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('rules_version')->nullable();
            $table->json('rules')->nullable();
            $table->json('competition_format')->nullable();
            $table->decimal('registration_fee', 10, 2)->nullable();
            $table->decimal('matchday_fee', 10, 2)->nullable();
            $table->string('currency', 10)->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('venue')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
