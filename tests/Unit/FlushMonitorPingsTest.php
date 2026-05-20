<?php

namespace DowntimeDesk\Laravel\Tests\Unit;

use DowntimeDesk\Laravel\Facades\DowntimeDesk;
use DowntimeDesk\Laravel\Jobs\FlushMonitorPings;
use DowntimeDesk\Laravel\Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FlushMonitorPingsTest extends TestCase
{
    public function test_it_flushes_pings_from_cache(): void
    {
        Http::fake();
        Cache::flush();

        // Mock config for default monitor
        config(['downtime-desk.monitors.default' => ['id' => '123', 'secret' => 'abc']]);
        DowntimeDesk::report();

        $this->assertCount(1, Cache::get('monitor:pings'));

        (new FlushMonitorPings)->handle();

        Http::assertSentCount(1);
        $this->assertEmpty(Cache::get('monitor:pings', []));
    }

    public function test_it_only_flushes_up_to_max_batch_size(): void
    {
        Http::fake();
        Cache::flush();
        config(['downtime-desk.monitors.default' => ['id' => '123', 'secret' => 'abc']]);

        DowntimeDesk::limitBatchSizeTo(2);

        DowntimeDesk::report();
        DowntimeDesk::report();
        DowntimeDesk::report();

        $this->assertCount(3, Cache::get('monitor:pings'));

        (new FlushMonitorPings)->handle();

        Http::assertSentCount(2);
        $this->assertCount(1, Cache::get('monitor:pings'));
    }

    public function test_it_uses_correct_headers_and_url(): void
    {
        Http::fake();
        Cache::flush();
        config(['downtime-desk.monitors.default' => ['id' => '123', 'secret' => 'abc']]);
        config(['downtime-desk.base_url' => 'https://example.com/{uuid}']);

        DowntimeDesk::report();

        (new FlushMonitorPings)->handle();

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Monitor-Secret', 'abc') &&
                   $request->url() === 'https://example.com/123';
        });
    }

    public function test_it_does_not_lose_pings_on_http_failure(): void
    {
        Http::fake([
            '*' => Http::response([], 500),
        ]);
        Cache::flush();
        config(['downtime-desk.monitors.default' => ['id' => '123', 'secret' => 'abc']]);

        DowntimeDesk::report();

        $this->assertCount(1, Cache::get('monitor:pings'));

        (new FlushMonitorPings)->handle();

        Http::assertSentCount(1);
        // Should be put back in cache
        $this->assertCount(1, Cache::get('monitor:pings'));
    }
}
