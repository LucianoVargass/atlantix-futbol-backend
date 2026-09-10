<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('player_documents', function (Blueprint $table) {
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('player_documents', function (Blueprint $table) {
            $table->dropColumn(['verified_at', 'expires_at', 'verified_by']);
        });
    }
};
