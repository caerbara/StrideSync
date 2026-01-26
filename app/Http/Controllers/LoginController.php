<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RunningSession;


class LoginController extends Controller
{
    public function login(Request $request)
    {
        // email validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // for credential
        $credentials = $request->only('email', 'password');

        // log in
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->is_admin) {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('user.dashboard');
            }
        }

        // if failed auth
        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->withInput();
    }
}
