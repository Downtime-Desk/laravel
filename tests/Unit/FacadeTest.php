<?php

namespace DowntimeDesk\Laravel\Tests\Unit;

use Illuminate\Support\Facades\Http;
use DowntimeDesk\Laravel\Facades\DowntimeDesk;
use DowntimeDesk\Laravel\Tests\TestCase;

class FacadeTest extends TestCase
{
    public function test_facade_dispatches_report(): void
    {
        Http::fake();

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
