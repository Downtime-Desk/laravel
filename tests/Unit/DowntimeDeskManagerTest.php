<?php

namespace DowntimeDesk\Laravel\Tests\Unit;

use DowntimeDesk\Laravel\DowntimeDeskManager;
use DowntimeDesk\Laravel\Facades\DowntimeDesk;
use DowntimeDesk\Laravel\Tests\TestCase;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DowntimeDeskManagerTest extends TestCase
{
    public function test_it_dispatches_report_to_dispatcher(): void
    {
        Http::fake();
        config(['downtime-desk.monitors.default' => ['id' => '123', 'secret' => 'abc']]);

        DowntimeDesk::withoutAggregation();
        app(DowntimeDeskManager::class)->report();

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Monitor-Secret', 'abc') &&
                   str_contains($request->url(), '123');
        });
    }

    public function test_it_can_ping_directly(): void
    {
        Http::fake();

        app(DowntimeDeskManager::class)
            ->ping('123', 'abc');

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Monitor-Secret', 'abc') &&
                   str_contains($request->url(), '123');
        });
    }

    public function test_it_aggregates_pings_in_cache(): void
    {
        Cache::flush();
        config(['downtime-desk.monitors.default' => ['id' => '123', 'secret' => 'abc']]);

        app(DowntimeDeskManager::class)->report();

        $this->assertCount(1, Cache::get('monitor:pings'));
    }

    public function test_it_throws_exception_if_monitor_not_configured(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Monitor [non-existent] is not configured.');

        app(DowntimeDeskManager::class)->report('non-existent');
    }

    public function test_it_can_customize_schedule_registration(): void
    {
        $called = false;
        DowntimeDesk::registerScheduleWith(function ($schedule, $name) use (&$called) {
            $called = true;
        });

        $callback = DowntimeDeskManager::scheduleRegistrationCallback();
        $callback(app(Schedule::class), 'default');

        $this->assertTrue($called);
    }
}
