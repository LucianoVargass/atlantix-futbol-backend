<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MultitenantBackfill;

class MultitenantBackfillCommand extends Command
{
    protected $signature = 'atlantix:backfill-multitenant';

    protected $description = 'Lleva los datos existentes al modelo multi-tenant (organizaciones, divisiones, dueños de club). Idempotente.';

    public function handle(): int
    {
        $this->info('Corriendo backfill multi-tenant…');
        MultitenantBackfill::run();
        $this->info('Listo.');
        return self::SUCCESS;
    }
}
