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
        Schema::create('tournament_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tournament_id')->index();
            $table->string('name');
            $table->unsignedInteger('position_from')->nullable();
            $table->unsignedInteger('position_to')->nullable();
            $table->string('format')->nullable();
            $table->string('prize')->nullable();
            $table->unsignedInteger('order')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_categories');
    }
};
