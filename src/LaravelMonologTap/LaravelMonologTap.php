<?php

namespace FlorinMotoc\LaravelLogs\LaravelMonologTap;

use Illuminate\Log\Logger;

class LaravelMonologTap
{
    public function __invoke(Logger $logger)
    {
        $introspection = new \Monolog\Processor\IntrospectionProcessor(
            \Monolog\Level::Debug, // whatever level you want this processor to handle
            [
                'Monolog\\',
                'Illuminate\\',
            ]
        );

        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor($introspection);
            if (env('FM_USE_LARAVEL_LOGS_EXTRA_PID')) {
                $handler->pushProcessor([$this, 'processLogRecordAddPid']);
            }

            if (env('FM_USE_LARAVEL_LOGS_EXTRA_JOB_ID')) {
                $handler->pushProcessor([$this, 'processLogRecordAddLaravelJobId']);
            }
        }
    }

    public function processLogRecordAddPid(\Monolog\LogRecord $record): \Monolog\LogRecord
    {
        $record['extra']['pid'] = getmypid();

        return $record;
    }

    public function processLogRecordAddLaravelJobId(\Monolog\LogRecord $record): \Monolog\LogRecord
    {
        $jobId = $GLOBALS['fm_laravel_queue_job_data']['jobId'] ?? null;
        if (is_string($jobId)) {
            $record['extra']['jobId'] = $jobId;
        }

        return $record;
    }
}
