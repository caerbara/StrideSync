<?php

namespace Telegram\Bot\Commands;

use App\Models\User;

class BuddyMatchCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'buddy_match';

    /**
     * @var string Command Description
     */
    protected $description = 'Find running buddies with similar preferences';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $chatId = $this->update->getMessage()->getChat()->getId();

        // Find the user by telegram_id
        $currentUser = User::where('telegram_id', $chatId)->first();

        if (!$currentUser) {
            $this->replyWithMessage([
                'text' => "Please link your account first!\n\nUse /start and click Link Account",
                'parse_mode' => 'Markdown'
            ]);
            return;
        }

        // Find buddies with similar pace preferences
        $buddies = User::where('id', '!=', $currentUser->id)
            ->whereNotNull('telegram_id')
            ->whereNotNull('prefered_pace')
            ->where('prefered_pace', '!=', '')
            ->limit(5)
            ->get();

        if ($buddies->isEmpty()) {
            $this->replyWithMessage([
                'text' => "No running buddies found yet!\n\nBe the first to join StrideSync and find your perfect running partner!",
                'parse_mode' => 'Markdown'
            ]);
            return;
        }

        $message = "*Your Running Buddy Matches!*\n\n";
        $message .= "Found " . $buddies->count() . " potential buddy(ies) with your pace!\n\n";

        foreach ($buddies as $buddy) {
            $message .= "*{$buddy->name}*\n";
            $message .= "Pace: " . ($buddy->prefered_pace ?: 'Not set') . "\n";
            $message .= "PB: " . ($buddy->pb_time ?: 'Not set') . "\n";
            if ($buddy->telegram_id) {
                $message .= "Chat: [Open chat](tg://user?id={$buddy->telegram_id})\n";
            }
            $message .= "---------------------\n";
        }

        $message .= "\nConnect with them and plan your next run together!";

        $this->replyWithMessage([
            'text' => $message,
            'parse_mode' => 'Markdown'
        ]);
    }
}
?>


