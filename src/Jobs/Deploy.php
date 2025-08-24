<?php

namespace Houda\ConcurrencyLab\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class Deploy implements ShouldQueue
{
    use Dispatchable, Queueable, Batchable, InteractsWithQueue;

    public function __construct(
        protected string $mode = 'none' // 'none' | 'lock' | 'funnel'
    ) {}

    public function handle(): void
    {
        $sleep = (int) config('concurrency-lab.sleep_seconds', 5);

        // ✅ Safely get a job id whether queued or run inline
        $jobId = method_exists($this->job ?? null, 'getJobId')
            ? $this->job->getJobId()
            : Str::uuid()->toString();

        $run = function () use ($sleep, $jobId) {
            Log::info("[{$jobId}] Started Deploying...");
            sleep($sleep);
            Log::info("[{$jobId}] Finished Deploying...");
        };

        match ($this->mode) {
            'lock' => Cache::lock('deployments')->block(10, $run),

            'funnel' => Redis::funnel(config('concurrency-lab.funnel_key', 'deployments'))
                ->limit((int) config('concurrency-lab.funnel_limit', 5))
                ->block(10)
                ->then($run),

            default => $run(),
        };
    }
}
