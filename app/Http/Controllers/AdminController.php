<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RunningSession;
use App\Models\JoinedSession;
use Carbon\Carbon;
use App\Models\User;
use App\Models\SessionReview;
use Illuminate\Support\Facades\Cache;


class AdminController extends Controller
{
    /**
     * Show the admin dashboard with monitoring data
     */
    public function dashboard()
    {
        // User statistics
        $totalUsers = User::where('is_admin', false)->count();
        $recentUsers = User::where('is_admin', false)->latest()->take(10)->get();
        $usersWithTelegram = User::where('is_admin', false)->whereNotNull('telegram_id')->count();
        $usersWithCompleteProfile = User::where('is_admin', false)
            ->whereNotNull('gender')
            ->whereNotNull('avg_pace')
            ->whereNotNull('location')
            ->count();

        // Session statistics
        $activeSessions = RunningSession::where('start_time', '<=', Carbon::now())
            ->where('end_time', '>=', Carbon::now())
            ->with('user', 'joinedUsers.user')
            ->latest()
            ->get();

        $upcomingSessions = RunningSession::where('start_time', '>', Carbon::now())
            ->with('user', 'joinedUsers.user')
            ->latest()
            ->take(10)
            ->get();

        $pastSessions = RunningSession::where('end_time', '<', Carbon::now())
            ->with('user', 'joinedUsers.user')
            ->latest()
            ->take(10)
            ->get();

        $totalSessions = RunningSession::count();
        $totalParticipations = JoinedSession::count();

        // Telegram bot statistics
        $telegramUsersRegistered = User::where('is_admin', false)
            ->whereNotNull('telegram_id')
            ->count();
        $telegramUsersWithoutProfile = User::where('is_admin', false)
            ->whereNotNull('telegram_id')
            ->where(function ($query) {
                $query->whereNull('gender')
                    ->orWhereNull('avg_pace')
                    ->orWhereNull('location');
            })
            ->count();

        // User registration timeline (last 30 days)
        $registrationTrend = User::where('is_admin', false)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Most active users (by session participation)
        $mostActiveUsers = User::where('is_admin', false)
            ->withCount('runningSessions')
            ->orderByDesc('running_sessions_count')
            ->take(10)
            ->get();

        $recentReviews = SessionReview::with(['user', 'session.user'])
            ->latest()
            ->take(20)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'recentUsers',
            'usersWithTelegram',
            'usersWithCompleteProfile',
            'activeSessions',
            'upcomingSessions',
            'pastSessions',
            'totalSessions',
            'totalParticipations',
            'telegramUsersRegistered',
            'telegramUsersWithoutProfile',
            'registrationTrend',
            'mostActiveUsers',
            'recentReviews'
        ));
    }

    /**
     * Get JSON data for dashboard statistics (for AJAX calls)
     */
    public function getStats()
    {
        $totalUsers = User::where('is_admin', false)->count();
        $usersWithTelegram = User::where('is_admin', false)->whereNotNull('telegram_id')->count();
        $activeSessions = RunningSession::where('start_time', '<=', Carbon::now())
            ->where('end_time', '>=', Carbon::now())
            ->count();
        $totalParticipations = JoinedSession::count();

        return response()->json([
            'totalUsers' => $totalUsers,
            'usersWithTelegram' => $usersWithTelegram,
            'activeSessions' => $activeSessions,
            'totalParticipations' => $totalParticipations,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * View user details
     */
    public function viewUser($id)
    {
        $user = User::with('runningSessions', 'joinedSessions')->findOrFail($id);
        
        return view('admin.user-detail', compact('user'));
    }

    /**
     * View session details
     */
    public function viewSession($id)
    {
        $session = RunningSession::with('user', 'joinedUsers.user')->findOrFail($id);
        
        return view('admin.session-detail', compact('session'));
    }

    public function deleteReview(SessionReview $review)
    {
        $review->delete();
        Cache::forget('welcome.reviews');

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Review deleted.');
    }

    public function toggleReviewFeatured(SessionReview $review)
    {
        if (! $review->is_featured) {
            $featuredCount = SessionReview::where('is_featured', true)->count();
            if ($featuredCount >= 5) {
                return redirect()
                    ->route('admin.dashboard')
                    ->with('error', 'Only 5 reviews can be featured at a time.');
            }
            $review->is_featured = true;
            $review->featured_at = now();
        } else {
            $review->is_featured = false;
            $review->featured_at = null;
        }

        $review->save();
        Cache::forget('welcome.reviews');

        return redirect()
            ->route('admin.dashboard')
            ->with('success', $review->is_featured ? 'Review featured.' : 'Review unfeatured.');
    }
}


