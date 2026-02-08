<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RunningSession;
use App\Models\JoinedSession;
use Illuminate\Support\Facades\Auth;

class RunningSessionController extends Controller
{
    public function dashboard()
    {
        $sessions = RunningSession::with(['user', 'joinedUsers.user']) // include joined users
        ->orderBy('start_time', 'desc')
            ->get();

        return view('user.dashboard', compact('sessions'));
    }


    public function join($session_id)
    {
        $user_id = Auth::id();

        // Check if already joined
        $alreadyJoined = JoinedSession::where('session_id', $session_id)
            ->where('user_id', $user_id)
            ->exists();

        if (!$alreadyJoined) {
            JoinedSession::create([
                'user_id' => $user_id,
                'session_id' => $session_id,
            ]);
        }

        return redirect()->back()->with('success', 'You have joined the session!');
    }

    public function joinedUsers()
    {
        return $this->belongsToMany(User::class, 'joined_sessions', 'session_id', 'user_id');
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'location_name' => 'required|string',
            'average_pace' => 'required|string',
            'duration' => 'required|string',
        ]);

        RunningSession::create([
            'user_id' => Auth::id(),
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'location_name' => $request->location_name,
            'average_pace' => $request->average_pace,
            'duration' => $request->duration,
        ]);

        return redirect('/user/dashboard')->with('success', 'Session created successfully!');
    }

    public function destroy(RunningSession $runningSession)
    {
        $runningSession->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Session deleted.');
    }




}

