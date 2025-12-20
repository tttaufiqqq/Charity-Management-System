<?php

namespace App\Console\Commands;

use App\Services\EventStatusService;
use App\Services\VolunteerHoursService;
use Illuminate\Console\Command;

class UpdateEventStatusesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:update-statuses
                            {--calculate-hours : Also auto-calculate volunteer hours for completed events}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-update event statuses based on start/end dates and optionally calculate volunteer hours';

    protected EventStatusService $eventStatusService;

    protected VolunteerHoursService $hoursService;

    public function __construct(EventStatusService $eventStatusService, VolunteerHoursService $hoursService)
    {
        parent::__construct();
        $this->eventStatusService = $eventStatusService;
        $this->hoursService = $hoursService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting event status updates...');

        // Update event statuses
        $updated = $this->eventStatusService->autoUpdateStatuses();

        if ($updated['to_ongoing'] > 0) {
            $this->info("✓ Updated {$updated['to_ongoing']} event(s) to 'Ongoing' status");
        }

        if ($updated['to_completed'] > 0) {
            $this->info("✓ Updated {$updated['to_completed']} event(s) to 'Completed' status");
        }

        if ($updated['to_ongoing'] === 0 && $updated['to_completed'] === 0) {
            $this->comment('No events needed status updates');
        }

        // Auto-calculate volunteer hours if requested
        if ($this->option('calculate-hours')) {
            $this->info('Calculating volunteer hours for completed events...');
            $hoursUpdated = $this->hoursService->autoCalculateForCompletedEvents();

            if ($hoursUpdated > 0) {
                $this->info("✓ Calculated hours for {$hoursUpdated} volunteer participation(s)");
            } else {
                $this->comment('No volunteer hours needed calculation');
            }
        }

        $this->newLine();
        $this->info('Event status update completed successfully!');

        return Command::SUCCESS;
    }
}
