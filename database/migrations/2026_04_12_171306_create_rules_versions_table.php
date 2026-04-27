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
        Schema::create('rules_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tournament_id')->index();
            $table->unsignedInteger('version')->default(1);
            $table->json('rules')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rules_versions');
    }
};
