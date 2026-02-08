<?php

namespace Telegram\Bot\Commands;

use App\Models\RunningSession;

class SessionsCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'sessions';

    /**
     * @var string Command Description
     */
    protected $description = 'View all running sessions';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $sessions = RunningSession::latest()->take(5)->get();

        if ($sessions->isEmpty()) {
            $this->replyWithMessage([
                'text' => 'No running sessions available at the moment.',
                'parse_mode' => 'Markdown'
            ]);
            return;
        }

        $message = "*Available Running Sessions* (Latest 5)\n\n";

        foreach ($sessions as $session) {
            $message .= "*{$session->location_name}*\n";
            $message .= ($session->user->name ?? 'Unknown') . "\n";
            $message .= $session->start_time->format('M d, H:i') . "\n";
            $message .= $session->duration . " | Pace: {$session->average_pace}\n";
            $message .= $session->joinedUsers->count() . " joined\n";
            $message .= "---------------------\n";
        }

        $message .= "\nType /join to join a session!";

        $this->replyWithMessage([
            'text' => $message,
            'parse_mode' => 'Markdown'
        ]);
    }
}
?>


