<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    private ?string $cloudName;
    private ?string $apiKey;
    private ?string $apiSecret;
    private ?string $folder;

    public function __construct()
    {
        $config = config('services.cloudinary', []);
        $this->cloudName = $config['cloud_name'] ?? null;
        $this->apiKey = $config['api_key'] ?? null;
        $this->apiSecret = $config['api_secret'] ?? null;
        $this->folder = $config['folder'] ?? null;
    }

    public function isConfigured(): bool
    {
        return !empty($this->cloudName) && !empty($this->apiKey) && !empty($this->apiSecret);
    }

    public function uploadBytes(string $bytes, string $filename, ?string $folder = null): ?string
    {
        if (!$this->isConfigured()) {
            Log::warning('Cloudinary not configured', [
                'cloud_name' => $this->cloudName,
                'api_key_present' => !empty($this->apiKey),
                'api_secret_present' => !empty($this->apiSecret),
            ]);
            return null;
        }

        $targetFolder = $folder ?? $this->folder;
        $timestamp = time();
        $params = [
            'timestamp' => $timestamp,
        ];

        if (!empty($targetFolder)) {
            $params['folder'] = $targetFolder;
        }

        $signature = $this->signParams($params);

        $response = Http::withoutVerifying()->asMultipart()
            ->attach('file', $bytes, $filename)
            ->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload", array_merge($params, [
                'api_key' => $this->apiKey,
                'signature' => $signature,
            ]));

        if (!$response->ok()) {
            Log::error('Cloudinary upload failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        $payload = $response->json();
        $url = $payload['secure_url'] ?? $payload['url'] ?? null;
        Log::info('Cloudinary upload success', [
            'url' => $url,
            'public_id' => $payload['public_id'] ?? null,
        ]);
        return $url;
    }

    public function uploadUploadedFile($file, ?string $folder = null): ?string
    {
        if (!$file) {
            return null;
        }

        $path = $file->getRealPath();
        if (!$path || !is_readable($path)) {
            return null;
        }

        $bytes = file_get_contents($path);
        if ($bytes === false) {
            return null;
        }

        $filename = $file->getClientOriginalName() ?: ('profile_' . time() . '.jpg');
        return $this->uploadBytes($bytes, $filename, $folder);
    }

    private function signParams(array $params): string
    {
        ksort($params);
        $pairs = [];
        foreach ($params as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $pairs[] = $key . '=' . $value;
        }

        $toSign = implode('&', $pairs);
        return sha1($toSign . $this->apiSecret);
    }
}


