<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JoinedSession extends Model
{
    protected $primaryKey = 'jsession_id';

    protected $fillable = [
        'session_id',
        'user_id',
        'invited_user_id',
        'status',
        'joined_at',
    ];

    public $timestamps = false;   // <--- FIXED

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invitedUser()
    {
        return $this->belongsTo(User::class, 'invited_user_id');
    }

    public function runningSession() {
        return $this->belongsTo(RunningSession::class, 'session_id');
    }
}
