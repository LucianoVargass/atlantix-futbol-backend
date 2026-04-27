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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->string('name');
            $table->string('surname');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();
            $table->string('position')->nullable();
            $table->unsignedInteger('shirt_number')->nullable();
            $table->string('profile_photo')->nullable();
            $table->unsignedInteger('goals')->default(0);
            $table->unsignedInteger('yellow_cards')->default(0);
            $table->unsignedInteger('red_cards')->default(0);
            $table->boolean('is_figura')->default(false);
            $table->unsignedInteger('appearances')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
