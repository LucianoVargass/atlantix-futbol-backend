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
        Schema::table('sanctions', function (Blueprint $table) {
            $table->decimal('fine_amount', 10, 2)->nullable()->after('matches');
            $table->string('fine_currency', 10)->nullable()->after('fine_amount');
            $table->decimal('paid_amount', 10, 2)->nullable()->after('fine_currency');
            $table->timestamp('paid_at')->nullable()->after('paid_amount');
            $table->timestamp('cleared_at')->nullable()->after('paid_at');
            $table->text('cleared_reason')->nullable()->after('cleared_at');
            $table->boolean('clear_on_payment')->default(false)->after('cleared_reason');
            $table->boolean('is_permanent')->default(false)->after('clear_on_payment');
            $table->string('rule_key')->nullable()->after('is_permanent');
            $table->foreignId('source_match_id')->nullable()->after('rule_key')->constrained('football_matches')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sanctions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('source_match_id');
            $table->dropColumn([
                'rule_key',
                'is_permanent',
                'clear_on_payment',
                'cleared_reason',
                'cleared_at',
                'paid_at',
                'paid_amount',
                'fine_currency',
                'fine_amount',
            ]);
        });
    }
};
