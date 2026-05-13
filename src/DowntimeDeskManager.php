<?php

namespace DowntimeDesk\Laravel;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class DowntimeDeskManager
{
    protected static bool $schedulingDisabled = false;

    public function disableAutoScheduling(): void
    {
        static::$schedulingDisabled = true;
    }

    public static function schedulingDisabled(): bool
    {
        return static::$schedulingDisabled;
    }

    public function report(?string $name = null): void
    {
        $name ??= config('downtime-desk.default');

        $config = config("downtime-desk.webhooks.{$name}");

        if (! $config) {
            return;
        }

        if (blank($secret = $config['secret'] ?? null)) {
            $secret = null;
        }

        $this->ping($config['id'], $secret);
    }

    public function ping(
        string $id,
        ?string $secret = null
    ): void {
        try {
            Http::timeout(
                config('downtime-desk.timeout', 5)
            )->withHeaders([
                'X-Monitor-Key' => $secret,
            ])->post(
                rtrim(config('downtime-desk.base_url'), '/').'/api/heartbeat/'.$id
            );
        } catch (Throwable $e) {
            report($e);

            if (config('downtime-desk.throw', false)) {
                throw $e;
            }
        }
    }

    public function shouldDispatch(string $name): bool
    {
        $interval = (int) config(
            "downtime-desk.webhooks.{$name}.interval",
            60
        );

        $cacheKey = "downtime-desk:last-ping:{$name}";

        $lastPing = Cache::get($cacheKey);

        if (! $lastPing) {
            Cache::put($cacheKey, now(), now()->addDay());

            return true;
        }

        if (now()->diffInSeconds($lastPing) >= $interval) {
            Cache::put($cacheKey, now(), now()->addDay());

            return true;
        }

        return false;
    }
}
