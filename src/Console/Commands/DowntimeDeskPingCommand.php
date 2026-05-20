<?php

namespace DowntimeDesk\Laravel\Console\Commands;

use DowntimeDesk\Laravel\DowntimeDeskManager;
use Illuminate\Console\Command;

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
