<?php

namespace App\Services;

use App\Models\EmailTac;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class EmailTacService
{
    public function sendTac(string $email, string $purpose): array
    {
        $latest = EmailTac::where('email', $email)
            ->where('purpose', $purpose)
            ->orderByDesc('created_at')
            ->first();

        if ($latest && $latest->created_at->diffInSeconds(now()) < 60) {
            return ['ok' => false, 'message' => 'Please wait a minute before requesting another code.'];
        }

        EmailTac::where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->delete();

        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        EmailTac::create([
            'email' => $email,
            'code_hash' => Hash::make($code),
            'purpose' => $purpose,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        $subject = $purpose === 'forgot_password'
            ? 'Reset your StrideSync password'
            : 'Verify your StrideSync email';

        $intro = $purpose === 'forgot_password'
            ? 'We received a request to reset your StrideSync password.'
            : 'Thanks for registering with StrideSync.';

        $plain = "Hello,\n\n{$intro}\n\nYour TAC code is: {$code}\n\nThis code expires in 10 minutes.\n\nIf you did not request this, you can ignore this email.\n\nThanks,\nStrideSync Team";

        $html = '<!doctype html><html><body style="margin:0;padding:0;background:#f4f5f7;font-family:Arial,sans-serif;color:#111;">'
            . '<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f4f5f7;padding:24px 0;">'
            . '<tr><td align="center">'
            . '<table width="560" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">'
            . '<tr><td style="padding:20px 24px;background:#0f172a;color:#ffffff;font-size:18px;font-weight:bold;">StrideSync</td></tr>'
            . '<tr><td style="padding:24px;">'
            . '<h1 style="margin:0 0 12px;font-size:20px;">' . $subject . '</h1>'
            . '<p style="margin:0 0 16px;font-size:14px;line-height:1.5;color:#374151;">' . $intro . '</p>'
            . '<div style="padding:16px;border:1px dashed #9ca3af;border-radius:10px;text-align:center;font-size:22px;font-weight:bold;letter-spacing:4px;color:#111827;">' . $code . '</div>'
            . '<p style="margin:16px 0 0;font-size:13px;color:#6b7280;">This code expires in 10 minutes.</p>'
            . '<p style="margin:16px 0 0;font-size:13px;color:#6b7280;">If you did not request this, you can ignore this email.</p>'
            . '</td></tr>'
            . '<tr><td style="padding:16px 24px;background:#f9fafb;color:#6b7280;font-size:12px;">'
            . 'Need help? Contact support at support@stridesync.local'
            . '</td></tr>'
            . '</table>'
            . '</td></tr></table></body></html>';

        if (config('services.sendgrid.use_api')) {
            return $this->sendViaSendgridApi($email, $subject, $plain, $html);
        }

        try {
            Mail::html($html, function ($message) use ($email, $subject, $plain) {
                $message->to($email)->subject($subject);
                $message->text($plain);
            });
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Unable to send TAC email.'];
        }

        return ['ok' => true, 'message' => 'TAC code sent. Please check your email.'];
    }

    public function verifyTac(string $email, string $purpose, string $code): array
    {
        $record = EmailTac::where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->orderByDesc('created_at')
            ->first();

        if (!$record) {
            return ['ok' => false, 'message' => 'No TAC request found.'];
        }

        if ($record->expires_at->isPast()) {
            return ['ok' => false, 'message' => 'TAC code expired.'];
        }

        if ($record->attempts >= 5) {
            return ['ok' => false, 'message' => 'Too many attempts. Please request a new code.'];
        }

        $record->increment('attempts');

        if (!Hash::check($code, $record->code_hash)) {
            return ['ok' => false, 'message' => 'Invalid TAC code.'];
        }

        $record->update(['used_at' => now()]);
        return ['ok' => true, 'message' => 'TAC verified.'];
    }

    private function sendViaSendgridApi(string $email, string $subject, string $plain, string $html): array
    {
        $apiKey = config('services.sendgrid.key');
        if (!$apiKey) {
            return ['ok' => false, 'message' => 'SendGrid API key is missing.'];
        }

        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name');

        $payload = [
            'personalizations' => [
                [
                    'to' => [['email' => $email]],
                ],
            ],
            'from' => [
                'email' => $fromAddress,
                'name' => $fromName,
            ],
            'subject' => $subject,
            'content' => [
                ['type' => 'text/plain', 'value' => $plain],
                ['type' => 'text/html', 'value' => $html],
            ],
        ];

        try {
            $response = Http::timeout(10)
                ->withToken($apiKey)
                ->post('https://api.sendgrid.com/v3/mail/send', $payload);

            if (!$response->successful()) {
                return ['ok' => false, 'message' => 'Unable to send TAC email.'];
            }
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Unable to send TAC email.'];
        }

        return ['ok' => true, 'message' => 'TAC code sent. Please check your email.'];
    }
}


