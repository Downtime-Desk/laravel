<?php

namespace DowntimeDesk\Laravel\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DowntimeDesk\Laravel\DowntimeDeskManager;

class DispatchHeartbeat implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected string $name
    ) {
        $this->onQueue(
            config('downtime-desk.queue', 'monitoring')
        );
    }

    public function handle(
        DowntimeDeskManager $manager
    ): void {
        if (! $manager->shouldDispatch($this->name)) {
            return;
        }

        $manager->report($this->name);
    }
}
