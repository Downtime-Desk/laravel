<?php

namespace DowntimeDesk\Laravel;

use DowntimeDesk\Laravel\Contracts\Dispatcher;
use DowntimeDesk\Laravel\Jobs\DispatchHeartbeat;

class DowntimeDeskManager
{
    protected static bool $schedulingDisabled = false;

    protected static bool $aggregationDisabled = false;

    protected static string $defaultMonitor = 'default';

    protected static int $maxBatchSize = 100;

    protected static string $cacheKey = 'monitor:pings';

    protected static ?\Closure $scheduleRegistrationCallback = null;

    public function __construct(
        protected Dispatcher $dispatcher
    ) {}

    public function withoutScheduling(): void
    {
        static::$schedulingDisabled = true;
    }

    public function withoutAggregation(): void
    {
        static::$aggregationDisabled = true;
    }

    public function defaultMonitor(string $name): void
    {
        static::$defaultMonitor = $name;
    }

    public function limitBatchSizeTo(int $size): void
    {
        static::$maxBatchSize = $size;
    }

    public function registerScheduleWith(callable $callback): void
    {
        static::$scheduleRegistrationCallback = $callback;
    }

    public function useCacheKeyName(string $name): void
    {
        static::$cacheKey = $name;
    }

    public static function reset(): void
    {
        static::$schedulingDisabled = false;
        static::$aggregationDisabled = false;
        static::$defaultMonitor = 'default';
        static::$maxBatchSize = 100;
        static::$cacheKey = 'monitor:pings';
        static::$scheduleRegistrationCallback = null;
    }

    public static function schedulingDisabled(): bool
    {
        return static::$schedulingDisabled;
    }

    public static function aggregationDisabled(): bool
    {
        return static::$aggregationDisabled;
    }

    public static function defaultMonitorName(): string
    {
        return static::$defaultMonitor;
    }

    public static function batchSizeLimit(): int
    {
        return static::$maxBatchSize;
    }

    public static function cacheKeyName(): string
    {
        return static::$cacheKey;
    }

    public static function scheduleRegistrationCallback(): \Closure
    {
        return static::$scheduleRegistrationCallback
            ?? function ($schedule, $name) {
                return $schedule->job(new DispatchHeartbeat($name))->everyMinute();
            };
    }

    public function report(?string $name = null): void
    {
        $name = $name ?? static::defaultMonitorName();

        $config = $this->getMonitorConfig($name);

        if (static::aggregationDisabled()) {
            $this->reportNow($name);

            return;
        }

        $this->dispatcher->dispatch(
            id: $config['id'],
            secret: $config['secret'] ?? null
        );
    }

    public function reportNow(?string $name = null): void
    {
        $name = $name ?? static::defaultMonitorName();

        $config = $this->getMonitorConfig($name);

        $this->ping(
            id: $config['id'],
            secret: $config['secret'] ?? null
        );
    }

    public function ping(string $id, ?string $secret = null): void
    {
        $this->dispatcher->dispatchNow($id, $secret);
    }

    protected function getMonitorConfig(string $name): array
    {
        if (($config = config('downtime-desk.monitors.'.$name, [])) === []) {
            throw new \Exception("Monitor [$name] is not configured.");
        }

        if (empty($config['id'])) {
            throw new \Exception("Monitor [$name] is missing a webhook ID.");
        }

        return $config;
    }
}
