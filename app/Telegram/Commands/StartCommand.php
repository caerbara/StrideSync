<?php

namespace Telegram\Bot\Commands;

use App\Models\User;

class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'start';

    /**
     * @var string Command Description
     */
    protected $description = 'Start StrideSync Bot';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $chatId = $this->update->getMessage()->getChat()->getId();
        $firstName = $this->update->getMessage()->getChat()->getFirstName();

        $message = "Welcome to *StrideSync* Bot!\n\n";
        $message .= "Hi *{$firstName}*!\n\n";
        $message .= "I can help you with:\n";
        $message .= "/register - Register or link your account\n";
        $message .= "/sessions - View running sessions\n";
        $message .= "/join - Join a running session\n";
        $message .= "/help - Show all commands\n\n";
        $message .= "Let's get started!";

        $this->replyWithMessage([
            'text' => $message,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'Link Account', 'callback_data' => 'link_account'],
                        ['text' => 'Web App', 'url' => url('/')]
                    ],
                    [
                        ['text' => 'Help', 'callback_data' => 'help']
                    ]
                ]
            ])
        ]);
    }
}
?>


