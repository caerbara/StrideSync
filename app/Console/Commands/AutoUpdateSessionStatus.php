<?php

namespace App\Console\Commands;

use App\Models\RunningSession;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoUpdateSessionStatus extends Command
{
    protected $signature = 'sessions:auto-update';
    protected $description = 'Automatically start and complete sessions based on start/end time.';

    public function handle(): int
    {
        $now = Carbon::now();

        // Auto-start sessions that have reached start time.
        RunningSession::whereNull('started_at')
            ->where('start_time', '<=', $now)
            ->chunkById(200, function ($sessions) {
                foreach ($sessions as $session) {
                    $startAt = $session->start_time ?? Carbon::now();
                    $session->update(['started_at' => $startAt]);
                }
            }, 'session_id');

        // Auto-complete sessions that have passed end time.
        RunningSession::whereNull('completed_at')
            ->where('end_time', '<=', $now)
            ->chunkById(200, function ($sessions) {
                foreach ($sessions as $session) {
                    $endAt = $session->end_time ?? Carbon::now();
                    $updates = ['completed_at' => $endAt];
                    if (is_null($session->started_at)) {
                        $updates['started_at'] = $session->start_time ?? $endAt;
                    }
                    $session->update($updates);
                }
            }, 'session_id');

        return Command::SUCCESS;
    }
}


