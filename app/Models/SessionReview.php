<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'running_session_id',
        'user_id',
        'rating',
        'comment',
        'is_featured',
        'featured_at',
    ];

    public function session()
    {
        return $this->belongsTo(RunningSession::class, 'running_session_id', 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


