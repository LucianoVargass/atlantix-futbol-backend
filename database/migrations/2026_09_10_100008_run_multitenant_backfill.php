<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;
use App\Services\MultitenantBackfill;

return new class extends Migration
{
    public function up(): void
    {
        // Datos existentes -> modelo multi-tenant (organizaciones, divisiones,
        // dueños de club, género normalizado). Idempotente.
        // No hace fallar el deploy: si algo sale mal, se puede re-correr con
        //   php artisan tinker --execute="App\Services\MultitenantBackfill::run()"
        try {
            MultitenantBackfill::run();
        } catch (\Throwable $e) {
            Log::error('MultitenantBackfill falló en la migración: ' . $e->getMessage());
        }
    }

    public function down(): void
    {
        // No se revierte el backfill de datos.
    }
};
