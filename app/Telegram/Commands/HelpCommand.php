<?php

namespace Telegram\Bot\Commands;

class HelpCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'help';

    /**
     * @var string Command Description
     */
    protected $description = 'Show all available commands';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $message = "*StrideSync Bot Commands*\n\n";
        $message .= "*Available Commands:*\n";
        $message .= "/start - Welcome message\n";
        $message .= "/register - Register or link your account\n";
        $message .= "/sessions - View all running sessions\n";
        $message .= "/my_sessions - View your running sessions\n";
        $message .= "/join - Join a running session\n";
        $message .= "/profile - View your profile\n";
        $message .= "/help - Show this message\n\n";
        $message .= "*Quick Actions:*\n";
        $message .= "View sessions every hour\n";
        $message .= "Get notifications for new sessions\n";
        $message .= "Connect with other runners\n\n";
        $message .= "Need help? Type /start to get started!";

        $this->replyWithMessage([
            'text' => $message,
            'parse_mode' => 'Markdown'
        ]);
    }
}
?>
