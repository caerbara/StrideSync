<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuddyLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'liker_id',
        'liked_id',
        'status',
    ];
}
