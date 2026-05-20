# Downtime Desk Laravel Integration

Quickly add Downtime Desk monitoring to your Laravel application.

## Features

- Automatic schedule registration
- Manual heartbeat dispatching
- Multiple named webhooks
- Automatic aggregation
- Queue support
- Scheduler macros
- Config-driven
- Zero-config onboarding

---

# Installation

```bash
composer require downtime-desk/laravel
```

Publish config:

```bash
php artisan vendor:publish --tag=downtime-desk-config
```

---

# Usage

## Default Webhook

Downtime Desk will automatically report for the default monitor when `report()` is called and no `$name` attribute is provided.

```php
use DowntimeDesk\Laravel\Facades\DowntimeDesk;

DowntimeDesk::report();
```

---

## Named Webhook

If you have multiple monitors configured in your application, then you can specify which monitor to report for by passing the name of the monitor to the `report()` method.

```php
DowntimeDesk::report('database');
```

## Immediate Reporting

If for whatever reason you'd like to circumvent the schedule, or reporting aggregation, you can call `reportNow()` to report immediately.

`reportNow()` accepts the same arguments as `report()`.

```php
DowntimeDesk::reportNow();
```

---

## Direct Ping

```php
DowntimeDesk::ping($id, $secret);
```

---

## Disable Auto Scheduling

This package automatically registers a schedule to report for the default monitor every minute. If you'd like to disable this,
you can call `DowntimeDesk::withoutScheduling()`.

This is useful if you're using a custom scheduler implementation, or say you're running your application in a non-scheduled environment.

```php
DowntimeDesk::withoutScheduling();
```

## Without Aggregation

Aggregation is enabled by default, and makes use of the configured cache driver to process and flush reports. 

You can disable aggregation by calling `DowntimeDesk::withoutAggregation()`.

```php
DowntimeDesk::withoutAggregation();
```

## Default Monitor Name

You can change the default monitor name by calling `DowntimeDesk::defaultMonitor()` and passing the new default monitor name.

Remember you'll need to update the published `downtime-desk` configuration file to match the new name.

```php
DowntimeDesk::defaultMonitor('example');
```

## Batch Size Limit

If you're experiencing issues related to the default batch size, you can adjust it by calling `DowntimeDesk::limitBatchSizeTo()` and passing the desired batch size.

```php
DowntimeDesk::limitBatchSizeTo(50);
```

## Schedule Registration Callback

You can customize the schedule registration callback by calling `DowntimeDesk::registerScheduleWith()` and passing a callable that accepts a `Schedule` instance and a monitor name.

_Note:_ Calling `DowntimeDesk::withoutScheduling()` will disable the schedule registration callback.

Below is the default callback as a starting point:

```php
function ($schedule, $name) {
    return $schedule->job(new DispatchHeartbeat($name))->everyMinute();
}
```

```php
DowntimeDesk::registerScheduleWith($callable);
```

## Cache Key Name

If you're experiencing issues related to the default cache key name, you can change it by calling `DowntimeDesk::useCacheKeyName()` and passing the new cache key name.

```php
DowntimeDesk::useCacheKeyName();
```

---

## Manual Scheduler

```php
use Illuminate\Support\Facades\Schedule;

Schedule::downtimeDesk('default')
    ->everyMinute();
```

---

# Testing

```bash
./vendor/bin/phpunit
```

or

```bash
composer test
```
