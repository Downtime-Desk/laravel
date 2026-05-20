<?php

namespace DowntimeDesk\Laravel\Dispatchers;

use DowntimeDesk\Laravel\Contracts\Dispatcher as DispatcherContract;
use DowntimeDesk\Laravel\Facades\DowntimeDesk;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Dispatcher implements DispatcherContract
{
    /**
     * {@inheritDoc}
     */
    public function dispatch(string $id, ?string $secret = null)
    {
        $cacheKey = DowntimeDesk::cacheKeyName();

        Cache::lock($cacheKey.':lock', 10)->block(5, function () use ($cacheKey, $id, $secret) {
            $pings = Cache::get($cacheKey, []);

            $pings[] = [
                $id,
                $secret,
                now()->timestamp,
            ];

            Cache::put(
                key: $cacheKey,
                value: $pings,
                ttl: now()->addMinutes(5)
            );
        });
    }

    /**
     * {@inheritDoc}
     */
    public function dispatchNow(string $id, ?string $secret = null): void
    {
        $request = Http::withUserAgent('DowntimeDesk Laravel Integration');

        if (! blank($secret)) {
            $request->withHeader('X-Monitor-Secret', $secret);
        }

        $request->post(
            url: str_replace('{uuid}', $id, config('downtime-desk.base_url'))
        );
    }
}
