<?php

namespace DowntimeDesk\Laravel\Console\Commands;

use Illuminate\Console\Command;
use DowntimeDesk\Laravel\DowntimeDeskManager;

class DowntimeDeskPingCommand extends Command
{
    protected $signature = 'downtime-desk:ping {name=default}';

    protected $description = 'Dispatch a Downtime Desk heartbeat';

    public function handle(
        DowntimeDeskManager $manager
    ): int {
        $manager->report(
            $this->argument('name')
        );

        $this->info('Heartbeat dispatched.');

        return self::SUCCESS;
    }
}
