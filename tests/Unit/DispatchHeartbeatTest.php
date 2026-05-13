<?php

namespace DowntimeDesk\Laravel\Tests\Unit;

use Illuminate\Support\Facades\Http;
use DowntimeDesk\Laravel\Jobs\DispatchHeartbeat;
use DowntimeDesk\Laravel\DowntimeDeskManager;
use DowntimeDesk\Laravel\Tests\TestCase;

class DispatchHeartbeatTest extends TestCase
{
    public function test_job_dispatches_heartbeat(): void
    {
        Http::fake();

        $job = new DispatchHeartbeat('default');

        $job->handle(
            app(DowntimeDeskManager::class)
        );

        Http::assertSentCount(1);
    }
}
