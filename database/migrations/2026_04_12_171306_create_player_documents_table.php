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
        Schema::create('player_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id')->index();
            $table->string('document_type')->nullable();
            $table->string('document_number')->nullable();
            $table->string('front_url')->nullable();
            $table->string('back_url')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_documents');
    }
};
