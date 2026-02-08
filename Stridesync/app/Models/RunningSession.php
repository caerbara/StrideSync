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
        'location_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function joinedUsers()
    {
        return $this->hasMany(JoinedSession::class, 'session_id');
    }


}

