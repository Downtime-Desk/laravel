<?php

namespace DowntimeDesk\Laravel\Jobs;

use DowntimeDesk\Laravel\Facades\DowntimeDesk;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\Pool;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FlushMonitorPings implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected string $cacheKey;

    protected int $maxBatchSize;

    public function __construct()
    {
        $this->cacheKey = DowntimeDesk::cacheKeyName();
        $this->maxBatchSize = DowntimeDesk::batchSizeLimit();
        $this->onQueue(config('downtime-desk.queue'));
    }

    public function handle(): void
    {
        $lock = Cache::lock($this->cacheKey.':lock', 10);

        if (! $lock->get()) {
            return;
        }

        try {

            $pings = Cache::get($this->cacheKey, []);

            if (empty($pings)) {
                return;
            }

            $batch = array_slice($pings, 0, $this->maxBatchSize);
            $remainder = array_slice($pings, $this->maxBatchSize);

            if (empty($remainder)) {
                Cache::forget($this->cacheKey);
            } else {
                Cache::put($this->cacheKey, $remainder, now()->addMinutes(5));
            }

            $responses = Http::pool(function (Pool $pool) use ($batch) {
                foreach ($batch as $index => [$id, $secret, $timestamp]) {
                    $url = str_replace('{uuid}', $id, config('downtime-desk.base_url'));

                    $request = $pool->as("ping-{$index}");

                    if (! blank($secret)) {
                        $request->withHeader('X-Monitor-Secret', $secret);
                    }

                    $request->post(url: $url);
                }
            });

            $failed = [];
            foreach ($responses as $index => $response) {
                if ($response instanceof \Exception || $response->failed()) {
                    $i = (int) str_replace('ping-', '', $index);
                    $failed[] = $batch[$i];
                }
            }

            if (! empty($failed)) {
                $pings = Cache::get($this->cacheKey, []);
                Cache::put($this->cacheKey, array_merge($failed, $pings), now()->addMinutes(5));
            }

        } finally {
            optional($lock)->release();
        }
    }
}
