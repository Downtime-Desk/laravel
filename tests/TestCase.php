<?php

namespace DowntimeDesk\Laravel\Tests;

use DowntimeDesk\Laravel\DowntimeDeskLaravelIntegrationServiceProvider;
use DowntimeDesk\Laravel\DowntimeDeskManager;
use DowntimeDesk\Laravel\Facades\DowntimeDesk;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function tearDown(): void
    {
        DowntimeDeskManager::reset();

        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return [
            DowntimeDeskLaravelIntegrationServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return ['DowntimeDesk' => DowntimeDesk::class];
    }

    protected function defineEnvironment($app)
    {
        $config = require __DIR__.'/../config/downtime-desk.php';

        $app['config']->set('downtime-desk', $config);
    }
}
