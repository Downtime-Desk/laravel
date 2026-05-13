<?php

namespace DowntimeDesk\Laravel\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use DowntimeDesk\Laravel\Facades\DowntimeDesk;
use DowntimeDesk\Laravel\DowntimeDeskLaravelIntegrationServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            DowntimeDeskLaravelIntegrationServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'DowntimeDesk' => DowntimeDesk::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $config = require __DIR__.'/../config/downtime-desk.php';

        $app['config']->set('downtime-desk', $config);
    }
}
