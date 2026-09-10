<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\SuperAdminSeeder;
use Database\Seeders\DemoTournamentSeeder;
use Database\Seeders\DemoResultsSeeder;
use App\Services\MultitenantBackfill;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SuperAdminSeeder::class);
        $this->call(DemoTournamentSeeder::class);
        $this->call(DemoResultsSeeder::class);

        // Deja el dataset demo en el modelo multi-tenant (organizaciones,
        // divisiones "General", dueños de club, género normalizado).
        MultitenantBackfill::run();
    }
}
