<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RunningSession;
use Carbon\Carbon;
use App\Models\User;


class AdminController extends Controller
{
    public function dashboard()
    {
        $sessions = RunningSession::with('user', 'joinedUsers.user')->latest()->get();
        $pastSessions = RunningSession::where('end_time', '<', Carbon::now())
            ->with('user')
            ->latest()
            ->get();

        $users = User::where('is_admin', false)->latest()->get();


        return view('admin.dashboard', compact('sessions', 'pastSessions', 'users'));
    }
}

