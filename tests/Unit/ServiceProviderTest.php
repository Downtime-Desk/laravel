<?php

namespace DowntimeDesk\Laravel\Tests\Unit;

use DowntimeDesk\Laravel\Tests\TestCase;
use Illuminate\Console\Scheduling\Schedule;

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
        $this->assertTrue(
            Schedule::hasMacro('downtimeDesk')
        );
    }
}
