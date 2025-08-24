<?php

namespace Houda\ConcurrencyLab;

use Illuminate\Support\ServiceProvider;
use Houda\ConcurrencyLab\Console\RaceDemoCommand;

class ConcurrencyLabServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/concurrency-lab.php', 'concurrency-lab');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/concurrency-lab.php' => config_path('concurrency-lab.php'),
            ], 'concurrency-lab-config');

            $this->commands([RaceDemoCommand::class]);
        }
    }
}
