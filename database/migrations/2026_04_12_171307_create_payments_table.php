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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->nullable()->constrained('tournaments')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('payer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type')->default('registration_fee');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('ARS');
            $table->string('status')->default('pending');
            $table->string('method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('reference')->nullable();
            $table->string('external_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
