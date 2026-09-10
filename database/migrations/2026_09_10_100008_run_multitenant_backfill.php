<?php

use Illuminate\Database\Migrations\Migration;
use App\Services\MultitenantBackfill;

return new class extends Migration
{
    public function up(): void
    {
        // Datos existentes -> modelo multi-tenant (organizaciones, divisiones,
        // dueños de club, género normalizado). Idempotente.
        MultitenantBackfill::run();
    }

    public function down(): void
    {
        // No se revierte el backfill de datos.
    }
};
