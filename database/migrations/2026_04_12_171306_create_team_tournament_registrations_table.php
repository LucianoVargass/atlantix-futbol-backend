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
        Schema::create('team_tournament_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('tournament_id')->index();
            $table->string('subscription_status')->default('registered');
            $table->timestamp('subscription_date')->nullable();
            $table->string('payment_status')->default('pending');
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->boolean('rules_accepted')->default(false);
            $table->string('rules_accepted_version')->nullable();
            $table->timestamp('rules_accepted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_tournament_registrations');
    }
};
