<?php

namespace App\Http\Controllers;

use Imtigger\LaravelJobStatus\JobStatus;

class JobStatusController extends Controller
{
    public function status(JobStatus $jobStatus)
    {
        if (empty($jobStatus))
            return response()->make('No such job.', 404);

        return response()->stream(function () use ($jobStatus) {
            $safeToBreak = false;

            do {
                ob_start();

                echo 'data: ' . json_encode([
                    'id' => $jobStatus->id,
                    'status' => $jobStatus->status,
                    'ended' => $jobStatus->is_ended,
                    'finished' => $jobStatus->is_finished,
                    'progress' => $jobStatus->progress_percentage,
                ]);

                echo "\n\n";

                ob_end_flush();
                flush();

                if ($jobStatus->is_ended)
                    $safeToBreak = true;

                sleep(0.15);
            } while (! $safeToBreak && $jobStatus->refresh());
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no'
        ]);
    }
}
