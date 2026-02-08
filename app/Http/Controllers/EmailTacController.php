<?php

namespace App\Http\Controllers;

use App\Services\EmailTacService;
use Illuminate\Http\Request;

class EmailTacController extends Controller
{
    private EmailTacService $tacService;

    public function __construct(EmailTacService $tacService)
    {
        $this->tacService = $tacService;
    }

    public function sendRegisterTac(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $result = $this->tacService->sendTac($validated['email'], 'register');
        return response()->json([
            'success' => $result['ok'],
            'message' => $result['message'],
        ]);
    }

    public function sendForgotTac(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $result = $this->tacService->sendTac($validated['email'], 'forgot_password');
        return response()->json([
            'success' => $result['ok'],
            'message' => $result['message'],
        ]);
    }
}


