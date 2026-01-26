<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\EmailTacService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function show()
    {
        return view('auth.forgot-password');
    }

    public function reset(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'tac_code' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $tacService = app(EmailTacService::class);
        $tacResult = $tacService->verifyTac($validated['email'], 'forgot_password', $validated['tac_code']);
        if (!$tacResult['ok']) {
            return back()->withErrors(['tac_code' => $tacResult['message']])->withInput();
        }

        $user = User::where('email', $validated['email'])->firstOrFail();
        $user->update(['password' => Hash::make($validated['password'])]);

        return redirect()->route('login')->with('success', 'Password reset successfully. Please log in.');
    }
}
