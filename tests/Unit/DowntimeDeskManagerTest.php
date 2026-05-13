<?php

namespace DowntimeDesk\Laravel\Tests\Unit;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use DowntimeDesk\Laravel\DowntimeDeskManager;
use DowntimeDesk\Laravel\Tests\TestCase;

class DowntimeDeskManagerTest extends TestCase
{
    public function test_it_dispatches_default_webhook(): void
    {
        Http::fake();

        app(DowntimeDeskManager::class)->report();

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Monitor-Key', 'abcd');
        });
    }

    public function test_it_can_ping_directly(): void
    {
        Http::fake();

        app(DowntimeDeskManager::class)
            ->ping('id', 'secret');

        Http::assertSentCount(1);
    }

    public function test_should_dispatch_returns_true_initially(): void
    {
        Cache::flush();

        $result = app(DowntimeDeskManager::class)
            ->shouldDispatch('default');

        $this->assertTrue($result);
    }

    public function test_should_dispatch_returns_false_within_interval(): void
    {
        Cache::flush();

        $manager = app(DowntimeDeskManager::class);

        $manager->shouldDispatch('default');

        $this->assertFalse(
            $manager->shouldDispatch('default')
        );
    }

    public function test_it_can_disable_auto_scheduling(): void
    {
        $manager = app(DowntimeDeskManager::class);

        $manager->disableAutoScheduling();

        $this->assertTrue(
            DowntimeDeskManager::schedulingDisabled()
        );
    }
}
