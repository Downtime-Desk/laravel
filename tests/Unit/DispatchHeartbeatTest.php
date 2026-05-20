<?php

namespace DowntimeDesk\Laravel\Tests\Unit;

use DowntimeDesk\Laravel\DowntimeDeskManager;
use DowntimeDesk\Laravel\Facades\DowntimeDesk;
use DowntimeDesk\Laravel\Jobs\DispatchHeartbeat;
use DowntimeDesk\Laravel\Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DispatchHeartbeatTest extends TestCase
{
    public function test_job_dispatches_heartbeat(): void
    {
        Http::fake();
        config(['downtime-desk.monitors.default' => ['id' => '123', 'secret' => 'abc']]);

        DowntimeDesk::withoutAggregation();

        $job = new DispatchHeartbeat('default');

        $job->handle(
            app(DowntimeDeskManager::class)
        );

        Http::assertSentCount(1);
    }

    public function test_job_aggregates_heartbeat(): void
    {
        Cache::flush();
        config(['downtime-desk.monitors.default' => ['id' => '123', 'secret' => 'abc']]);

        $job = new DispatchHeartbeat('default');

        $job->handle(
            app(DowntimeDeskManager::class)
        );

        $this->assertCount(1, Cache::get('monitor:pings'));
    }
}
