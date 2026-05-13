# Downtime Desk Laravel Integration

Quickly add Downtime Desk monitoring to your Laravel application.

## Features

- Composer installable
- Automatic scheduler registration
- Manual heartbeat dispatching
- Multiple named webhooks
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

```php
use DowntimeDesk\Laravel\Facades\DowntimeDesk;

DowntimeDesk::report();
```

---

## Named Webhook

```php
DowntimeDesk::report('database');
```

---

## Direct Ping

```php
DowntimeDesk::ping($id, $secret);
```

---

## Disable Auto Scheduling

```php
DowntimeDesk::disableAutoScheduling();
```

---

## Manual Scheduler

```php
use Illuminate\Support\Facades\Schedule;

Schedule::DowntimeDesk('default')
    ->everyMinute();
```

---

# Testing

```bash
.vendor/bin/phpunit
```

or

```bash
composer test
```
