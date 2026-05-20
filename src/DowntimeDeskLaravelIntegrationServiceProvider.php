<?php

namespace DowntimeDesk\Laravel;

use DowntimeDesk\Laravel\Console\Commands\DowntimeDeskPingCommand;
use DowntimeDesk\Laravel\Contracts\Dispatcher as DispatcherContract;
use DowntimeDesk\Laravel\Dispatchers\Dispatcher;
use DowntimeDesk\Laravel\Jobs\FlushMonitorPings;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

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
            $this->registerAggregator();
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

        $this->app->alias(
            DowntimeDeskManager::class,
            'downtime-desk'
        );

        $this->app->singleton(DowntimeDeskManager::class);

        $this->app->bind(
            DispatcherContract::class,
            fn () => app(Dispatcher::class)
        );
    }

    protected function registerAggregator(): void
    {
        if (! config('downtime-desk.enabled')) {
            return;
        }

        if (DowntimeDeskManager::aggregationDisabled()) {
            return;
        }

        $schedule = $this->app->make(Schedule::class);

        $schedule->job(FlushMonitorPings::class)->everyFiveSeconds();
    }

    protected function registerScheduler(): void
    {
        if (! config('downtime-desk.enabled')) {
            return;
        }

        if (DowntimeDeskManager::schedulingDisabled()) {
            return;
        }

        $schedule = $this->app->make(Schedule::class);

        foreach (array_keys(config('downtime-desk.monitors', [])) as $name) {
            DowntimeDeskManager::scheduleRegistrationCallback()($schedule, $name);
        }
    }

    protected function registerMacros(): void
    {
        Schedule::macro('downtimeDesk',
            fn (string $name) => $this->app->call(fn () => app(DowntimeDeskManager::class)->report($name))
        );
    }
}
