<?php

namespace App\Http\Controllers;

use App\Models\User; // ✅ CORRECT WAY to import the User model
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class RegisterController extends Controller
{
    public function register(Request $request)
    {
        Log::info('➡️ Register form submitted:', $request->all());


        // email registere validate
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        Log::info('✅ Validation passed');

        // create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        Log::info('✅ User created:', ['id' => $user->id]);

        auth()->login($user);
        Log::info('✅ Logged in and redirecting to dashboard');

        return redirect()->route('user.dashboard');
    }

}
