<?php

namespace DowntimeDesk\Laravel\Tests\Unit;

use DowntimeDesk\Laravel\Facades\DowntimeDesk;
use DowntimeDesk\Laravel\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class FacadeTest extends TestCase
{
    public function test_disabling_scheduling_sets_flag(): void
    {
        $this->assertFalse(DowntimeDesk::schedulingDisabled());

        DowntimeDesk::withoutScheduling();

        $this->assertTrue(DowntimeDesk::schedulingDisabled());
    }

    public function test_disabling_aggregation_sets_flag(): void
    {
        $this->assertFalse(DowntimeDesk::aggregationDisabled());

        DowntimeDesk::withoutAggregation();

        $this->assertTrue(DowntimeDesk::aggregationDisabled());
    }

    public function test_setting_default_monitor_key(): void
    {
        $this->assertEquals('default', DowntimeDesk::defaultMonitorName());

        DowntimeDesk::defaultMonitor('abcd');

        $this->assertEquals('abcd', DowntimeDesk::defaultMonitorName());
    }

    public function test_setting_max_batch_size(): void
    {
        $this->assertEquals(100, DowntimeDesk::batchSizeLimit());

        DowntimeDesk::limitBatchSizeTo(50);

        $this->assertEquals(50, DowntimeDesk::batchSizeLimit());
    }

    public function test_cache_key_setting(): void
    {
        $this->assertEquals('monitor:pings', DowntimeDesk::cacheKeyName());

        DowntimeDesk::useCacheKeyName('custom:key');

        $this->assertEquals('custom:key', DowntimeDesk::cacheKeyName());
    }

    public function test_facade_dispatches_report(): void
    {
        Http::fake();

        config(['downtime-desk.monitors.default' => ['id' => '123', 'secret' => 'abc']]);

        DowntimeDesk::withoutAggregation();

        DowntimeDesk::report();

        Http::assertSentCount(1);
    }

    public function test_facade_dispatches_direct_ping(): void
    {
        Http::fake();

        DowntimeDesk::ping('id', 'secret');

        Http::assertSentCount(1);
    }
}
