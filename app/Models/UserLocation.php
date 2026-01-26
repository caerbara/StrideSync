<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    protected $fillable = ['telegram_user_id', 'lat', 'lng'];
}
