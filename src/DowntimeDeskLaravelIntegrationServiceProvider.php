<?php

namespace DowntimeDesk\Laravel;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use DowntimeDesk\Laravel\Console\Commands\DowntimeDeskPingCommand;
use DowntimeDesk\Laravel\Jobs\DispatchHeartbeat;

class DowntimeDeskLaravelIntegrationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/downtime-desk.php' => config_path('downtime-desk.php'),
        ], 'downtime-desk-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                DowntimeDeskPingCommand::class,
            ]);
        }

        $this->app->booted(function () {
            $this->registerScheduler();
            $this->registerMacros();
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/downtime-desk.php',
            'downtime-desk'
        );

        $this->app->singleton(
            DowntimeDeskManager::class,
            fn () => new DowntimeDeskManager
        );

        $this->app->alias(
            DowntimeDeskManager::class,
            'downtime-desk'
        );
    }

    protected function registerScheduler(): void
    {
        if (! config('downtime-desk.enabled')) {
            return;
        }

        if (! config('downtime-desk.auto_schedule')) {
            return;
        }

        if (DowntimeDeskManager::schedulingDisabled()) {
            return;
        }

        $schedule = $this->app->make(Schedule::class);

        foreach (
            config('downtime-desk.webhooks', []) as $name => $webhook
        ) {
            $schedule->job(
                new DispatchHeartbeat($name)
            )->everyMinute();
        }
    }

    protected function registerMacros(): void
    {
        Schedule::macro('DowntimeDesk', function (string $name) {
            return $this->app->call(function () use ($name) {
                app(DowntimeDeskManager::class)
                    ->report($name);
            });
        });
    }
}
