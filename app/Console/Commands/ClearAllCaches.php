<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearAllCaches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-all
                            {--silent : Run without output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all Laravel caches (config, route, view, application cache, compiled views)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $silent = $this->option('silent');

        if (! $silent) {
            $this->info('🧹 Clearing all Laravel caches...');
            $this->newLine();
        }

        $caches = [
            'config:clear' => 'Configuration cache',
            'route:clear' => 'Route cache',
            'view:clear' => 'Compiled views',
            'cache:clear' => 'Application cache',
            'event:clear' => 'Event cache',
        ];

        foreach ($caches as $command => $description) {
            try {
                Artisan::call($command);

                if (! $silent) {
                    $this->comment("  ✓ Cleared {$description}");
                }
            } catch (\Exception $e) {
                if (! $silent) {
                    $this->error("  ✗ Failed to clear {$description}: ".$e->getMessage());
                }
            }
        }

        if (! $silent) {
            $this->newLine();
            $this->info('✅ All caches cleared successfully!');
            $this->warn('⚠️  Remember to restart your development server for changes to take effect.');
        }

        return Command::SUCCESS;
    }
}
