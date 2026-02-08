<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'telegram_id',
        'telegram_username',
        'gender',
        'avg_pace',
        'location',
        'strava_screenshot',
        'telegram_state',
        'is_admin',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
    public function runningSessions()
    {
        return $this->hasMany(RunningSession::class, 'user_id');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->strava_screenshot) {
            return null;
        }

        if (preg_match('#^https?://#', $this->strava_screenshot)) {
            return $this->strava_screenshot;
        }

        return asset('storage/' . $this->strava_screenshot);
    }

    public function formatLocationText(string $fallback = 'Not set'): string
    {
        $locationValue = $this->location;
        if (!$locationValue) {
            return $fallback;
        }

        $decoded = json_decode($locationValue, true);
        if (is_array($decoded)) {
            $city = $decoded['city'] ?? null;
            if (is_string($city) && trim($city) !== '') {
                return trim($city);
            }

            $state = $decoded['state'] ?? null;
            if (is_string($state) && trim($state) !== '') {
                return trim($state);
            }

            $lat = $decoded['latitude'] ?? null;
            $lon = $decoded['longitude'] ?? null;
            if (is_numeric($lat) && is_numeric($lon)) {
                $latText = rtrim(rtrim(number_format((float) $lat, 5, '.', ''), '0'), '.');
                $lonText = rtrim(rtrim(number_format((float) $lon, 5, '.', ''), '0'), '.');
                return "{$latText}, {$lonText}";
            }

            $name = $decoded['name'] ?? null;
            if (is_string($name) && trim($name) !== '') {
                return trim($name);
            }
        } elseif (is_string($decoded) && trim($decoded) !== '') {
            return trim($decoded);
        }

        if (is_string($locationValue) && trim($locationValue) !== '') {
            return trim($locationValue);
        }

        return $fallback;
    }


}


