<?php

namespace DowntimeDesk\Laravel\Jobs;

use DowntimeDesk\Laravel\DowntimeDeskManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DispatchHeartbeat implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected string $name
    ) {
        $this->onQueue(config('downtime-desk.queue'));
    }

    public function handle(DowntimeDeskManager $manager): void
    {
        $manager->report($this->name);
    }
}
