<?php

namespace FlorinMotoc\LaravelLogs;

use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class LaravelLogsServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        if (env('FM_USE_LARAVEL_LOGS_EXTRA_JOB_ID')) {
            $this->addJobId();
        }
    }

    public function addJobId()
    {
        Queue::before(function (JobProcessing $event) {
            try {
                $GLOBALS['fm_laravel_queue_job_data']['jobId'] = $event->job->getJobId();
            } catch (\Throwable $e) {
                Log::error(sprintf("LaravelLogsServiceProvider error @ before: %s @ %s @ %s", $e->getMessage(), $e->getFile(), $e->getLine()));
            }
        });

        Queue::after(function (JobProcessed $event) {
            try {
                unset($GLOBALS['fm_laravel_queue_job_data']);
            } catch (\Throwable $e) {
                Log::error(sprintf("LaravelLogsServiceProvider error @ after: %s @ %s @ %s", $e->getMessage(), $e->getFile(), $e->getLine()));
            }
        });
    }
}
