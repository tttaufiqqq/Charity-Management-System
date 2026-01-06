<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class FreshAllDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:fresh-all {--seed : Run seeders after migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fresh migrate all heterogeneous databases (Izzhilmy, Sashvini, Izzati, Hannah, Adam)';

    /**
     * Database connections in order
     *
     * @var array
     */
    protected $connections = [
        'izzhilmy' => 'Izzhilmy (PostgreSQL) - Auth',
        'sashvini' => 'Sashvini (MariaDB) - Volunteers',
        'izzati' => 'Izzati (PostgreSQL) - Operations',
        'hannah' => 'Hannah (MySQL) - Finance',
        'adam' => 'Adam (MySQL) - Public/Recipients',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('╔════════════════════════════════════════════════════════╗');
        $this->info('║   CHARITY-IZZ HETEROGENEOUS DATABASE MIGRATION        ║');
        $this->info('╚════════════════════════════════════════════════════════╝');
        $this->newLine();

        if (! $this->confirm('This will DROP ALL TABLES in all 5 databases. Continue?', false)) {
            $this->error('❌ Migration cancelled by user.');

            return Command::FAILURE;
        }

        $this->newLine();

        foreach ($this->connections as $connection => $description) {
            $this->migrateConnection($connection, $description);
        }

        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('✅  ALL DATABASES MIGRATED SUCCESSFULLY!');
        $this->info('═══════════════════════════════════════════════════════');

        if ($this->option('seed')) {
            $this->newLine();
            $this->info('🌱  Running seeders...');
            Artisan::call('db:seed', [], $this->getOutput());
            $this->info('✅  Seeding completed!');
        }

        return Command::SUCCESS;
    }

    /**
     * Migrate a specific database connection
     */
    protected function migrateConnection(string $connection, string $description): void
    {
        $this->info('───────────────────────────────────────────────────────');
        $this->info("📦  Migrating: {$description}");
        $this->info("    Connection: {$connection}");

        try {
            // Test connection first
            \DB::connection($connection)->getPdo();

            // Fresh migrate
            Artisan::call('migrate:fresh', [
                '--database' => $connection,
                '--force' => true,
            ], $this->getOutput());

            $this->info("✅  {$connection} migrated successfully!");

        } catch (\Exception $e) {
            $this->error("❌  Failed to migrate {$connection}: {$e->getMessage()}");
            throw $e;
        }
    }
}
