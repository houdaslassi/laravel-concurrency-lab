<?php

namespace Houda\ConcurrencyLab\Console;

use Illuminate\Console\Command;
use Houda\ConcurrencyLab\Jobs\Deploy;

class RaceDemoCommand extends Command
{
    protected $signature = 'race:demo 
        {--jobs=10 : Number of jobs to dispatch} 
        {--mode=none : none|lock|funnel}';

    protected $description = 'Dispatch multiple Deploy jobs to demonstrate race conditions & prevention';

    public function handle(): int
    {
        $jobs = (int) $this->option('jobs');
        $mode = (string) $this->option('mode');

        $this->info("Dispatching {$jobs} jobs in [{$mode}] mode...");

        for ($i = 0; $i < $jobs; $i++) {
            dispatch(new Deploy($mode));
        }

        $this->line('Check storage/logs/laravel.log to observe execution order.');
        return self::SUCCESS;
    }
}
