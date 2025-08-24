<?php

namespace Houda\ConcurrencyLab\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class Deploy implements ShouldQueue
{
    use Dispatchable, Queueable, Batchable;

    public function __construct(
        protected string $mode = 'none' // 'none' | 'lock' | 'funnel'
    ) {}

    public function handle(): void
    {
        $sleep = (int) config('concurrency-lab.sleep_seconds', 5);
        $jobId = optional($this->job)->getJobId() ?? uniqid('deploy_', true);

        $run = function () use ($sleep, $jobId) {
            Log::info("[{$jobId}] Started Deploying...");
            sleep($sleep);
            Log::info("[{$jobId}] Finished Deploying...");
        };

        match ($this->mode) {
            'lock' => Cache::lock('deployments')->block(10, $run),

            'funnel' => Redis::funnel(config('concurrency-lab.funnel_key'))
                ->limit((int) config('concurrency-lab.funnel_limit', 5))
                ->block(10)
                ->then($run),

            default => $run(),
        };
    }
}
