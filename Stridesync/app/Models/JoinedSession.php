<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JoinedSession extends Model
{
    protected $primaryKey = 'jsession_id';

    protected $fillable = [
        'session_id',
        'user_id',
        'joined_at',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function runningSession() {
        return $this->belongsTo(RunningSession::class, 'session_id');
    }
}


