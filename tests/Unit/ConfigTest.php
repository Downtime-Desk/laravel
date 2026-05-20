<?php

namespace DowntimeDesk\Laravel\Tests\Unit;

use DowntimeDesk\Laravel\Tests\TestCase;

class ConfigTest extends TestCase
{
    public function test_config_contains_default_monitor(): void
    {
        $this->assertEquals('default', config('downtime-desk.default'));
        $this->assertArrayHasKey('default', config('downtime-desk.monitors'));
    }
}
