<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class user_setting extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'two_factor_auth' => 'boolean',
        'profile_visible' => 'boolean',
        'online_status' => 'boolean',
        'achievements_visible' => 'boolean',
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
    ];

}
