<?php

namespace Telegram\Bot\Commands;

use App\Models\User;

class ProfileCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'profile';

    /**
     * @var string Command Description
     */
    protected $description = 'View your profile with photo';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $chatId = $this->update->getMessage()->getChat()->getId();

        // Find user by telegram_id
        $user = User::where('telegram_id', $chatId)->first();

        if (!$user) {
            $this->replyWithMessage([
                'text' => "You haven't registered yet. Please use /register to create an account.",
                'parse_mode' => 'Markdown'
            ]);
            return;
        }

        // Prepare profile information
        $profileText = "*Your Profile*\n\n";
        $profileText .= "*Name:* " . $user->name . "\n";
        $profileText .= "*Email:* " . $user->email . "\n";
        $profileText .= "*Gender:* " . ($user->gender ?? 'Not set') . "\n";
        $profileText .= "*Avg Pace:* " . ($user->avg_pace ?? 'Not set') . "\n";
        $profileText .= "*Location:* " . $user->formatLocationText('Not set') . "\n";

        // Check if user has a photo
        if ($user->photo_url) {
            // Send photo with caption
            $photoUrl = $user->photo_url;

            $this->replyWithPhoto([
                'photo' => $photoUrl,
                'caption' => $profileText,
                'parse_mode' => 'Markdown'
            ]);
        } else {
            // Send text message without photo
            $profileText .= "\n*No photo set yet*";

            $this->replyWithMessage([
                'text' => $profileText,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => 'Update Photo', 'url' => url('/')]
                        ]
                    ]
                ])
            ]);
        }
    }
}
?>


