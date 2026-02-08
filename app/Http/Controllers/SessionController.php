<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JoinedSession;

class SessionController extends Controller
{
    public function join($session_id)
    {
        $user_id = Auth::id();

        // Avoid duplicate joins
        $alreadyJoined = JoinedSession::where('session_id', $session_id)
            ->where('user_id', $user_id)
            ->exists();

        if (!$alreadyJoined) {
            JoinedSession::create([
                'session_id' => $session_id,
                'user_id' => $user_id,
            ]);
        }

        return redirect()->back()->with('success', 'You have joined the session.');
    }

    public function leave($session_id)
    {
        $user_id = Auth::id();

        JoinedSession::where('session_id', $session_id)
            ->where('user_id', $user_id)
            ->delete();

        return redirect()->back()->with('success', 'You have left the session.');
    }
}


