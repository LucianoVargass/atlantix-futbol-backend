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
        Schema::table('users', function (Blueprint $table) {
            $table->string('mp_access_token')->nullable()->after('avatar_url');
            $table->string('mp_public_key')->nullable()->after('mp_access_token');
            $table->string('mp_mode')->nullable()->after('mp_public_key');
            $table->string('mp_notification_url')->nullable()->after('mp_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('mp_notification_url');
            $table->dropColumn('mp_mode');
            $table->dropColumn('mp_public_key');
            $table->dropColumn('mp_access_token');
        });
    }
};
