<?php

namespace DowntimeDesk\Laravel\Tests\Unit;

use Illuminate\Console\Scheduling\Schedule;
use DowntimeDesk\Laravel\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_service_binding_exists(): void
    {
        $this->assertTrue(
            app()->bound('downtime-desk')
        );
    }

    public function test_schedule_macro_is_registered(): void
    {
        $schedule = app(Schedule::class);

        $this->assertTrue(
            $schedule->hasMacro('DowntimeDesk')
        );
    }
}
