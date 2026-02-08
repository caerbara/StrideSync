<?php

namespace App\Console\Commands;

use App\Models\JoinedSession;
use App\Models\RunningSession;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SendSessionReminders extends Command
{
    protected $signature = 'sessions:send-reminders';
    protected $description = 'Send Telegram reminders 30 and 10 minutes before running sessions';

    public function handle(): int
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        if (!$token) {
            $this->warn('TELEGRAM_BOT_TOKEN is not set.');
            return 0;
        }

        $apiUrl = "https://api.telegram.org/bot{$token}";
        $now = now();
        $reminderWindows = [
            30 => [$now->copy()->addMinutes(29), $now->copy()->addMinutes(31)],
            10 => [$now->copy()->addMinutes(9), $now->copy()->addMinutes(11)],
        ];

        foreach ($reminderWindows as $minutes => [$windowStart, $windowEnd]) {
            $sessions = RunningSession::whereBetween('start_time', [$windowStart, $windowEnd])
                ->whereNull('started_at')
                ->whereNull('completed_at')
                ->get();

            foreach ($sessions as $session) {
                $cacheKey = "tg_session_reminder_sent_{$session->session_id}_{$minutes}";
                if (Cache::has($cacheKey)) {
                    continue;
                }

                $session->loadMissing('user');
                $recipients = [];
                $organizerName = $session->user ? $session->user->name : 'Organizer';

                if ($session->user && $session->user->telegram_id) {
                    $recipients[$session->user->telegram_id] = [
                        'role' => 'Organizer',
                        'name' => $session->user->name,
                    ];
                }

                $joinedUserIds = JoinedSession::where('session_id', $session->session_id)
                    ->whereIn('status', ['joined', 'accepted'])
                    ->pluck('user_id');

                if ($joinedUserIds->isNotEmpty()) {
                    $participantUsers = User::whereIn('id', $joinedUserIds)
                        ->whereNotNull('telegram_id')
                        ->get(['name', 'telegram_id']);

                    foreach ($participantUsers as $participant) {
                        $recipients[$participant->telegram_id] = [
                            'role' => 'Participant',
                            'name' => $participant->name,
                        ];
                    }
                }

                if (empty($recipients)) {
                    Cache::put($cacheKey, true, $windowEnd->copy()->addHours(2));
                    continue;
                }

                $locationName = $session->location_name ?? 'Unknown';
                if (preg_match('/^Lat\s*-?\d+(?:\.\d+)?,\s*Lng\s*-?\d+(?:\.\d+)?$/i', $locationName)) {
                    $locationName = 'Location not set';
                }

                $startTime = $session->start_time ? $session->start_time->format('Y-m-d H:i') : 'soon';
                foreach ($recipients as $chatId => $info) {
                    $message = "<b>Session Reminder</b>\n";
                    $message .= "Role: {$info['role']}\n";
                    $message .= "Your run starts in about {$minutes} minutes.\n";
                    $message .= "Organizer: {$organizerName}\n";
                    $message .= "Location: {$locationName}\n";
                    $message .= "Start: {$startTime}";

                    Http::withoutVerifying()->post("{$apiUrl}/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => $message,
                        'parse_mode' => 'HTML',
                    ]);
                }

                Cache::put($cacheKey, true, $windowEnd->copy()->addHours(2));
            }
        }

        // After session ends, remind participants to leave a review.
        $endWindowStart = $now->copy()->subMinutes(1);
        $endWindowEnd = $now->copy()->addMinutes(1);
        $endedSessions = RunningSession::whereBetween('end_time', [$endWindowStart, $endWindowEnd])
            ->get();

        foreach ($endedSessions as $session) {
            $cacheKey = "tg_session_review_reminder_sent_{$session->session_id}";
            if (Cache::has($cacheKey)) {
                continue;
            }

            $session->loadMissing('user');
            $recipients = [];
            if ($session->user && $session->user->telegram_id) {
                $recipients[$session->user->telegram_id] = [
                    'role' => 'Organizer',
                    'name' => $session->user->name,
                ];
            }

            $joinedUserIds = JoinedSession::where('session_id', $session->session_id)
                ->whereIn('status', ['joined', 'accepted'])
                ->pluck('user_id');

            if ($joinedUserIds->isNotEmpty()) {
                $participantUsers = User::whereIn('id', $joinedUserIds)
                    ->whereNotNull('telegram_id')
                    ->get(['name', 'telegram_id']);

                foreach ($participantUsers as $participant) {
                    $recipients[$participant->telegram_id] = [
                        'role' => 'Participant',
                        'name' => $participant->name,
                    ];
                }
            }

            if (empty($recipients)) {
                Cache::put($cacheKey, true, $endWindowEnd->copy()->addHours(6));
                continue;
            }

            $locationName = $session->location_name ?? 'Unknown';
            if (preg_match('/^Lat\s*-?\d+(?:\.\d+)?,\s*Lng\s*-?\d+(?:\.\d+)?$/i', $locationName)) {
                $locationName = 'Location not set';
            }
            $startTime = $session->start_time ? $session->start_time->format('Y-m-d H:i') : 'N/A';

            foreach ($recipients as $chatId => $info) {
                $message = "<b>Session Completed</b>\n";
                $message .= "Role: {$info['role']}\n";
                $message .= "Thanks for joining the session.\n";
                $message .= "Location: {$locationName}\n";
                $message .= "Start: {$startTime}\n\n";
                $message .= "Please leave your review when you have a moment.";

                Http::withoutVerifying()->post("{$apiUrl}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ]);
            }

            Cache::put($cacheKey, true, $endWindowEnd->copy()->addHours(6));
        }

        return 0;
    }
}


