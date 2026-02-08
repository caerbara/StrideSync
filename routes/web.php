<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RunningSessionController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BuddyMatchController;
use App\Http\Controllers\TelegramAdminController;
use App\Http\Controllers\TelegramTestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmailTacController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\EventCalendarController;
use App\Http\Controllers\CourseController;
use App\Models\SessionReview;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\GeocodeController;
use App\Http\Controllers\DistanceController;

use App\Http\Controllers\TelegramWebhookController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $reviews = Cache::remember('welcome.reviews', now()->addMinutes(5), function () {
        return SessionReview::with(['user', 'session'])
            ->where('is_featured', true)
            ->orderByDesc('featured_at')
            ->take(5)
            ->get();
    });

    return view('welcome', [
        'reviews' => $reviews,
    ]);
})->name('welcome');

// Authentication
Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('custom.login');

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('custom.register');
Route::post('/register/send-tac', [EmailTacController::class, 'sendRegisterTac'])->name('register.send-tac');
Route::get('/reverse-geocode', [RegisterController::class, 'reverseGeocode'])->name('reverse.geocode');
Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.forgot');
Route::post('/forgot-password/send-tac', [EmailTacController::class, 'sendForgotTac'])->name('password.send-tac');
Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'reset'])->name('password.reset');
Route::get('/event-calendar', [EventCalendarController::class, 'show'])->name('event.calendar');
Route::get('/course', [CourseController::class, 'show'])->name('course');
Route::get('/geocode-location', [GeocodeController::class, 'locationSearch'])->name('geocode.location');
Route::get('/route-distance', [DistanceController::class, 'routeDistance'])->name('route.distance');

// Telegram Webhook (no CSRF protection needed for Telegram)
Route::post('/webhook/telegram', [TelegramWebhookController::class, 'handle']);

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Requires Authentication)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | User Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/user/dashboard', [RunningSessionController::class, 'dashboard'])
        ->name('user.dashboard');

    // Profile
    Route::get('/user/profile', [ProfileController::class, 'show'])->name('user.profile');
    Route::get('/user/profile/edit', [ProfileController::class, 'edit'])->name('user.profile.edit');
    Route::put('/user/profile', [ProfileController::class, 'update'])->name('user.profile.update');
    Route::post('/user/telegram/unlink', [ProfileController::class, 'unlinkTelegram'])->name('user.telegram.unlink');
    Route::delete('/user/profile', [ProfileController::class, 'destroy'])->name('user.profile.delete');

    /*
    |--------------------------------------------------------------------------
    | Admin Dashboard Management (Admin Only)
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
            ->name('admin.dashboard');
        Route::get('/admin/stats', [AdminController::class, 'getStats'])
            ->name('admin.stats');
        Route::get('/admin/users/{id}', [AdminController::class, 'viewUser'])
            ->name('admin.view-user');
        Route::get('/admin/sessions/{id}', [AdminController::class, 'viewSession'])
            ->name('admin.view-session');
        Route::delete('/admin/reviews/{review}', [AdminController::class, 'deleteReview'])
            ->name('admin.reviews.delete');
        Route::post('/admin/reviews/{review}/feature', [AdminController::class, 'toggleReviewFeatured'])
            ->name('admin.reviews.feature');
    });

    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Management (Admin Only)
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->group(function () {
        Route::get('/admin/telegram', [TelegramAdminController::class, 'index'])
            ->name('admin.telegram.index');
        Route::post('/admin/telegram/set-webhook', [TelegramAdminController::class, 'setWebhook'])
            ->name('admin.telegram.set-webhook');
        Route::post('/admin/telegram/remove-webhook', [TelegramAdminController::class, 'removeWebhook'])
            ->name('admin.telegram.remove-webhook');
        Route::post('/admin/telegram/broadcast', [TelegramAdminController::class, 'broadcast'])
            ->name('admin.telegram.broadcast');
        Route::post('/admin/telegram/send-message', [TelegramAdminController::class, 'sendMessage'])
            ->name('admin.telegram.send-message');
        Route::post('/admin/telegram/update-description', [TelegramAdminController::class, 'updateDescription'])
            ->name('admin.telegram.update-description');
        Route::post('/admin/telegram/update-short-description', [TelegramAdminController::class, 'updateShortDescription'])
            ->name('admin.telegram.update-short-description');
        Route::get('/admin/telegram/reports', [TelegramAdminController::class, 'reports'])
            ->name('admin.telegram.reports');
    });

    /*
    |--------------------------------------------------------------------------
    | Running Session Management
    |--------------------------------------------------------------------------
    */
    Route::get('/sessions/create', [RunningSessionController::class, 'create'])
        ->name('sessions.create');

    Route::post('/sessions', [RunningSessionController::class, 'store'])
        ->name('sessions.store');

    Route::get('/sessions/{running_session}/edit', [RunningSessionController::class, 'edit'])
        ->name('sessions.edit');

    Route::post('/sessions/{running_session}/stop', [RunningSessionController::class, 'stop'])
        ->name('sessions.stop');

    Route::get('/sessions/{running_session}', [RunningSessionController::class, 'show'])
        ->name('sessions.show');

    Route::put('/sessions/{running_session}', [RunningSessionController::class, 'update'])
        ->name('sessions.update');

    Route::post('/sessions/{running_session}/start', [RunningSessionController::class, 'start'])
        ->name('sessions.start');

    Route::post('/sessions/{running_session}/complete', [RunningSessionController::class, 'complete'])
        ->name('sessions.complete');

    Route::post('/sessions/{running_session}/review', [RunningSessionController::class, 'storeReview'])
        ->name('sessions.review');

    Route::post('/sessions/{session_id}/join', [SessionController::class, 'join'])
        ->name('sessions.join');
    Route::delete('/sessions/{session_id}/leave', [SessionController::class, 'leave'])
        ->name('sessions.leave');

    Route::delete('/running-sessions/{running_session}', [RunningSessionController::class, 'destroy'])
        ->name('running_sessions.destroy');

    /*
    |--------------------------------------------------------------------------
    | Buddy Match
    |--------------------------------------------------------------------------
    */
    Route::get('/buddy-match', [BuddyMatchController::class, 'getMatches'])
        ->name('buddy.matches');
    Route::post('/buddy-match/send-request/{user_id}', [BuddyMatchController::class, 'sendBuddyRequest'])
        ->name('buddy.send_request');

    /*
    |--------------------------------------------------------------------------
    | User Management (Admin Only)
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
    });
});

/*
|--------------------------------------------------------------------------
| Telegram Bot Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Telegram Test Routes (Local Testing Only)
|--------------------------------------------------------------------------
*/
Route::prefix('/test/telegram')->group(function () {
    Route::get('/start', [TelegramTestController::class, 'testStart']);
    Route::get('/name/{name?}', [TelegramTestController::class, 'testNameInput']);
    Route::get('/location/{lat?}/{lon?}', [TelegramTestController::class, 'testLocation']);
    Route::get('/pace/{pace?}', [TelegramTestController::class, 'testPaceInput']);
    Route::get('/gender/{gender?}', [TelegramTestController::class, 'testGender']);
    Route::get('/user', [TelegramTestController::class, 'getTestUser']);
    Route::get('/complete-profile', [TelegramTestController::class, 'completeProfile']);
});

/*
|--------------------------------------------------------------------------
| Telegram Bot Testing Routes (LOCAL DEVELOPMENT ONLY)
|--------------------------------------------------------------------------
*/
Route::prefix('/test')->group(function () {
    Route::get('/telegram/start', [TelegramTestController::class, 'testStart'])->name('test.telegram.start');
    Route::get('/telegram/name/{name}', [TelegramTestController::class, 'testNameInput'])->name('test.telegram.name');
    Route::get('/telegram/location', [TelegramTestController::class, 'testLocation'])->name('test.telegram.location');
    Route::get('/telegram/pace/{pace}', [TelegramTestController::class, 'testPaceInput'])->name('test.telegram.pace');
    Route::get('/telegram/gender/{gender}', [TelegramTestController::class, 'testGender'])->name('test.telegram.gender');
    Route::get('/telegram/user', [TelegramTestController::class, 'getTestUser'])->name('test.telegram.user');
});


