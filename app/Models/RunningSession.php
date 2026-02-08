<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RunningSession extends Model
{
    use HasFactory;

    protected $primaryKey = 'session_id';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'average_pace',
        'duration',
        'activity',
        'location_name',
        'location_lat',
        'location_lng',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRouteKeyName()
    {
        return 'session_id';
    }

    public function joinedUsers()
    {
        return $this->hasMany(JoinedSession::class, 'session_id');
    }

    public function reviews()
    {
        return $this->hasMany(SessionReview::class, 'running_session_id', 'session_id');
    }

    public static function estimateDistanceKmFromActivity(?string $activity): ?float
    {
        $activity = trim((string) $activity);
        if ($activity === '') {
            return null;
        }

        if (preg_match('/(\d+(?:\.\d+)?)\s*km/i', $activity, $m)) {
            return (float) $m[1];
        }

        $normalized = strtolower($activity);
        if (in_array($normalized, ['5km', '5k'], true)) {
            return 5.0;
        }
        if (in_array($normalized, ['10km', '10k'], true)) {
            return 10.0;
        }
        if (strpos($normalized, 'long run') !== false) {
            return 15.0;
        }
        if (strpos($normalized, 'interval') !== false) {
            return 4.0;
        }

        return null;
    }
}


