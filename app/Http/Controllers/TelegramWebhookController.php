<?php

namespace App\Http\Controllers;

use App\Models\BuddyLike;
use App\Models\User;
use App\Models\UserReport;
use App\Models\RunningSession;
use App\Models\JoinedSession;
use App\Models\SessionReview;
use App\Services\CloudinaryService;
use App\Services\GeocodingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TelegramWebhookController extends Controller
{
    private $token;
    private $apiUrl;

    public function __construct()
    {
        $this->token = env('TELEGRAM_BOT_TOKEN');
        $this->apiUrl = "https://api.telegram.org/bot{$this->token}";
    }

    // ==========================================
    // === 1. MAIN WEBHOOK HANDLER ===
    // ==========================================

    public function handle(Request $request)
    {
        $update = $request->all();
        Log::info('Telegram update received', [
            'has_message' => isset($update['message']),
            'has_callback' => isset($update['callback_query']),
        ]);

        try {
            if (isset($update['message'])) {
                $this->handleMessage($update['message']);
            }

            if (isset($update['callback_query'])) {
                $this->handleCallbackQuery($update['callback_query']);
            }
        } catch (\Exception $e) {
            Log::error('Bot Error: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
        }

        return response()->json(['ok' => true]);
    }

    // ==========================================
    // === 2. MESSAGE HANDLER ===
    // ==========================================

        private function handleMessage($message)
    {
        $chatId = $message['chat']['id'];
        $text = trim($message['text'] ?? '');
        $telegramUserId = $message['from']['id'] ?? null;
        $firstName = $message['from']['first_name'] ?? 'Friend';
        $telegramUsername = $message['from']['username'] ?? null;

        $user = User::where('telegram_id', $telegramUserId)->first();
        $hasTempEmail = $user && substr($user->email, -strlen('@stridesync.local')) === '@stridesync.local';

        if (!$user || $hasTempEmail) {
            $email = strtolower(trim($text));
            if ($this->isEmailLinkPending($chatId) || filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->sendMessage($chatId, 'Please enter a valid email address used on StrideSync.');
                    return;
                }

                $existing = User::whereRaw('LOWER(email) = ?', [$email])->first();
                if (!$existing) {
                    $this->sendMessage($chatId, "No account found for that email. Please register first:\nhttps://stridesync.app/register");
                    return;
                }

                if ($existing->telegram_id && $existing->telegram_id !== $telegramUserId) {
                    $this->sendMessage($chatId, 'That account is already linked to another Telegram user.');
                    return;
                }

                if ($user && $hasTempEmail && $user->id !== $existing->id) {
                    $user->update([
                        'telegram_id' => null,
                        'telegram_state' => 'unlinked',
                    ]);
                }

                $existing->update([
                    'telegram_id' => $telegramUserId,
                    'telegram_state' => $this->computeTelegramState($existing),
                    'telegram_username' => $telegramUsername,
                ]);

                $this->clearEmailLinkPending($chatId);
                $this->sendMessage($chatId, "Linked successfully, {$existing->name}! Send /start to open the menu.");
                $this->showMainMenu($chatId, $existing);
                return;
            }

            $this->setEmailLinkPending($chatId);
            $this->sendMessage($chatId, "Hi {$firstName}! Please reply with the email you used on StrideSync to link your account.");
            $this->sendMessage($chatId, "No account yet? Register here:\nhttps://stridesync.app/register");
            return;
        }

        $this->syncTelegramState($user);
        if ($telegramUsername && $user->telegram_username !== $telegramUsername) {
            $user->update(['telegram_username' => $telegramUsername]);
        }
        $sessionFlow = $this->getSessionFlow($user);
        $savedLocationLabel = $this->getSavedLocationLabel($user);

        if ($text !== '' && (strcasecmp($text, 'Cancel') === 0 || strpos($text, '/cancel') === 0)) {
            $this->clearSessionFlow($user);
            $this->clearDeactivatePending($user);
            $this->clearReportPending($user);
            $this->clearReviewPending($user);
            $this->clearLikeInbox($user);
            $this->clearLocationUpdatePending($user);
            $this->clearPhotoUpdatePending($user);
            $this->removeReplyKeyboard($chatId);
            $this->showMainMenu($chatId, $user);
            return;
        }

        // Handle location share
        if (isset($message['location'])) {
            if ($sessionFlow && in_array(($sessionFlow['step'] ?? ''), ['location', 'session_location_choice'], true)) {
                $this->handleSessionLocationInput($chatId, $user, $message['location'], $sessionFlow);
                return;
            }

            $this->handleLocationShare($chatId, $user, $message['location']);
            return;
        }

        if (isset($message['photo'])) {
            $this->handleProfilePhoto($chatId, $user, $message['photo']);
            return;
        }

        $locationStep = $sessionFlow && in_array(($sessionFlow['step'] ?? ''), ['location', 'session_location_choice'], true);
        if ($text !== '' && ($user->telegram_state === 'waiting_location' || $this->isLocationUpdatePending($user) || $locationStep)) {
            $isSavedLocation = $savedLocationLabel && $text === $savedLocationLabel;
            $label = $sessionFlow['data']['location_button_label'] ?? null;
            $isLocationChoice = $locationStep && ($sessionFlow['step'] ?? '') === 'session_location_choice' && $label && $text === $label;

            if (!$isSavedLocation && !$isLocationChoice) {
                if ($locationStep) {
                    $this->clearSessionFlow($user);
                }
                $this->clearLocationUpdatePending($user);
                $this->removeReplyKeyboard($chatId);
                $this->showMainMenu($chatId, $user);
                return;
            }
        }

        if ($user->telegram_state === 'waiting_photo') {
            $this->sendMessage($chatId, 'Please upload your profile photo to continue.');
            return;
        }

        if (stripos($text, 'deactivate') !== false || strpos($text, '/deactivate') === 0) {
            $this->clearSessionFlow($user);
            $this->setDeactivatePending($user);
            $this->showDeactivateConfirm($chatId);
            return;
        }

        if ($this->isDeactivatePending($user)) {
            if (strcasecmp($text, 'Confirm Deactivate') === 0) {
                $this->clearDeactivatePending($user);
                $user->update([
                    'telegram_id' => null,
                    'telegram_state' => 'unlinked'
                ]);
                $this->sendMessage($chatId, 'Your Telegram account has been unlinked. Send /start if you want to link again.');
                return;
            }

            if (strcasecmp($text, 'Cancel') === 0 || strcasecmp($text, 'Cancel Deactivate') === 0) {
                $this->clearDeactivatePending($user);
                $this->sendMessage($chatId, "Deactivation canceled. You're still linked.");
                return;
            }
        }

        $reportPending = $this->getReportPending($user);
        if ($reportPending) {
            $normalizedText = trim(strtolower($text));
            if (($reportPending['step'] ?? 'select') === 'custom_reason') {
                if ($normalizedText === 'cancel') {
                    $this->clearReportPending($user);
                    $this->sendMessage($chatId, 'Report canceled.');
                    $this->showMainMenu($chatId, $user);
                    return;
                }

                $target = User::find($reportPending['target_id'] ?? null);
                if (!$target) {
                    $this->clearReportPending($user);
                    $this->sendMessage($chatId, 'The reported user is no longer available.');
                    $this->showMainMenu($chatId, $user);
                    return;
                }

                $reason = trim($text);
                if ($reason === '') {
                    $this->sendMessage($chatId, 'Please type a reason or type cancel.');
                    return;
                }

                $this->notifyAdminsReport($user, $target, $reason);
                $this->clearReportPending($user);
                $this->sendMessage($chatId, 'Report submitted to admins.');
                $this->showMainMenu($chatId, $user);
                return;
            }

            if ($normalizedText === 'cancel') {
                $this->clearReportPending($user);
                $this->sendMessage($chatId, 'Report canceled.');
                $this->showMainMenu($chatId, $user);
                return;
            }

            $reasons = $this->getReportReasons();
            $selectedReason = null;
            foreach ($reasons as $reason) {
                if (strtolower($reason) === $normalizedText) {
                    $selectedReason = $reason;
                    break;
                }
            }
            if (!$selectedReason && preg_match('/^\s*([0-9]+)/', $text, $matches)) {
                $choice = (int) $matches[1];
                if (isset($reasons[$choice - 1])) {
                    $selectedReason = $reasons[$choice - 1];
                }
            }
            if (!$selectedReason) {
                $this->sendMessage($chatId, 'Please choose a reason using the buttons.');
                return;
            }

            $target = User::find($reportPending['target_id'] ?? null);
            if (!$target) {
                $this->clearReportPending($user);
                $this->sendMessage($chatId, 'The reported user is no longer available.');
                $this->showMainMenu($chatId, $user);
                return;
            }

            $this->clearReportPending($user);
            $this->notifyAdminsReport($user, $target, $selectedReason);
            Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => 'Report submitted. Thank you for helping keep the community safe.',
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode(['remove_keyboard' => true]),
            ]);
            $this->showMainMenu($chatId, $user);
            return;
        }

        $reviewPending = $this->getReviewPending($user);
        if ($reviewPending && ($reviewPending['step'] ?? '') === 'comment') {
            $comment = trim($text);
            if ($comment === '' || stripos($comment, 'cancel') !== false) {
                $this->clearReviewPending($user);
                $this->sendMessage($chatId, 'Review canceled.');
                $this->showMainMenu($chatId, $user);
                return;
            }

            $sessionId = (int) $reviewPending['session_id'];
            $rating = (int) $reviewPending['rating'];
            $saved = $this->storeTelegramReview($user, $sessionId, $rating, $comment);
            $this->clearReviewPending($user);
            if ($saved) {
                $this->notifyAdminsReview($user, $sessionId, $rating, $comment);
                $this->showMainMenu($chatId, $user, 'Thanks for your review! Your review has been submitted.');
            } else {
                $this->showMainMenu($chatId, $user, 'Unable to submit review.');
            }
            return;
        }

        $likeInbox = $this->getLikeInbox($user);
        if (!empty($likeInbox)) {
            $normalizedText = trim(strtolower($text));
            if (strpos($normalizedText, 'show') !== false) {
                $likerId = $this->popLikeInbox($user);
                if ($likerId) {
                    $liker = User::find($likerId);
                    if ($liker) {
                        $this->sendLikedProfile($chatId, $user, $liker);
                    } else {
                        $this->sendMessage($chatId, 'That profile is no longer available.');
                    }
                } else {
                    $this->sendMessage($chatId, 'No more likes to show right now.');
                }

                Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => 'Use /start to return to the menu.',
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode(['remove_keyboard' => true]),
                ]);
                return;
            }

            if (strpos($normalizedText, 'not searching') !== false) {
                $this->clearLikeInbox($user);
                Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => 'Okay. You can resume anytime from the menu.',
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode(['remove_keyboard' => true]),
                ]);
                $this->showMainMenu($chatId, $user);
                return;
            }

            $this->sendMessage($chatId, 'Please choose one of the options using the buttons.');
            return;
        }

        if ($sessionFlow) {
            if (strpos($text, '/start') === 0) {
                $this->clearSessionFlow($user);
                $this->sendMessage($chatId, "Welcome back, <b>{$user->name}</b>!");
                $this->showMainMenu($chatId, $user);
                return;
            }

            if (strcasecmp($text, 'Cancel') === 0 || strpos($text, '/cancel') === 0) {
                $this->clearSessionFlow($user);
                $this->sendMessage($chatId, 'Session creation canceled.');
                $this->showMainMenu($chatId, $user);
                return;
            }

            if (($sessionFlow['step'] ?? '') === 'session_location_choice') {
                $label = $sessionFlow['data']['location_button_label'] ?? null;
                if ($label && $text === $label) {
                    $sessionFlow['step'] = 'session_day';
                    $this->setSessionFlow($user, $sessionFlow);
                    $this->sendSessionDayOptions($chatId, 'Choose the day for your run:');
                    return;
                }

                $this->sendMessage($chatId, 'Please use the buttons to choose a location.');
                return;
            }

            $this->handleSessionCreationStep($chatId, $user, $text, $sessionFlow);
            return;
        }

        if ($savedLocationLabel && $text === $savedLocationLabel) {
            if ($user->telegram_state === 'waiting_location' || $this->isLocationUpdatePending($user)) {
                $this->clearLocationUpdatePending($user);
                $this->syncTelegramState($user);
                $this->sendMessage($chatId, 'Location updated.');
                $this->showMainMenu($chatId, $user);
                return;
            }
        }

        // Quick command to open the run session page on the web
        if (stripos($text, 'run session') === 0 || strpos($text, '/runsession') === 0) {
            $this->showRunSessionLink($chatId);
            return;
        }

        // Route by command/state
        $normalized = mb_strtolower(trim($text));

        if (strpos($text, '/start') === 0) {
            $this->sendMessage($chatId, "Welcome back, <b>{$user->name}</b>!");
            $this->showMainMenu($chatId, $user);
        } elseif (strpos($normalized, 'my profile') !== false || strpos($normalized, 'manage your profile') !== false || strpos($text, '/profile') === 0) {
            $this->showMyProfile($chatId, $user);
        } elseif (strpos($normalized, 'my schedule') !== false || strpos($normalized, 'timetable') !== false || strpos($text, '/schedule') === 0) {
            $this->showWeeklySchedule($chatId, $user);
        } elseif (strpos($normalized, 'find buddy') !== false || strpos($normalized, 'discover runners') !== false || preg_match('/^view profiles?$/i', $text)) {
            $this->showFindBuddy($chatId, $user);
        } elseif (strpos($normalized, 'buddy requests') !== false || strcasecmp($text, 'Check Invitations') === 0) {
            $this->showCheckInvitations($chatId, $user);
        } elseif (strpos($normalized, 'running sessions') !== false || strpos($normalized, 'join nearby run') !== false) {
            $this->showRunningSessions($chatId, $user);
        } elseif (strpos($normalized, 'create running session') !== false || strcasecmp($text, 'Create Session') === 0 || strpos($text, '/createsession') === 0) {
            $this->startSessionCreation($chatId, $user);
        } elseif (strpos($normalized, 'review') !== false || strpos($normalized, 'feedback') !== false || strpos($text, '/review') === 0) {
            $this->showReviewOptions($chatId, $user);
        } elseif ($user->telegram_state === 'waiting_gender') {
            $this->handleGenderInput($chatId, $user, $text);
        } elseif ($user->telegram_state === 'waiting_pace') {
            $this->handlePaceInput($chatId, $user, $text);
        } else {
            $this->sendMessage($chatId, "I didn't understand that command.\nTry /start to see the menu.");
        }
    }

    // ==========================================
    // === 3. MAIN MENU ===
    // ==========================================

    private function showMainMenu($chatId, $user, ?string $notice = null)
    {
        $state = $this->syncTelegramState($user);
        $isProfileComplete = $state === 'profile_complete';

        if (!$isProfileComplete) {
            if ($notice) {
                $this->sendMessage($chatId, $notice);
            }
            $intro = "<b>StrideSyncBot</b>\n";
            $intro .= "Connect with runners near you, create running sessions, and join runs around your area. Find a running buddy, sync your pace, and stay motivated together.\n\n";
            $intro .= "- Discover runners near you\n";
            $intro .= "- Create running sessions\n";
            $intro .= "- Join nearby runs\n";
            $intro .= "- Manage your running profile\n\n";
            $intro .= "Stay consistent, stay social, and run smarter.";

            $this->sendMessage($chatId, $intro);
            $this->sendMessage($chatId, "Welcome to <b>StrideSync</b>!\n\nLet's complete your profile first.");
            $this->promptForProfileCompletion($chatId, $user, $state);
            $this->sendMessage($chatId, 'Complete your profile to unlock all bot features.');
        } else {
            $message = "<b>StrideSyncBot</b>\n";
            $message .= "Connect with runners near you, create running sessions, and join runs around your area. Find a running buddy, sync your pace, and stay motivated together.\n\n";
            $message .= "- Discover runners near you\n";
            $message .= "- Create running sessions\n";
            $message .= "- Join nearby runs\n";
            $message .= "- Manage your running profile\n\n";
            $message .= "Stay consistent, stay social, and run smarter.";
            if ($notice) {
                $message = "<b>{$notice}</b>\n\n" . $message;
            }

              $inlineKeyboard = [
                  'inline_keyboard' => [
                      [
                          ['text' => 'My profile', 'callback_data' => 'menu_profile'],
                      ],
                      [
                          ['text' => 'My schedule', 'callback_data' => 'menu_schedule'],
                      ],
                      [
                          ['text' => 'Find buddy', 'callback_data' => 'menu_find_buddy'],
                      ],
                    [
                        ['text' => 'Buddy requests', 'callback_data' => 'menu_buddy_requests'],
                    ],
                    [
                        ['text' => 'Join nearby run', 'callback_data' => 'menu_join_nearby'],
                    ],
                    [
                        ['text' => 'Create running session', 'callback_data' => 'menu_create_session'],
                    ],
                    [
                        ['text' => 'Leave review', 'callback_data' => 'menu_review'],
                    ],
                ],
            ];

            Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode($inlineKeyboard),
            ]);
            $this->removeReplyKeyboard($chatId);
        }
    }

    // ==========================================
    // === 4. PROFILE MANAGEMENT ===
    // ==========================================

    private function showMyProfile($chatId, $user)
    {
        // Get location details
        $locationText = $user->formatLocationText('Not set');

        // Format profile info
        $profileInfo = "<b>{$user->name}</b>\n\n";
        $profileInfo .= "Gender: " . ($user->gender ?? 'Not set') . "\n";
        $profileInfo .= "Avg Pace: " . ($user->avg_pace ?? 'Not set') . "\n";
        $profileInfo .= "Location: {$locationText}\n";

        // Option to edit profile
        $inlineKeyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'Edit Gender', 'callback_data' => 'edit_gender'],
                    ['text' => 'Edit Pace', 'callback_data' => 'edit_pace'],
                ],
                [
                    ['text' => 'Update Location', 'callback_data' => 'edit_location'],
                    ['text' => 'Update Photo', 'callback_data' => 'edit_photo'],
                ],
                [
                    ['text' => 'Delete Photo', 'callback_data' => 'delete_photo'],
                ],
                [
                    ['text' => 'Deactivate Account', 'callback_data' => 'menu_deactivate'],
                ],
                [
                    ['text' => 'Main Menu', 'callback_data' => 'menu_main'],
                ],
            ]
        ];

        // Send photo with profile info as caption
        $photoUrl = $this->getUserPhotoUrl($user);
        $photoSent = false;
        if ($photoUrl && strpos($photoUrl, 'https://') === 0) {
            $response = $this->sendPhotoUrl($chatId, $photoUrl, $profileInfo, $inlineKeyboard);
            $photoSent = $response && $response->ok();
        }

        if (!$photoSent) {
            // If no photo or sendPhoto failed, send text message with buttons
            $profileInfo .= "\n<i>No photo set yet</i>";
            Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $profileInfo,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode($inlineKeyboard)
            ]);
        }
    }

    private function handleGenderInput($chatId, $user, $text)
    {
        // This should not be called from text, gender is handled via callback
        // But keeping for safety
        $text = strtolower($text);
        if (in_array($text, ['male', 'female', 'other'])) {
            $user->update(['gender' => ucfirst($text), 'telegram_state' => 'waiting_pace']);
            $this->sendPaceOptions($chatId, "Select your average running pace:");
        } else {
            $this->sendMessage($chatId, "Please reply with: male, female, or other");
        }
    }

    private function handlePaceInput($chatId, $user, $pace)
    {
        $user->update(['avg_pace' => trim($pace), 'telegram_state' => 'waiting_photo']);
        $this->setPhotoUpdatePending($user);
        $this->sendMessage($chatId, 'Great! Now upload your profile photo to complete your profile.');
    }

    private function handleLocationShare($chatId, $user, $location)
    {
        $lat = $location['latitude'];
        $lon = $location['longitude'];
        $geo = app(GeocodingService::class);
        $resolved = $geo->reverseGeocodeCityState((float) $lat, (float) $lon);
        $state = $resolved['state'] ?? null;
        $city = $resolved['city'] ?? null;
        if (!$state) {
            $state = $this->resolveStateFromCoords($lat, $lon);
        }

        $canUpdate = $user->telegram_state === 'waiting_location' || $this->isLocationUpdatePending($user);

        // Store location as JSON
        $user->update([
            'location' => json_encode([
                'latitude' => $lat,
                'longitude' => $lon,
                'city' => $city,
                'state' => $state,
                'updated_at' => now()->toDateTimeString()
            ])
        ]);
        $this->clearLocationUpdatePending($user);

        $state = $this->syncTelegramState($user);
        if ($state === 'profile_complete') {
            $message = $canUpdate ? 'Location updated.' : "Profile updated. You're ready to find running buddies!";
            $this->sendMessage($chatId, $message);
        }
        $this->showMainMenu($chatId, $user);
    }

    private function handleProfilePhoto($chatId, $user, $photos)
    {
        if ($user->telegram_state !== 'waiting_photo' && !$this->isPhotoUpdatePending($user)) {
            $this->sendMessage($chatId, 'To update your photo, open your profile and tap Update Photo.');
            return;
        }

        $photo = end($photos);
        $fileId = $photo['file_id'] ?? null;

        if (!$fileId) {
            $this->sendMessage($chatId, 'Photo not recognized. Please try again.');
            return;
        }

        $fileResponse = Http::withoutVerifying()->get("{$this->apiUrl}/getFile", [
            'file_id' => $fileId
        ]);

        if (!$fileResponse->ok() || empty($fileResponse['result']['file_path'])) {
            $this->sendMessage($chatId, 'Failed to fetch photo. Please try again.');
            return;
        }

        $filePath = $fileResponse['result']['file_path'];
        $fileUrl = "https://api.telegram.org/file/bot{$this->token}/{$filePath}";
        $fileContents = Http::withoutVerifying()->get($fileUrl)->body();

        if ($fileContents === '' || $fileContents === null) {
            $this->sendMessage($chatId, 'Failed to download photo. Please try again.');
            return;
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION) ?: 'jpg';
        $filename = 'telegram_' . $user->id . '_' . time() . '.' . $extension;
        $cloudinary = app(CloudinaryService::class);
        $cloudinaryUrl = $cloudinary->uploadBytes($fileContents, $filename, 'stridesync/telegram_profiles');

        if ($cloudinaryUrl) {
            $storagePath = $cloudinaryUrl;
        } else {
            $storagePath = 'telegram_profiles/' . $filename;
            Storage::disk('public')->put($storagePath, $fileContents);
        }

        $user->update([
            'strava_screenshot' => $storagePath,
            'telegram_state' => 'profile_complete'
        ]);
        $this->clearPhotoUpdatePending($user);

        $photoUrl = $this->getUserPhotoUrl($user);
        if ($photoUrl) {
            $this->sendPhotoUrl($chatId, $photoUrl, 'Your profile photo');
        }

            $this->sendMessage($chatId, 'Photo saved successfully.');
            $this->sendMessage($chatId, 'Your profile is complete. Choose an option below.');
            $this->sendMenuMessage($chatId);
            $this->removeReplyKeyboard($chatId);
        }

    // ==========================================
    // === 5. FIND BUDDY FEATURE ===
    // ==========================================

    private function showFindBuddy($chatId, $user)
    {
        $userCoords = $this->extractCoordinates($user->location);
        $userLocationData = $user->location ? json_decode($user->location, true) : null;
        if (!$userCoords) {
            Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => 'Please share your current location so I can find nearby runners.',
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => 'My profile', 'callback_data' => 'menu_profile'],
                            ['text' => 'My schedule', 'callback_data' => 'menu_schedule'],
                        ],
                        [
                            ['text' => 'Find buddy', 'callback_data' => 'menu_find_buddy'],
                            ['text' => 'Buddy requests', 'callback_data' => 'menu_buddy_requests'],
                        ],
                        [
                            ['text' => 'Join nearby run', 'callback_data' => 'menu_join_nearby'],
                            ['text' => 'Create running session', 'callback_data' => 'menu_create_session'],
                        ],
                    ],
                ]),
            ]);
            return;
        }
        $userState = is_array($userLocationData) ? ($userLocationData['state'] ?? null) : null;
        $userState = $userState ? strtolower(trim($userState)) : null;

        if (!$userState) {
            $resolved = $this->resolveStateFromCoords($userCoords['lat'], $userCoords['lon']);
            if ($resolved) {
                $location = is_array($userLocationData) ? $userLocationData : [];
                $location['state'] = $resolved;
                $location['updated_at'] = now()->toDateTimeString();
                $user->update(['location' => json_encode($location)]);
                $userState = strtolower(trim($resolved));
            }
        }
        if (!$userState) {
            $this->sendMessage($chatId, 'Please share your location again so I can detect your state.');
            return;
        }

        $candidates = User::where('telegram_id', '!=', $user->telegram_id)
            ->whereNotNull('telegram_id')
            ->whereNotNull('location')
            ->where('telegram_state', 'profile_complete')
            ->whereNotNull('avg_pace')
            ->get();

        $queue = $candidates
            ->map(function ($buddy) use ($userCoords, $userState) {
                $buddyCoords = $this->extractCoordinates($buddy->location);
                if (!$buddyCoords) {
                    return null;
                }

                $buddyLocationData = $buddy->location ? json_decode($buddy->location, true) : null;
                $buddyState = is_array($buddyLocationData) ? ($buddyLocationData['state'] ?? null) : null;
                $buddyState = $buddyState ? strtolower(trim($buddyState)) : null;
                if ($buddyState !== $userState) {
                    return null;
                }

                $distance = $this->calculateDistance(
                    $userCoords['lat'],
                    $userCoords['lon'],
                    $buddyCoords['lat'],
                    $buddyCoords['lon']
                );

                return [
                    'id' => $buddy->id,
                    'distance' => $distance
                ];
            })
            ->filter()
            ->sortBy('distance')
            ->values()
            ->take(20)
            ->all();

        if (empty($queue)) {
            $this->sendMessage($chatId, 'No runners found in your state yet. Try again later.');
            $this->showMainMenu($chatId, $user);
            return;
        }

        $this->setBuddyQueue($user, [
            'items' => $queue,
            'index' => 0,
        ]);

        $this->sendMessage($chatId, "<b>Nearby Runners</b>\n\nSwipe through runners and choose Like or Dislike.");
        $this->showNextBuddy($chatId, $user);
    }
    // ==========================================
    // === 6. CHECK INVITATIONS ===
    // ==========================================

    private function showCheckInvitations($chatId, $user)
    {
        // Find invitations sent to this user (received invitations)
        $invitations = \DB::table('joined_sessions')
            ->join('users', 'joined_sessions.user_id', '=', 'users.id')
            ->where('joined_sessions.invited_user_id', $user->id)
            ->select('joined_sessions.*', 'users.name', 'users.avg_pace')
            ->get();

        if ($invitations->isEmpty()) {
            $this->sendMessage($chatId, "No new buddy requests at the moment.");
            $this->showMainMenu($chatId, $user);
            return;
        }

        $this->sendMessage($chatId, "<b>Buddy Requests</b>\n\nPeople interested in running with you:");

        foreach ($invitations as $invitation) {
            $inviteInfo = "<b>{$invitation->name}</b>\n";
            $inviteInfo .= "Pace: {$invitation->avg_pace}\n";
            $inviteInfo .= "Wants to run with you!";

            $inlineKeyboard = [
                'inline_keyboard' => [
                    [
                          ['text' => 'Accept', 'callback_data' => "accept_invite_{$invitation->id}"],
                          ['text' => 'Decline', 'callback_data' => "decline_invite_{$invitation->id}"],
                          ['text' => 'Report', 'callback_data' => "report_invite_{$invitation->id}"],
                    ]
                ]
            ];

            Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $inviteInfo,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode($inlineKeyboard)
            ]);
        }
    }

    // ==========================================
    // === 6.5 WEEKLY SCHEDULE ===
    // ==========================================

    private function showWeeklySchedule($chatId, $user)
    {
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $sessions = RunningSession::whereBetween('start_time', [$weekStart, $weekEnd])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('joinedUsers', fn ($qq) => $qq->where('user_id', $user->id));
            })
            ->orderBy('start_time')
            ->get();

        if ($sessions->isEmpty()) {
            $this->sendMessage($chatId, 'No sessions scheduled for this week.');
            $this->showMainMenu($chatId, $user);
            return;
        }

        $message = "<b>Your Weekly Schedule</b>\n";
        $message .= $weekStart->format('M d') . ' - ' . $weekEnd->format('M d, Y') . "\n\n";

        // Send header
        $this->sendMessage($chatId, trim($message));

        foreach ($sessions as $session) {
            $hasConflict = $sessions->contains(function ($other) use ($session) {
                if ($other->session_id === $session->session_id) {
                    return false;
                }
                return $session->start_time < $other->end_time && $session->end_time > $other->start_time;
            });

            $start = $session->start_time ? $session->start_time->format('D, M d h:i A') : 'TBD';
            $end = $session->end_time ? $session->end_time->format('h:i A') : 'TBD';
            $location = $session->location_name ?? 'Not set';
            $role = $session->user_id === $user->id ? 'Organizer' : 'Participant';
            $conflictText = $hasConflict ? ' OVERLAP' : '';

            $sessionText = "<b>{$start} - {$end}</b>{$conflictText}\n";
            $sessionText .= "Location: {$location}\n";
            $sessionText .= "Role: {$role}\n";

            $buttons = [];
            if ($role === 'Participant') {
                $buttons[] = ['text' => 'Unjoin', 'callback_data' => "unjoin_session_{$session->session_id}"];
            }

            $inlineKeyboard = $buttons ? ['inline_keyboard' => [ $buttons, [ ['text' => 'Main Menu', 'callback_data' => 'menu_main'], ], ],] : ['inline_keyboard' => [ [ ['text' => 'Main Menu', 'callback_data' => 'menu_main'], ], ],];

            Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => trim($sessionText),
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode($inlineKeyboard),
            ]);
        }
    }

    // ==========================================
    // === 7. RUNNING SESSIONS (NEARBY) ===
    // ==========================================

        private function showRunningSessions($chatId, $user)
    {
        $userLocationData = $user->location ? json_decode($user->location, true) : null;
        $userState = is_array($userLocationData) ? ($userLocationData['state'] ?? null) : null;
        if (!$userState) {
            $this->sendMessage($chatId, 'Please share your location so I can show nearby sessions in your state.');
            $this->sendLocationRequest($chatId, 'Share your current location to find nearby runs.', $user);
            return;
        }

        $sessions = RunningSession::with(['user'])->withCount('joinedUsers')
            ->where('end_time', '>=', Carbon::now())
            ->orderBy('start_time', 'asc')
            ->get()
            ->filter(function ($session) use ($userState) {
                $location = $session->location_name ?? '';
                return $location !== '' && stripos($location, $userState) !== false;
            })
            ->take(10);

        if ($sessions->isEmpty()) {
            $message = "No running sessions found in {$userState} right now.\n";
            $message .= "Create a session so others can join you!";
            $this->sendMessage($chatId, $message);
            $this->showMainMenu($chatId, $user);
            return;
        }

        $this->sendMessage($chatId, "Running sessions:\nTap Join to participate.");

        foreach ($sessions as $session) {
            $start = $session->start_time ? $session->start_time->format('Y-m-d H:i') : 'N/A';
            $end = $session->end_time ? $session->end_time->format('Y-m-d H:i') : 'N/A';
            $joinedCount = $session->joined_users_count ?? 0;
            $locationName = $session->location_name ?? 'Unknown';

            if (preg_match('/^Lat\\s*-?\\d+(?:\\.\\d+)?,\\s*Lng\\s*-?\\d+(?:\\.\\d+)?$/i', $locationName)) {
                $locationName = 'Location not set';
            }

            $organizerName = $session->user ? $session->user->name : 'Organizer';
            $sessionInfo = "<b>{$locationName}</b>\n";
            $sessionInfo .= "Organizer: {$organizerName}\n";
            $sessionInfo .= "Start: {$start}\n";
            $sessionInfo .= "End: {$end}\n";
            $sessionInfo .= "Pace: {$session->average_pace}\n";
            $sessionInfo .= "Duration: {$session->duration}\n";
            $sessionInfo .= "Joined: {$joinedCount}";

            $inlineKeyboard = [];
            if ($session->user_id !== $user->id) {
                $inlineKeyboard = [
                    'inline_keyboard' => [
                        [
                              ['text' => 'Join Session', 'callback_data' => "join_session_{$session->session_id}"],
                        ]
                    ]
                ];
            }

            Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $sessionInfo,
                'parse_mode' => 'HTML',
                'reply_markup' => $inlineKeyboard ? json_encode($inlineKeyboard) : null
            ]);
        }
    }

    // ==========================================
    // === 8. CREATE SESSION ===
    // ==========================================

    private function showCreateSessionGuide($chatId, $user)
    {
        $guide = "<b>Create a Running Session</b>\n\n";
        $guide .= "To create a running session, visit our website:\n";
        $guide .= "<b>https://stridesync.app/sessions/create</b>\n\n";
        $guide .= "Or use this quick link:\n";
        $guide .= "<a href='https://stridesync.app/sessions/create'>Create Session on StrideSync</a>\n\n";
        $guide .= "You can also scan this code with your phone!";

        $this->sendMessage($chatId, $guide);

        // Optional: Send a QR code image if you have it
        // $this->sendPhoto($chatId, 'path/to/qr_code.png');
    }

    /**
     * Send an inline button that opens the running session page on the web
     */
    private function showRunSessionLink($chatId)
    {
        $runSessionUrl = url('/sessions/create');
        $inlineKeyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'Open Run Session (Web)', 'url' => $runSessionUrl],
                ]
            ]
        ];

        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => "Create or manage your running sessions on the website:",
            'reply_markup' => json_encode($inlineKeyboard),
            'parse_mode' => 'HTML'
        ]);
    }

    /**
     * Ask user to confirm deactivation (unlink Telegram account)
     */
    private function showDeactivateConfirm($chatId)
    {
        $inlineKeyboard = [
            'inline_keyboard' => [
                [
['text' => 'Confirm Deactivate', 'callback_data' => 'deactivate_confirm'],
['text' => 'Cancel', 'callback_data' => 'deactivate_cancel'],
                ]
            ]
        ];

        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => "Are you sure you want to deactivate and unlink your Telegram account?\n\nYou can also reply with \"Confirm Deactivate\" or \"Cancel\".",
            'reply_markup' => json_encode($inlineKeyboard),
            'parse_mode' => 'HTML'
        ]);
    }

    private function startSessionCreation($chatId, $user)
    {
        // Reset any stale flow so users always start clean.
        $this->clearSessionFlow($user);

        $locationName = 'Unknown';
        $locationLat = null;
        $locationLng = null;
        $locationData = $user->location ? json_decode($user->location, true) : null;
        if (is_array($locationData)) {
            $city = $locationData['city'] ?? null;
            $state = $locationData['state'] ?? null;
            if ($city && $state) {
                $locationName = trim($city) . ', ' . trim($state);
            } elseif ($city) {
                $locationName = trim($city);
            } elseif ($state) {
                $locationName = trim($state);
            }

            $locationLat = isset($locationData['latitude']) ? (float) $locationData['latitude'] : null;
            $locationLng = isset($locationData['longitude']) ? (float) $locationData['longitude'] : null;
        }

        // If we lack coordinates, ask for location first.
        if ($locationLat === null || $locationLng === null) {
            $this->setSessionFlow($user, [
                'step' => 'location',
                'data' => [
                    'session_mode' => 'quick',
                ],
            ]);
            $this->sendLocationRequest($chatId, 'Share your current location to create a session.', $user);
            return;
        }

        $this->setSessionFlow($user, [
            'step' => 'session_day',
            'data' => [
                'location_name' => $locationName,
                'location_lat' => $locationLat,
                'location_lng' => $locationLng,
                'session_mode' => 'quick',
            ],
        ]);

        $locationLabel = $this->getSavedLocationLabel($user);
        if ($locationLabel) {
            $this->setSessionFlow($user, [
                'step' => 'session_location_choice',
                'data' => array_merge($this->getSessionFlow($user)['data'] ?? [], [
                    'location_button_label' => $locationLabel,
                ]),
            ]);

            $message = "Create a new session.\n";
            $message .= "Choose a location:";
            $this->sendLocationChoiceRequest($chatId, $message, $locationLabel);
            return;
        }

        $message = "Create a new session.\n";
        $message .= "Location: {$locationName}\n";
        $message .= "Choose the day for your run:";

        // Show quick, simple inline buttons right away.
        $this->sendSessionDayOptions($chatId, $message);
    }

    private function handleSessionLocationInput($chatId, $user, $location, $sessionFlow)
    {
        $lat = $location['latitude'] ?? null;
        $lng = $location['longitude'] ?? null;

        if ($lat === null || $lng === null) {
            $this->sendMessage($chatId, 'Location not recognized. Please try again.');
            return;
        }

        if (($sessionFlow['data']['session_mode'] ?? null) === 'quick') {
            $geo = app(GeocodingService::class);
            $resolved = $geo->reverseGeocodeCityState((float) $lat, (float) $lng);
            $city = $resolved['city'] ?? null;
            $state = $resolved['state'] ?? null;
            $sessionFlow['data']['location_lat'] = (float) $lat;
            $sessionFlow['data']['location_lng'] = (float) $lng;
            if ($state) {
                $sessionFlow['data']['location_state'] = $state;
            }
            $sessionFlow['step'] = 'location_label';
            $this->setSessionFlow($user, $sessionFlow);
            $this->sendMessage($chatId, 'Location received. Now type the place where the run will start (e.g. "Padang Kawad UiTM Jasin, Melaka").');
            return;
        }

        $sessionFlow['data']['location_lat'] = $lat;
        $sessionFlow['data']['location_lng'] = $lng;
        $sessionFlow['step'] = 'location_label';
        $this->setSessionFlow($user, $sessionFlow);

        $this->sendMessage($chatId, 'Location received. Now type the district and state (e.g. "Jasin, Melaka").');
    }

    private function handleSessionCreationStep($chatId, $user, $text, $sessionFlow)
    {
        $step = $sessionFlow['step'] ?? '';
        $data = $sessionFlow['data'] ?? [];

        if (strpos($step, 'session_') === 0) {
            $this->sendMessage($chatId, 'Please use the buttons to create your session.');
            return;
        }

        if ($step === 'location') {
            // Require a location share (button), no typing needed.
            $this->sendLocationRequest($chatId, 'Tap the button to share your current location.', $user);
            return;
        }
        if ($step === 'location_label') {
            $label = trim($text);
            if ($label === '' || stripos($label, 'cancel') !== false) {
                $this->clearSessionFlow($user);
                $this->showMainMenu($chatId, $user);
                return;
            }
            if (!empty($data['location_state']) && stripos($label, $data['location_state']) === false) {
                $label = rtrim($label, ', ') . ', ' . $data['location_state'];
            }
            $sessionFlow['data']['location_name'] = $label;
            $sessionFlow['step'] = 'session_day';
            $this->setSessionFlow($user, $sessionFlow);
            $message = "Location: {$label}\nChoose the day for your run:";
            $this->sendSessionDayOptions($chatId, $message);
            return;
        }
        $this->sendMessage($chatId, 'Please use the buttons to create your session.');
    }

    private function parseSessionTime($value)
    {
        $value = trim(strtolower($value));
        if ($value === '') {
            return null;
        }

        $timezone = config('app.timezone');
        $now = Carbon::now($timezone);

        // Examples: today 7pm, tomorrow 07:30, today 7:15 pm
        if (preg_match('/^(today|tomorrow)\s+(\d{1,2})(?::(\d{2}))?\s*(am|pm)?$/i', $value, $matches)) {
            $dayOffset = strtolower($matches[1]) === 'tomorrow' ? 1 : 0;
            $hour = (int) $matches[2];
            $minute = isset($matches[3]) ? (int) $matches[3] : 0;
            $ampm = isset($matches[4]) ? strtolower($matches[4]) : null;

            $hour = $this->normalizeHour($hour, $ampm);
            if ($hour === null || $minute < 0 || $minute > 59) {
                return null;
            }

            return Carbon::create(
                $now->year,
                $now->month,
                $now->day,
                $hour,
                $minute,
                0,
                $timezone
            )->addDays($dayOffset);
        }

        // Examples: 2025-12-17 19:30, 2025-12-17 7pm, 2025-12-17 7:15 pm
        if (preg_match('/^(\\d{4}-\\d{2}-\\d{2})\\s+(\\d{1,2})(?::(\\d{2}))?\\s*(am|pm)?$/i', $value, $matches)) {
            $date = $matches[1];
            $hour = (int) $matches[2];
            $minute = isset($matches[3]) ? (int) $matches[3] : 0;
            $ampm = isset($matches[4]) ? strtolower($matches[4]) : null;

            $hour = $this->normalizeHour($hour, $ampm);
            if ($hour === null || $minute < 0 || $minute > 59) {
                return null;
            }

            [$year, $month, $day] = array_map('intval', explode('-', $date));
            return Carbon::create($year, $month, $day, $hour, $minute, 0, $timezone);
        }

        // Examples: 19:30, 7pm, 7:15 pm (assume today)
        if (preg_match('/^(\\d{1,2})(?::(\\d{2}))?\\s*(am|pm)?$/i', $value, $matches)) {
            $hour = (int) $matches[1];
            $minute = isset($matches[2]) ? (int) $matches[2] : 0;
            $ampm = isset($matches[3]) ? strtolower($matches[3]) : null;

            $hour = $this->normalizeHour($hour, $ampm);
            if ($hour === null || $minute < 0 || $minute > 59) {
                return null;
            }

            return Carbon::create(
                $now->year,
                $now->month,
                $now->day,
                $hour,
                $minute,
                0,
                $timezone
            );
        }

        return null;
    }

    private function normalizeHour($hour, $ampm)
    {
        if ($ampm === 'am' || $ampm === 'pm') {
            if ($hour < 1 || $hour > 12) {
                return null;
            }

            if ($ampm === 'pm' && $hour !== 12) {
                return $hour + 12;
            }

            if ($ampm === 'am' && $hour === 12) {
                return 0;
            }

            return $hour;
        }

        if ($hour < 0 || $hour > 23) {
            return null;
        }

        return $hour;
    }

    private function getSessionFlowKey($user)
    {
        return 'tg_session_create_' . $user->id;
    }

    private function getSessionFlow($user)
    {
        return Cache::get($this->getSessionFlowKey($user));
    }

    private function setSessionFlow($user, array $flow)
    {
        Cache::put($this->getSessionFlowKey($user), $flow, now()->addMinutes(30));
    }

    private function clearSessionFlow($user)
    {
        Cache::forget($this->getSessionFlowKey($user));
    }

    private function getPaceOptions(): array
    {
        return [
            '12:00/km - 11:00/km',
            '11:00/km - 10:00/km',
            '10:00/km - 9:00/km',
            '9:00/km - 8:00/km',
            '8:00/km - 7:00/km',
            '7:00/km - 6:00/km',
            '6:00/km - 5:00/km',
            '5:00/km - 4:00/km',
            '4:00/km - 3:00/km',
        ];
    }

    private function sendPaceOptions($chatId, $prompt): void
    {
        $options = $this->getPaceOptions();
        $rows = [];
        foreach ($options as $index => $label) {
            $rows[] = [
                ['text' => $label, 'callback_data' => 'pace_' . $index],
            ];
        }

        $inlineKeyboard = [
            'inline_keyboard' => $rows,
        ];

        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $prompt,
            'reply_markup' => json_encode($inlineKeyboard),
            'parse_mode' => 'HTML'
        ]);
    }

    private function sendSessionDayOptions($chatId, $prompt): void
    {
        $inlineKeyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'Today', 'callback_data' => 'session_day_today'],
                    ['text' => 'Tomorrow', 'callback_data' => 'session_day_tomorrow'],
                ],
                [
                    ['text' => 'Other', 'callback_data' => 'session_day_other'],
                ],
                [
                    ['text' => 'Cancel', 'callback_data' => 'session_create_cancel'],
                ],
            ],
        ];

        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $prompt,
            'reply_markup' => json_encode($inlineKeyboard),
            'parse_mode' => 'HTML'
        ]);
    }

    private function sendSessionDateOptions($chatId, $prompt): void
    {
        $timezone = config('app.timezone');
        $today = Carbon::now($timezone)->startOfDay();
        $rows = [];
        $row = [];

        for ($i = 1; $i <= 7; $i++) {
            $date = $today->copy()->addDays($i);
            $label = $date->format('D, M j');
            $value = $date->format('Y-m-d');
            $row[] = ['text' => $label, 'callback_data' => 'session_day_date_' . $value];
            if (count($row) === 2) {
                $rows[] = $row;
                $row = [];
            }
        }

        if (!empty($row)) {
            $rows[] = $row;
        }

        $rows[] = [
            ['text' => 'Back', 'callback_data' => 'session_day_back'],
            ['text' => 'Cancel', 'callback_data' => 'session_create_cancel'],
        ];

        $inlineKeyboard = [
            'inline_keyboard' => $rows,
        ];

        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $prompt,
            'reply_markup' => json_encode($inlineKeyboard),
            'parse_mode' => 'HTML'
        ]);
    }

    private function getSessionTimeSlots(): array
    {
        return [
            '5am' => [5, 0],
            '6am' => [6, 0],
            '7am' => [7, 0],
            '8am' => [8, 0],
            '5pm' => [17, 0],
            '6pm' => [18, 0],
            '7pm' => [19, 0],
            '8pm' => [20, 0],
        ];
    }

    private function sendSessionTimeOptions($chatId, $prompt): void
    {
        $inlineKeyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '5am', 'callback_data' => 'session_time_5am'],
                    ['text' => '6am', 'callback_data' => 'session_time_6am'],
                    ['text' => '7am', 'callback_data' => 'session_time_7am'],
                ],
                [
                    ['text' => '8am', 'callback_data' => 'session_time_8am'],
                    ['text' => '5pm', 'callback_data' => 'session_time_5pm'],
                ],
                [
                    ['text' => '6pm', 'callback_data' => 'session_time_6pm'],
                    ['text' => '7pm', 'callback_data' => 'session_time_7pm'],
                ],
                [
                    ['text' => '8pm', 'callback_data' => 'session_time_8pm'],
                ],
                [
                    ['text' => 'Cancel', 'callback_data' => 'session_create_cancel'],
                ],
            ],
        ];

        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $prompt,
            'reply_markup' => json_encode($inlineKeyboard),
            'parse_mode' => 'HTML'
        ]);
    }

    private function getSessionDurationOptions(): array
    {
        return [
            '30' => ['label' => '30 min', 'minutes' => 30],
            '45' => ['label' => '45 min', 'minutes' => 45],
            '60' => ['label' => '60 min', 'minutes' => 60],
            '90' => ['label' => '90 min', 'minutes' => 90],
            '120' => ['label' => '120 min', 'minutes' => 120],
        ];
    }

    private function sendSessionDurationOptions($chatId, $prompt): void
    {
        $inlineKeyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '30 min', 'callback_data' => 'session_duration_30'],
                    ['text' => '45 min', 'callback_data' => 'session_duration_45'],
                ],
                [
                    ['text' => '60 min', 'callback_data' => 'session_duration_60'],
                    ['text' => '90 min', 'callback_data' => 'session_duration_90'],
                ],
                [
                    ['text' => '120 min', 'callback_data' => 'session_duration_120'],
                ],
                [
                    ['text' => 'Cancel', 'callback_data' => 'session_create_cancel'],
                ],
            ],
        ];

        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $prompt,
            'reply_markup' => json_encode($inlineKeyboard),
            'parse_mode' => 'HTML'
        ]);
    }

    private function sendSessionPaceOptions($chatId, $prompt): void
    {
        $options = $this->getPaceOptions();
        $rows = [];
        foreach ($options as $index => $label) {
            $rows[] = [
                ['text' => $label, 'callback_data' => 'session_pace_' . $index],
            ];
        }

        $rows[] = [
            ['text' => 'Cancel', 'callback_data' => 'session_create_cancel'],
        ];

        $inlineKeyboard = [
            'inline_keyboard' => $rows,
        ];

        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $prompt,
            'reply_markup' => json_encode($inlineKeyboard),
            'parse_mode' => 'HTML'
        ]);
    }

    private function getSessionActivityOptions(): array
    {
        return [
            '5k' => '5km',
            '10k' => '10km',
            'long' => 'Long Run',
            'interval' => 'Interval',
        ];
    }

    private function sendSessionActivityOptions($chatId, $prompt): void
    {
        $inlineKeyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '5km', 'callback_data' => 'session_activity_5k'],
                    ['text' => '10km', 'callback_data' => 'session_activity_10k'],
                ],
                [
                    ['text' => 'Long Run', 'callback_data' => 'session_activity_long'],
                    ['text' => 'Interval', 'callback_data' => 'session_activity_interval'],
                ],
                [
                    ['text' => 'Cancel', 'callback_data' => 'session_create_cancel'],
                ],
            ],
        ];

        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $prompt,
            'reply_markup' => json_encode($inlineKeyboard),
            'parse_mode' => 'HTML'
        ]);
    }

    private function sendLocationRequest($chatId, $prompt, $user = null): void
    {
        $keyboard = [
            [
                ['text' => 'Share my location', 'request_location' => true],
            ],
            [
                ['text' => 'Cancel'],
            ],
        ];

        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $prompt,
            'reply_markup' => json_encode([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ]);
    }

    private function sendLocationChoiceRequest($chatId, $prompt, string $locationLabel): void
    {
        $keyboard = [
            [
                ['text' => $locationLabel],
            ],
            [
                ['text' => 'Share my location', 'request_location' => true],
            ],
            [
                ['text' => 'Cancel'],
            ],
        ];

        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $prompt,
            'reply_markup' => json_encode([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ]);
    }

    private function getSavedLocationLabel($user): ?string
    {
        if (!$user || !$user->location) {
            return null;
        }

        $text = $user->formatLocationText('');
        if (!is_string($text) || trim($text) === '') {
            return null;
        }

        return 'Saved location: ' . trim($text);
    }

    private function computeTelegramState($user): string
    {
        if (!$this->extractCoordinates($user->location)) {
            return 'waiting_location';
        }

        if (!$user->gender) {
            return 'waiting_gender';
        }

        if (!$user->avg_pace) {
            return 'waiting_pace';
        }

        if (!$user->strava_screenshot) {
            return 'waiting_photo';
        }

        return 'profile_complete';
    }

    private function syncTelegramState($user): string
    {
        $state = $this->computeTelegramState($user);
        if ($user->telegram_state !== $state) {
            $user->update(['telegram_state' => $state]);
        }
        return $state;
    }

    private function promptForProfileCompletion($chatId, $user, string $state): void
    {
        if ($state === 'waiting_gender') {
            $inlineKeyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'Male', 'callback_data' => 'gender_male'],
                        ['text' => 'Female', 'callback_data' => 'gender_female'],
                    ]
                ]
            ];

            Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => 'Select your gender:',
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode($inlineKeyboard)
            ]);
            return;
        }
        if ($state === 'waiting_pace') {
            $this->sendPaceOptions($chatId, "Select your average running pace:");
            return;
        }

        if ($state === 'waiting_location') {
            $this->sendLocationRequest($chatId, "Please share your current location so we can find running buddies near you.", $user);
            return;
        }

        if ($state === 'waiting_photo') {
            $this->sendMessage($chatId, 'Please upload your profile photo to complete your profile.');
        }
    }

    private function getDeactivateKey($user)
    {
        return 'tg_deactivate_' . $user->id;
    }

    private function setDeactivatePending($user)
    {
        Cache::put($this->getDeactivateKey($user), true, now()->addMinutes(10));
    }

    private function clearDeactivatePending($user)
    {
        Cache::forget($this->getDeactivateKey($user));
    }

    private function isDeactivatePending($user)
    {
        return Cache::has($this->getDeactivateKey($user));
    }

    private function getEmailLinkKey($chatId)
    {
        return 'tg_link_email_' . $chatId;
    }

    private function setEmailLinkPending($chatId)
    {
        Cache::put($this->getEmailLinkKey($chatId), true, now()->addMinutes(10));
    }

    private function clearEmailLinkPending($chatId)
    {
        Cache::forget($this->getEmailLinkKey($chatId));
    }

    private function isEmailLinkPending($chatId)
    {
        return Cache::has($this->getEmailLinkKey($chatId));
    }

    private function getLocationUpdateKey($user)
    {
        return 'tg_location_update_' . $user->id;
    }

    private function setLocationUpdatePending($user)
    {
        Cache::put($this->getLocationUpdateKey($user), true, now()->addMinutes(10));
    }

    private function clearLocationUpdatePending($user)
    {
        Cache::forget($this->getLocationUpdateKey($user));
    }

    private function isLocationUpdatePending($user)
    {
        return Cache::has($this->getLocationUpdateKey($user));
    }

    private function getPhotoUpdateKey($user)
    {
        return 'tg_photo_update_' . $user->id;
    }

    private function setPhotoUpdatePending($user)
    {
        Cache::put($this->getPhotoUpdateKey($user), true, now()->addMinutes(10));
    }

    private function clearPhotoUpdatePending($user)
    {
        Cache::forget($this->getPhotoUpdateKey($user));
    }

    private function isPhotoUpdatePending($user)
    {
        return Cache::has($this->getPhotoUpdateKey($user));
    }

    private function getMatchKey(int $userId, int $buddyId): string
    {
        $ids = [$userId, $buddyId];
        sort($ids);
        return 'tg_buddy_match_' . $ids[0] . '_' . $ids[1];
    }

    private function notifyMatch($user, $buddy): void
    {
        if (!$user->telegram_id || !$buddy->telegram_id) {
            return;
        }

        $cacheKey = $this->getMatchKey($user->id, $buddy->id);
        if (Cache::has($cacheKey)) {
            return;
        }

        $buddyChatUrl = $buddy->telegram_username
            ? "https://t.me/{$buddy->telegram_username}"
            : "tg://user?id={$buddy->telegram_id}";
        $userChatUrl = $user->telegram_username
            ? "https://t.me/{$user->telegram_username}"
            : "tg://user?id={$user->telegram_id}";

        $keyboardForUser = [
            'inline_keyboard' => [
                [
                    ['text' => 'Start Chat', 'url' => $buddyChatUrl],
                ],
            ],
        ];

        $keyboardForBuddy = [
            'inline_keyboard' => [
                [
                    ['text' => 'Start Chat', 'url' => $userChatUrl],
                ],
            ],
        ];

        $this->sendMessage($user->telegram_id, "It's a match! You and <b>{$buddy->name}</b> liked each other.");
        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $user->telegram_id,
            'text' => 'Start chatting now:',
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode($keyboardForUser),
        ]);

        $this->sendMessage($buddy->telegram_id, "It's a match! You and <b>{$user->name}</b> liked each other.");
        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $buddy->telegram_id,
            'text' => 'Start chatting now:',
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode($keyboardForBuddy),
        ]);

        Cache::put($cacheKey, true, now()->addDays(30));
    }

    private function resolveStateFromCoords(float $lat, float $lon): ?string
    {
        $apiKey = env('OPENWEATHER_API_KEY');
        if (!$apiKey) {
            return null;
        }

        $cacheKey = 'geo.state.' . $lat . '.' . $lon;
        return Cache::remember($cacheKey, now()->addDays(7), function () use ($lat, $lon, $apiKey) {
            $response = Http::timeout(5)->get('https://api.openweathermap.org/geo/1.0/reverse', [
                'lat' => $lat,
                'lon' => $lon,
                'limit' => 1,
                'appid' => $apiKey,
            ]);

            if (!$response->ok()) {
                return null;
            }

            $payload = $response->json();
            if (!is_array($payload) || empty($payload[0])) {
                return null;
            }

            return $payload[0]['state'] ?? $payload[0]['name'] ?? null;
        });
    }

    // ==========================================
    // === 9. CALLBACK QUERY HANDLER ===
    // ==========================================

    private function handleCallbackQuery($callbackQuery)
    {
        $chatId = $callbackQuery['from']['id'];
        $data = $callbackQuery['data'];
        $callbackId = $callbackQuery['id'];
        $messageId = $callbackQuery['message']['message_id'] ?? null;

        $user = User::where('telegram_id', $chatId)->first();
        if (!$user) {
            $this->sendMessage($chatId, 'Account not linked. Send /start to link.');
            return;
        }

        // Acknowledge the callback
        Http::withoutVerifying()->post("{$this->apiUrl}/answerCallbackQuery", [
            'callback_query_id' => $callbackId
        ]);

        if ($user->telegram_state === 'waiting_photo' && !in_array($data, ['edit_photo', 'menu_profile'], true)) {
            $this->sendMessage($chatId, 'Please upload your profile photo to continue.');
            return;
        }

        // ===== GENDER SELECTION =====
        if (strpos($data, 'gender_') === 0) {
            $gender = str_replace('gender_', '', $data);
            $user->update(['gender' => ucfirst($gender)]);
            $this->sendMessage($chatId, "Gender set to <b>" . ucfirst($gender) . "</b>");
            $this->sendPaceOptions($chatId, "Select your average running pace:");
            $user->update(['telegram_state' => 'waiting_pace']);
        }
        elseif (strpos($data, 'pace_') === 0) {
            $index = (int) str_replace('pace_', '', $data);
            $options = $this->getPaceOptions();
            if (!array_key_exists($index, $options)) {
                $this->sendMessage($chatId, 'Please choose a pace option from the buttons.');
                return;
            }
            $user->update([
                'avg_pace' => $options[$index],
                'telegram_state' => 'waiting_location'
            ]);
            $this->sendLocationRequest($chatId, 'Thanks! Now share your current location.', $user);
        }
        // ===== MAIN MENU QUICK ACTIONS =====
        elseif ($data === 'menu_profile') {
            $this->showMyProfile($chatId, $user);
        }
        elseif ($data === 'menu_schedule') {
            $this->showWeeklySchedule($chatId, $user);
        }
        elseif ($data === 'menu_find_buddy') {
            $this->showFindBuddy($chatId, $user);
        }
        elseif ($data === 'menu_buddy_requests') {
            $this->showCheckInvitations($chatId, $user);
        }
        elseif ($data === 'menu_join_nearby') {
            $this->showRunningSessions($chatId, $user);
        }
        elseif ($data === 'menu_create_session') {
            if (isset($callbackId)) {
                Http::withoutVerifying()->post("{$this->apiUrl}/answerCallbackQuery", [
                    'callback_query_id' => $callbackId,
                    'text' => 'Creating a session...',
                    'show_alert' => false,
                ]);
            }
            try {
                $this->sendMessage($chatId, 'Starting session creation...');
                $this->startSessionCreation($chatId, $user);
            } catch (\Throwable $e) {
                \Log::error('Telegram create session failed', [
                    'user_id' => $user->id ?? null,
                    'chat_id' => $chatId,
                    'error' => $e->getMessage(),
                ]);
                $this->sendMessage($chatId, 'Unable to start session creation. Please try again.');
            }
        }
        elseif ($data === 'menu_review') {
            $this->showReviewOptions($chatId, $user);
        }
        elseif (strpos($data, 'review_select_') === 0) {
            $sessionId = (int) str_replace('review_select_', '', $data);
            $this->setReviewPending($user, $sessionId, null, 'rating');
            $this->sendReviewRatingOptions($chatId, $sessionId);
        }
        elseif ($data === 'review_rate_cancel') {
            $this->clearReviewPending($user);
            $this->showMainMenu($chatId, $user);
        }
        elseif (preg_match('/^review_rate_(\d+)_(\d+)$/', $data, $matches)) {
            $sessionId = (int) $matches[1];
            $rating = (int) $matches[2];
            $this->setReviewPending($user, $sessionId, $rating, 'comment');
            $this->sendMessage($chatId, 'Please type your review comment (or type Cancel).');
        }
        elseif ($data === 'menu_deactivate') {
            $this->clearSessionFlow($user);
            $this->setDeactivatePending($user);
            $this->showDeactivateConfirm($chatId);
        }
        elseif ($data === 'menu_main') {
            $this->showMainMenu($chatId, $user);
        }
        // ===== DEACTIVATE ACCOUNT =====
        elseif ($data === 'deactivate_confirm') {
            $this->clearDeactivatePending($user);
                $user->update([
                    'telegram_id' => null,
                    'telegram_state' => 'unlinked'
                ]);

            $this->sendMessage($chatId, "Your Telegram account has been unlinked. Send /start if you want to link again.");
        }
        elseif ($data === 'deactivate_cancel') {
            $this->clearDeactivatePending($user);
            $this->sendMessage($chatId, "Deactivation canceled. You're still linked.");
        }
        // ===== SESSION CREATE CONFIRM =====
        elseif ($data === 'session_create_confirm') {
            if (isset($callbackId)) {
                Http::withoutVerifying()->post("{$this->apiUrl}/answerCallbackQuery", [
                    'callback_query_id' => $callbackId,
                    'text' => 'Saving your session...',
                    'show_alert' => false,
                ]);
            }
            $sessionFlow = $this->getSessionFlow($user);
            $data = $sessionFlow['data'] ?? null;

            if (!$sessionFlow || ($sessionFlow['step'] ?? '') !== 'confirm' || !$data) {
                $this->sendMessage($chatId, 'Session creation data not found. Please start again.');
                return;
            }

            $start = $this->parseSessionTime($data['start_time'] ?? '');
            $end = $this->parseSessionTime($data['end_time'] ?? '');

            if (!$start || !$end) {
                $this->clearSessionFlow($user);
                $this->sendMessage($chatId, 'Session times are invalid. Please start again.');
                return;
            }

            $locationName = $data['location_name'] ?? 'Unknown';
            $userLocationData = $user->location ? json_decode($user->location, true) : null;
            $userState = is_array($userLocationData) ? ($userLocationData['state'] ?? null) : null;
            if ($userState && stripos($locationName, $userState) === false) {
                $locationName = rtrim($locationName, ', ') . ', ' . $userState;
            }

            $session = RunningSession::create([
                'user_id' => $user->id,
                'start_time' => $start,
                'end_time' => $end,
                'average_pace' => $data['average_pace'] ?? '',
                'duration' => $data['duration'] ?? '',
                'activity' => $data['activity'] ?? null,
                'location_name' => $locationName,
                'location_lat' => isset($data['location_lat']) ? (float) $data['location_lat'] : null,
                'location_lng' => isset($data['location_lng']) ? (float) $data['location_lng'] : null,
            ]);

            $this->clearSessionFlow($user);
            $this->sendMessage($chatId, "Session created. ID: {$session->session_id}");
            $this->sendMessage($chatId, "Reminders set: 30 min and 10 min before start.");
            $this->showMainMenu($chatId, $user);
        }
        elseif ($data === 'session_create_cancel') {
            if (isset($callbackId)) {
                Http::withoutVerifying()->post("{$this->apiUrl}/answerCallbackQuery", [
                    'callback_query_id' => $callbackId,
                    'text' => 'Session creation canceled',
                    'show_alert' => false,
                ]);
            }
            $this->clearSessionFlow($user);
            $this->sendMessage($chatId, 'Session creation canceled.');
            $this->showMainMenu($chatId, $user);
        }
        elseif (strpos($data, 'session_day_') === 0) {
            $sessionFlow = $this->getSessionFlow($user);
            if (!$sessionFlow) {
                // Restart the flow gracefully instead of failing silently.
                $this->startSessionCreation($chatId, $user);
                return;
            }

            $day = str_replace('session_day_', '', $data);
            if ($day === 'other') {
                $sessionFlow['step'] = 'session_date';
                $this->setSessionFlow($user, $sessionFlow);
                $this->sendSessionDateOptions($chatId, 'Choose a date:');
                return;
            }

            if ($day === 'back') {
                $sessionFlow['step'] = 'session_day';
                $this->setSessionFlow($user, $sessionFlow);
                $this->sendSessionDayOptions($chatId, 'Choose the day for your run:');
                return;
            }

            if (!in_array($day, ['today', 'tomorrow'], true)) {
                $this->sendMessage($chatId, 'Invalid day selection. Please start again.');
                return;
            }

            $sessionFlow['data']['start_day'] = $day;
            $sessionFlow['step'] = 'session_time';
            $this->setSessionFlow($user, $sessionFlow);
            $this->sendSessionTimeOptions($chatId, 'Choose a start time:');
        }
        elseif (strpos($data, 'session_day_date_') === 0) {
            $sessionFlow = $this->getSessionFlow($user);
            if (!$sessionFlow) {
                $this->sendMessage($chatId, 'Session creation data not found. Please start again.');
                return;
            }

            $dateValue = str_replace('session_day_date_', '', $data);
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateValue)) {
                $this->sendMessage($chatId, 'Invalid date selection. Please start again.');
                return;
            }

            $sessionFlow['data']['start_date'] = $dateValue;
            $sessionFlow['step'] = 'session_time';
            $this->setSessionFlow($user, $sessionFlow);
            $this->sendSessionTimeOptions($chatId, 'Choose a start time:');
        }
        elseif (strpos($data, 'session_time_') === 0) {
            $sessionFlow = $this->getSessionFlow($user);
            if (!$sessionFlow) {
                $this->sendMessage($chatId, 'Session creation data not found. Please start again.');
                return;
            }

            $timeKey = str_replace('session_time_', '', $data);
            $timeSlots = $this->getSessionTimeSlots();
            if (!isset($timeSlots[$timeKey])) {
                $this->sendMessage($chatId, 'Invalid time selection. Please start again.');
                return;
            }

            $startDay = $sessionFlow['data']['start_day'] ?? 'today';
            $timezone = config('app.timezone');
            $startDate = Carbon::now($timezone)->startOfDay();
            $startDateValue = $sessionFlow['data']['start_date'] ?? null;
            if (is_string($startDateValue) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDateValue)) {
                $startDate = Carbon::createFromFormat('Y-m-d', $startDateValue, $timezone)->startOfDay();
            } elseif ($startDay === 'tomorrow') {
                $startDate = $startDate->addDay();
            }

            [$hour, $minute] = $timeSlots[$timeKey];
            $startDate = $startDate->setTime($hour, $minute, 0);

            $sessionFlow['data']['start_time'] = $startDate->format('Y-m-d H:i');
            $sessionFlow['step'] = 'session_duration';
            $this->setSessionFlow($user, $sessionFlow);
            $this->sendSessionDurationOptions($chatId, 'Choose a duration:');
        }
        elseif (strpos($data, 'session_duration_') === 0) {
            $sessionFlow = $this->getSessionFlow($user);
            if (!$sessionFlow) {
                $this->sendMessage($chatId, 'Session creation data not found. Please start again.');
                return;
            }

            $durationKey = str_replace('session_duration_', '', $data);
            $durations = $this->getSessionDurationOptions();
            if (!isset($durations[$durationKey])) {
                $this->sendMessage($chatId, 'Invalid duration selection. Please start again.');
                return;
            }

            $minutes = $durations[$durationKey]['minutes'];
            $start = $this->parseSessionTime($sessionFlow['data']['start_time'] ?? '');
            if (!$start) {
                $this->sendMessage($chatId, 'Start time not set. Please start again.');
                return;
            }

            $end = $start->copy()->addMinutes($minutes);
            $sessionFlow['data']['duration'] = $durations[$durationKey]['label'];
            $sessionFlow['data']['end_time'] = $end->format('Y-m-d H:i');
            $sessionFlow['step'] = 'session_pace';
            $this->setSessionFlow($user, $sessionFlow);
            $this->sendSessionPaceOptions($chatId, 'Choose your average pace:');
        }
        elseif (strpos($data, 'session_pace_') === 0) {
            $sessionFlow = $this->getSessionFlow($user);
            if (!$sessionFlow) {
                $this->sendMessage($chatId, 'Session creation data not found. Please start again.');
                return;
            }

            $index = (int) str_replace('session_pace_', '', $data);
            $options = $this->getPaceOptions();
            if (!array_key_exists($index, $options)) {
                $this->sendMessage($chatId, 'Invalid pace selection. Please start again.');
                return;
            }

            $sessionFlow['data']['average_pace'] = $options[$index];
            $sessionFlow['step'] = 'session_activity';
            $this->setSessionFlow($user, $sessionFlow);
            $this->sendSessionActivityOptions($chatId, 'Choose an activity:');
        }
        elseif (strpos($data, 'session_activity_') === 0) {
            $sessionFlow = $this->getSessionFlow($user);
            if (!$sessionFlow) {
                $this->sendMessage($chatId, 'Session creation data not found. Please start again.');
                return;
            }

            $activityKey = str_replace('session_activity_', '', $data);
            $activities = $this->getSessionActivityOptions();
            if (!isset($activities[$activityKey])) {
                $this->sendMessage($chatId, 'Invalid activity selection. Please start again.');
                return;
            }

            $sessionFlow['data']['activity'] = $activities[$activityKey];
            $sessionFlow['step'] = 'confirm';
            $this->setSessionFlow($user, $sessionFlow);

            $summary = "Please confirm your session:\n";
            $summary .= "Location: " . ($sessionFlow['data']['location_name'] ?? 'Unknown') . "\n";
            $summary .= "Start: " . ($sessionFlow['data']['start_time'] ?? '-') . "\n";
            $summary .= "End: " . ($sessionFlow['data']['end_time'] ?? '-') . "\n";
            $summary .= "Pace: " . ($sessionFlow['data']['average_pace'] ?? '-') . "\n";
            $summary .= "Activity: " . ($sessionFlow['data']['activity'] ?? '-') . "\n";
            $summary .= "Duration: " . ($sessionFlow['data']['duration'] ?? '-') . "\n";
            $summary .= "Reminders: 30 min before, 10 min before";

            $inlineKeyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'Confirm', 'callback_data' => 'session_create_confirm'],
                        ['text' => 'Cancel', 'callback_data' => 'session_create_cancel'],
                    ]
                ]
            ];

            Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $summary,
                'reply_markup' => json_encode($inlineKeyboard),
                'parse_mode' => 'HTML'
            ]);
        }

        // ===== INVITE BUDDY =====
        elseif (strpos($data, 'invite_buddy_') === 0) {
            $buddyId = str_replace('invite_buddy_', '', $data);
            $buddy = User::find($buddyId);

            if ($buddy) {
                // Create a notification in joined_sessions table
                JoinedSession::create([
                    'session_id' => null, // This is an invitation, not a session
                    'user_id' => $user->id,
                    'invited_user_id' => $buddy->id,
                    'status' => 'invited'
                ]);

                $this->sendMessage($chatId, "Invitation sent to <b>{$buddy->name}</b>!");
                
                // Notify the buddy
                $this->sendMessage(
                    $buddy->telegram_id,
                    "<b>{$user->name}</b> invited you to run together!"
                );
            }
        }

        // ===== ACCEPT INVITATION =====
        elseif (strpos($data, 'accept_invite_') === 0) {
            $invitationId = str_replace('accept_invite_', '', $data);
            $invitation = JoinedSession::find($invitationId);

            if ($invitation) {
                $invitation->update(['status' => 'accepted']);
                $this->sendMessage($chatId, "You accepted the invitation!");

                $inviter = User::find($invitation->user_id);
                if ($inviter && $inviter->telegram_id) {
                    $this->sendMessage(
                        $inviter->telegram_id,
                        "<b>{$user->name}</b> accepted your invitation! Time to run together!"
                    );
                }
            }
        }

        // ===== DECLINE INVITATION =====
        elseif (strpos($data, 'decline_invite_') === 0) {
            $invitationId = str_replace('decline_invite_', '', $data);
            $invitation = JoinedSession::find($invitationId);

            if ($invitation) {
                $invitation->delete();
                $this->sendMessage($chatId, "Invitation declined.");
            }
        }
        elseif (strpos($data, 'report_invite_') === 0) {
            $invitationId = str_replace('report_invite_', '', $data);
            $invitation = JoinedSession::find($invitationId);

            if ($invitation) {
                $targetId = (int) $invitation->user_id; // the person who sent the invite
                $this->setReportPending($user, $targetId);
                $this->sendReportReasonPrompt($chatId);
            } else {
                $this->sendMessage($chatId, 'This invitation is no longer available.');
            }
        }

        // ===== JOIN SESSION =====
        elseif (strpos($data, 'join_session_') === 0) {
            $sessionId = str_replace('join_session_', '', $data);
            $session = RunningSession::find($sessionId);

            if ($session) {
                if ($session->user_id === $user->id) {
                    Http::withoutVerifying()->post("{$this->apiUrl}/answerCallbackQuery", [
                        'callback_query_id' => $callbackId,
                        'text' => 'You are the organizer of this session.',
                        'show_alert' => true
                    ]);
                    return;
                }

                try {
                    $joined = JoinedSession::firstOrCreate(
                        ['session_id' => $sessionId, 'user_id' => $user->id],
                        ['status' => 'joined', 'joined_at' => now()]
                    );

                    if (!$joined->wasRecentlyCreated) {
                        Http::withoutVerifying()->post("{$this->apiUrl}/answerCallbackQuery", [
                            'callback_query_id' => $callbackId,
                            'text' => 'You already joined this session!',
                            'show_alert' => true
                        ]);
                        return;
                    }

                    Http::withoutVerifying()->post("{$this->apiUrl}/answerCallbackQuery", [
                        'callback_query_id' => $callbackId,
                        'text' => 'Joined!',
                        'show_alert' => false
                    ]);

                    $locationName = $session->location_name ?? 'Unknown';
                    $this->sendMessage($chatId, "You joined the session <b>{$locationName}</b>!");
                    $this->sendMessage($chatId, "Reminders set: 30 min and 10 min before start.");
                    $this->showMainMenu($chatId, $user);

                    $sessionCreator = User::find($session->user_id);
                    if ($sessionCreator && $sessionCreator->telegram_id) {
                        $this->sendMessage(
                            $sessionCreator->telegram_id,
                            "<b>{$user->name}</b> joined your session <b>{$locationName}</b>!"
                        );
                    }
                } catch (\Throwable $e) {
                    \Log::error('Telegram join session failed', [
                        'session_id' => $sessionId,
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                    Http::withoutVerifying()->post("{$this->apiUrl}/answerCallbackQuery", [
                        'callback_query_id' => $callbackId,
                        'text' => 'Unable to join right now.',
                        'show_alert' => true
                    ]);
                }
            }
        }
        elseif (strpos($data, 'unjoin_session_') === 0) {
            $sessionId = str_replace('unjoin_session_', '', $data);
            $session = RunningSession::find($sessionId);

            if (!$session) {
                $this->sendMessage($chatId, 'Session not found.');
                return;
            }

            if ($session->user_id === $user->id) {
                $this->sendMessage($chatId, "You're the organizer of this session. You can't unjoin your own session.");
                return;
            }

            $deleted = JoinedSession::where('session_id', $sessionId)
                ->where('user_id', $user->id)
                ->delete();

            if ($deleted) {
                $this->sendMessage($chatId, 'You have left the session.');

                $organizer = User::find($session->user_id);
                if ($organizer && $organizer->telegram_id) {
                    $this->sendMessage(
                        $organizer->telegram_id,
                        "<b>{$user->name}</b> left your session at <b>" . ($session->location_name ?? 'Unknown') . "</b>."
                    );
                }
            } else {
                $this->sendMessage($chatId, 'You are not part of this session.');
            }
        }

        // ===== BUDDY BROWSING =====
        elseif (strpos($data, 'buddy_report_') === 0) {
            $targetId = (int) str_replace('buddy_report_', '', $data);
            $target = User::find($targetId);
            if (!$target) {
                $this->sendMessage($chatId, 'This user is no longer available.');
                return;
            }

            $this->setReportPending($user, $targetId);
            $this->sendReportReasonPrompt($chatId);
        }
        elseif (strpos($data, 'buddy_like_') === 0) {
            $buddyId = str_replace('buddy_like_', '', $data);
            $buddy = User::find($buddyId);

            if ($buddy && $buddy->telegram_id) {
                $existingLike = BuddyLike::where('liker_id', $user->id)
                    ->where('liked_id', $buddy->id)
                    ->first();
                $alreadyLiked = $existingLike && $existingLike->status === 'like';

                BuddyLike::updateOrCreate(
                    ['liker_id' => $user->id, 'liked_id' => $buddy->id],
                    ['status' => 'like']
                );

                $mutual = BuddyLike::where('liker_id', $buddy->id)
                    ->where('liked_id', $user->id)
                    ->where('status', 'like')
                    ->exists();

                if ($mutual) {
                    $this->notifyMatch($user, $buddy);
                } else {
                    $this->sendMessage($chatId, "Liked <b>{$buddy->name}</b>. Waiting for a like back.");
                    if (!$alreadyLiked) {
                        $likeCount = BuddyLike::where('liked_id', $buddy->id)
                            ->where('status', 'like')
                            ->count();

                        $inlineKeyboard = [
                            'inline_keyboard' => [
                                [
                                    ['text' => 'Buddy requests', 'callback_data' => 'menu_buddy_requests'],
                                ],
                                [
                                    ['text' => 'Report', 'callback_data' => "buddy_report_{$user->id}"],
                                ],
                            ],
                        ];
                        $this->pushLikeInbox($buddy, $user->id);
                        $this->sendLikePrompt($buddy->telegram_id, $likeCount);

                        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                            'chat_id' => $buddy->telegram_id,
                            'text' => "Someone liked your profile.\nTotal likes: {$likeCount}",
                            'parse_mode' => 'HTML',
                            'reply_markup' => json_encode($inlineKeyboard),
                        ]);
                    }
                }
            } else {
                $this->sendMessage($chatId, 'This user is no longer available.');
            }

            $this->showNextBuddy($chatId, $user);
        }
        elseif (strpos($data, 'buddy_dislike_') === 0) {
            $buddyId = str_replace('buddy_dislike_', '', $data);
            if (is_numeric($buddyId)) {
                BuddyLike::updateOrCreate(
                    ['liker_id' => $user->id, 'liked_id' => (int) $buddyId],
                    ['status' => 'dislike']
                );
            }
            $this->showNextBuddy($chatId, $user);
        }
        elseif ($data === 'buddy_stop') {
            $this->clearBuddyQueue($user);
            $this->sendMessage($chatId, 'Stopped browsing runners. Back to main menu.');
            $this->showMainMenu($chatId, $user);
        }
        elseif (strpos($data, 'report_reason_') === 0) {
            $reasonKey = str_replace('report_reason_', '', $data);
            if ($reasonKey === 'cancel') {
                $this->clearReportPending($user);
                $this->sendMessage($chatId, 'Report canceled.');
                $this->showMainMenu($chatId, $user);
                return;
            }

            $pending = $this->getReportPending($user);
            if (!$pending || empty($pending['target_id'])) {
                $this->sendMessage($chatId, 'No report in progress.');
                return;
            }

            $target = User::find($pending['target_id']);
            if (!$target) {
                $this->clearReportPending($user);
                $this->sendMessage($chatId, 'The reported user is no longer available.');
                $this->showMainMenu($chatId, $user);
                return;
            }

            if ($reasonKey === 'other') {
                $this->setReportPending($user, $target->id, 'custom_reason');
                $this->sendMessage($chatId, 'Please type why you are reporting this user (or type cancel).');
                return;
            }

            $reasonMap = [
                'scammer' => 'Scammer',
                'pervert' => 'Pervert',
            ];
            $reason = $reasonMap[$reasonKey] ?? ucfirst($reasonKey);

            $this->notifyAdminsReport($user, $target, $reason);
            $this->clearReportPending($user);
            $this->sendMessage($chatId, 'Report submitted to admins.');
            $this->showMainMenu($chatId, $user);
        }
        // ===== EDIT PROFILE =====
        elseif ($data === 'edit_gender') {
            $inlineKeyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'Male', 'callback_data' => 'gender_male'],
                        ['text' => 'Female', 'callback_data' => 'gender_female'],
                    ]
                ]
            ];

            Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'reply_markup' => json_encode($inlineKeyboard),
                'text' => 'Select your gender:'
            ]);
        }

        elseif ($data === 'edit_pace') {
            $this->sendPaceOptions($chatId, "Select your new average pace:");
            $user->update(['telegram_state' => 'waiting_pace']);
        }

        elseif ($data === 'edit_location') {
            $this->setLocationUpdatePending($user);
            $this->sendLocationRequest($chatId, "Share your current location:", $user);
        }
        elseif ($data === 'edit_photo') {
            $user->update(['telegram_state' => 'waiting_photo']);
            $this->setPhotoUpdatePending($user);
            $this->sendMessage($chatId, 'Please send your profile photo.');
        }
        elseif ($data === 'delete_photo') {
            if ($user->strava_screenshot && Storage::disk('public')->exists($user->strava_screenshot)) {
                Storage::disk('public')->delete($user->strava_screenshot);
            }

            $user->update([
                'strava_screenshot' => null,
                'telegram_state' => 'waiting_photo'
            ]);

            $this->sendMessage($chatId, 'Your profile photo was deleted. Please upload a new one to complete your profile.');
        }
    }

    // ==========================================
    // === 10. HELPER FUNCTIONS ===
    // ==========================================

    private function getUserPhotoUrl($user)
    {
        if (!$user->strava_screenshot) {
            return null;
        }

        if (preg_match('#^https?://#', $user->strava_screenshot)) {
            return $user->strava_screenshot;
        }

        if (!Storage::disk('public')->exists($user->strava_screenshot)) {
            return null;
        }

        return Storage::disk('public')->url($user->strava_screenshot);
    }

    private function getUserPhotoPath($user)
    {
        if (!$user->strava_screenshot) {
            return null;
        }

        if (!Storage::disk('public')->exists($user->strava_screenshot)) {
            return null;
        }

        return Storage::disk('public')->path($user->strava_screenshot);
    }

    private function sendPhotoUrl($chatId, $photoUrl, $caption = null, $replyMarkup = null)
    {
        $payload = [
            'chat_id' => $chatId,
            'photo' => $photoUrl
        ];

        if ($caption !== null) {
            $payload['caption'] = $caption;
            $payload['parse_mode'] = 'HTML';
        }

        if ($replyMarkup !== null) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }

        return Http::withoutVerifying()->post("{$this->apiUrl}/sendPhoto", $payload);
    }

    private function sendMessage($chatId, $text)
    {
        return Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ]);
    }

    private function sendMenuMessage($chatId): void
    {
        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => '<b>Menu</b>',
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'My profile', 'callback_data' => 'menu_profile'],
                        ['text' => 'My schedule', 'callback_data' => 'menu_schedule'],
                    ],
                    [
                        ['text' => 'Find buddy', 'callback_data' => 'menu_find_buddy'],
                        ['text' => 'Buddy requests', 'callback_data' => 'menu_buddy_requests'],
                    ],
                    [
                        ['text' => 'Join nearby run', 'callback_data' => 'menu_join_nearby'],
                        ['text' => 'Create running session', 'callback_data' => 'menu_create_session'],
                    ],
                ],
            ]),
        ]);
    }

    private function showReviewOptions($chatId, $user): void
    {
        $sessions = $this->getReviewableSessions($user);
        if ($sessions->isEmpty()) {
            $this->sendMessage($chatId, 'No sessions available for review yet.');
            return;
        }

        $rows = [];
        foreach ($sessions as $session) {
            $label = \Carbon\Carbon::parse($session->start_time)->format('M d') . ' - ' . ($session->location_name ?? 'Session');
            $rows[] = [
                ['text' => $label, 'callback_data' => 'review_select_' . $session->session_id],
            ];
        }

        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => 'Choose a session to review:',
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => $rows]),
        ]);
    }

    private function sendReviewRatingOptions($chatId, int $sessionId): void
    {
        $row1 = [];
        $row2 = [];
        for ($i = 1; $i <= 5; $i++) {
            $button = ['text' => str_repeat('⭐', $i), 'callback_data' => 'review_rate_' . $sessionId . '_' . $i];
            if ($i <= 3) {
                $row1[] = $button;
            } else {
                $row2[] = $button;
            }
        }

        $cancelRow = [
            ['text' => 'Cancel', 'callback_data' => 'review_rate_cancel'],
        ];

        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => 'Tap a star to rate:',
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => [$row1, $row2, $cancelRow]]),
        ]);
    }

    private function getReviewableSessions($user)
    {
        $now = now();
        return RunningSession::query()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('joinedUsers', function ($sub) use ($user) {
                        $sub->where('user_id', $user->id);
                    });
            })
            ->where('end_time', '<', $now)
            ->whereDoesntHave('reviews', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderByDesc('start_time')
            ->limit(5)
            ->get();
    }

    private function storeTelegramReview($user, int $sessionId, int $rating, string $comment): bool
    {
        $rating = max(1, min(5, $rating));
        $session = RunningSession::find($sessionId);
        if (!$session) {
            return false;
        }

        $isOwner = $session->user_id === $user->id;
        $joined = JoinedSession::where('session_id', $sessionId)
            ->where('user_id', $user->id)
            ->exists();

        if (!$isOwner && !$joined) {
            return false;
        }

        if ($session->end_time >= now()) {
            return false;
        }

        $exists = SessionReview::where('running_session_id', $sessionId)
            ->where('user_id', $user->id)
            ->exists();
        if ($exists) {
            return false;
        }

        SessionReview::create([
            'running_session_id' => $sessionId,
            'user_id' => $user->id,
            'rating' => $rating,
            'comment' => $comment,
        ]);

        return true;
    }

    private function notifyAdminsReview(User $reviewer, int $sessionId, int $rating, string $comment): void
    {
        $admins = User::where('is_admin', true)
            ->whereNotNull('telegram_id')
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        $session = RunningSession::find($sessionId);
        $sessionInfo = $session ? ($session->location_name ?? 'Unknown location') : 'Unknown session';

        $message = "<b>New Session Review</b>\n";
        $message .= "Session ID: {$sessionId}\n";
        $message .= "Location: {$sessionInfo}\n";
        $message .= "Reviewer: {$reviewer->name} (ID {$reviewer->id})\n";
        $message .= "Rating: {$rating}/5\n";
        $message .= "Comment: {$comment}\n";
        $message .= "Time: " . Carbon::now()->format('Y-m-d H:i');

        foreach ($admins as $admin) {
            Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $admin->telegram_id,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);
        }
    }

    private function removeReplyKeyboard($chatId): void
    {
        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => ' ',
            'reply_markup' => json_encode(['remove_keyboard' => true]),
        ]);
    }

    private function sendPhoto($chatId, $photoPath)
    {
        return Http::withoutVerifying()->post("{$this->apiUrl}/sendPhoto", [
            'chat_id' => $chatId,
            'photo' => new \CURLFile($photoPath)
        ]);
    }

    private function extractCoordinates($location)
    {
        if (!$location) {
            return null;
        }

        $data = is_array($location) ? $location : json_decode($location, true);
        if (!is_array($data)) {
            return null;
        }

        $lat = $data['latitude'] ?? $data['lat'] ?? null;
        $lon = $data['longitude'] ?? $data['lon'] ?? null;

        if (!is_numeric($lat) || !is_numeric($lon)) {
            return null;
        }

        return [
            'lat' => (float) $lat,
            'lon' => (float) $lon,
        ];
    }

    private function getReportReasons(): array
    {
        return [
            'Scammer',
            'Pervert',
            'Other',
        ];
    }

    private function getReviewPendingKey($user): string
    {
        return 'tg_review_pending_' . $user->id;
    }

    private function setReviewPending($user, int $sessionId, ?int $rating, string $step): void
    {
        Cache::put($this->getReviewPendingKey($user), [
            'session_id' => $sessionId,
            'rating' => $rating,
            'step' => $step,
        ], now()->addMinutes(30));
    }

    private function getReviewPending($user): ?array
    {
        return Cache::get($this->getReviewPendingKey($user));
    }

    private function clearReviewPending($user): void
    {
        Cache::forget($this->getReviewPendingKey($user));
    }

    private function getLikeInboxKey($user): string
    {
        return 'tg_like_inbox_' . $user->id;
    }

    private function getLikeInbox($user): array
    {
        $items = Cache::get($this->getLikeInboxKey($user), []);
        return is_array($items) ? $items : [];
    }

    private function pushLikeInbox($user, int $likerId): void
    {
        $items = $this->getLikeInbox($user);
        if (!in_array($likerId, $items, true)) {
            $items[] = $likerId;
        }
        Cache::put($this->getLikeInboxKey($user), $items, now()->addDays(3));
    }

    private function popLikeInbox($user): ?int
    {
        $items = $this->getLikeInbox($user);
        if (empty($items)) {
            return null;
        }
        $next = array_shift($items);
        Cache::put($this->getLikeInboxKey($user), $items, now()->addDays(3));
        return is_numeric($next) ? (int) $next : null;
    }

    private function clearLikeInbox($user): void
    {
        Cache::forget($this->getLikeInboxKey($user));
    }

    private function sendLikePrompt($chatId, int $likeCount): void
    {
        $text = "Someone liked your profile. Have a look?\n"
            . "Total likes: {$likeCount}\n\n"
            . "Choose an option below.";

        $keyboard = [
            'keyboard' => [
                [['text' => 'Show likes']],
                [['text' => 'Not searching anymore']],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ];

        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode($keyboard),
        ]);
    }

    private function sendLikedProfile($chatId, $viewer, $liker): void
    {
        $locationText = $liker->formatLocationText('Not specified');
        $profileInfo = "<b>{$liker->name}</b>\n\n";
        $profileInfo .= "Gender: " . ($liker->gender ?? 'Not specified') . "\n";
        $profileInfo .= "Pace: " . ($liker->avg_pace ?? 'Not specified') . "\n";
        $profileInfo .= "Location: {$locationText}\n";

        $viewerCoords = $this->extractCoordinates($viewer->location);
        $likerCoords = $this->extractCoordinates($liker->location);
        if ($viewerCoords && $likerCoords) {
            $distance = $this->calculateDistance(
                $viewerCoords['lat'],
                $viewerCoords['lon'],
                $likerCoords['lat'],
                $likerCoords['lon']
            );
            $profileInfo .= "Distance: " . number_format($distance, 1) . " km\n";
        }

        $photoUrl = $this->getUserPhotoUrl($liker);
        if ($photoUrl && strpos($photoUrl, 'https://') === 0) {
            $this->sendPhotoUrl($chatId, $photoUrl, $profileInfo);
        } else {
            $this->sendMessage($chatId, $profileInfo);
        }

        if ($liker->telegram_id) {
            $chatUrl = $liker->telegram_username
                ? "https://t.me/{$liker->telegram_username}"
                : "tg://user?id={$liker->telegram_id}";

            $label = $liker->telegram_username ? '@' . $liker->telegram_username : $liker->name;
            $message = "Start chatting 👉 <a href=\"{$chatUrl}\">{$label}</a>";

            Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => 'Chat now', 'url' => $chatUrl],
                        ],
                    ],
                ]),
            ]);
        }
    }

    private function getReportPendingKey($user): string
    {
        return 'tg_report_pending_' . $user->id;
    }

    private function setReportPending($user, int $targetId, string $step = 'select'): void
    {
        Cache::put($this->getReportPendingKey($user), [
            'target_id' => $targetId,
            'step' => $step,
        ], now()->addMinutes(15));
    }

    private function getReportPending($user): ?array
    {
        return Cache::get($this->getReportPendingKey($user));
    }

    private function clearReportPending($user): void
    {
        Cache::forget($this->getReportPendingKey($user));
    }

    private function sendReportReasonPrompt($chatId): void
    {
        $message = "Select a report reason:";

        $inlineKeyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'Scammer', 'callback_data' => 'report_reason_scammer'],
                    ['text' => 'Pervert', 'callback_data' => 'report_reason_pervert'],
                    ['text' => 'Other', 'callback_data' => 'report_reason_other'],
                ],
                [
                    ['text' => 'Cancel', 'callback_data' => 'report_reason_cancel'],
                ]
            ],
        ];

        Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode($inlineKeyboard),
        ]);
    }

    private function notifyAdminsReport(User $reporter, User $target, string $reason): void
    {
        UserReport::create([
            'reporter_id' => $reporter->id,
            'target_id' => $target->id,
            'reason' => $reason,
        ]);

        $admins = User::where('is_admin', true)
            ->whereNotNull('telegram_id')
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        $message = "<b>Profile Report</b>\n"
            . "Reporter: {$reporter->name} (ID {$reporter->id})\n"
            . "Reported: {$target->name} (ID {$target->id})\n"
            . "Reason: {$reason}\n"
            . "Time: " . Carbon::now()->format('Y-m-d H:i');

        foreach ($admins as $admin) {
            Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $admin->telegram_id,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);
        }
    }

    private function getBuddyQueueKey($user)
    {
        return 'tg_buddy_queue_' . $user->id;
    }

    private function getBuddyQueue($user)
    {
        return Cache::get($this->getBuddyQueueKey($user));
    }

    private function setBuddyQueue($user, array $queue)
    {
        Cache::put($this->getBuddyQueueKey($user), $queue, now()->addMinutes(30));
    }

    private function clearBuddyQueue($user)
    {
        Cache::forget($this->getBuddyQueueKey($user));
    }

    private function showNextBuddy($chatId, $user)
    {
        $queue = $this->getBuddyQueue($user);
        if (!$queue || empty($queue['items'])) {
            $this->sendMessage($chatId, 'No more runners available right now.');
            return;
        }

        $index = $queue['index'] ?? 0;
        if ($index >= count($queue['items'])) {
            // Loop back to the beginning so users can keep browsing, even after dislikes.
            $queue['index'] = 0;
            $this->setBuddyQueue($user, $queue);
            $this->sendMessage($chatId, 'You reached the end of the list. Cycling back to the start.');
            $this->showNextBuddy($chatId, $user);
            return;
        }

        $item = $queue['items'][$index];
        $queue['index'] = $index + 1;
        $this->setBuddyQueue($user, $queue);

        $buddy = User::find($item['id']);
        if (!$buddy) {
            $this->showNextBuddy($chatId, $user);
            return;
        }

        // Get location details
        $locationText = $buddy->formatLocationText('Not specified');

        // Format buddy info
        $buddyInfo = "<b>{$buddy->name}</b>\n\n";
        $buddyInfo .= "Gender: " . ($buddy->gender ?? 'Not specified') . "\n";
        $buddyInfo .= "Pace: {$buddy->avg_pace}\n";
        $buddyInfo .= "Location: {$locationText}\n";
        $buddyInfo .= "Distance: " . number_format($item['distance'], 1) . " km";

        $inlineKeyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'Like', 'callback_data' => "buddy_like_{$buddy->id}"],
                    ['text' => 'Dislike', 'callback_data' => "buddy_dislike_{$buddy->id}"],
                    ['text' => 'Stop', 'callback_data' => 'buddy_stop'],
                ]
            ]
        ];

        // Send photo with buddy info as caption (fallback to text on failure)
        $photoUrl = $this->getUserPhotoUrl($buddy);
        $photoSent = false;
        if ($photoUrl && strpos($photoUrl, 'https://') === 0) {
            $response = $this->sendPhotoUrl($chatId, $photoUrl, $buddyInfo, $inlineKeyboard);
            $photoSent = $response && $response->ok();
        }

        if (!$photoSent) {
            // If no photo or sendPhoto failed, send text message with buttons
            Http::withoutVerifying()->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $buddyInfo,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode($inlineKeyboard)
            ]);
        }
    }

    /**
     * Calculate distance between two coordinates (Haversine formula)
     * @return float Distance in kilometers
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadiusKm * $c;

        return $distance;
    }

    // ==========================================
    // === 11. WEBHOOK SETUP ===
    // ==========================================

    public function setWebhook()
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $url = $baseUrl . '/api/telegram/webhook';
        $response = Http::withoutVerifying()->get("{$this->apiUrl}/setWebhook?url={$url}");
        return $response->json();
    }

    public function getWebhookInfo()
    {
        $response = Http::withoutVerifying()->get("{$this->apiUrl}/getWebhookInfo");
        return $response->json();
    }
}
















