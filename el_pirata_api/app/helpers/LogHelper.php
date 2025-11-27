<?php

namespace App\helpers;

use App\Models\activities_logs;
use Illuminate\Support\Str;

class LogHelper
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function logAction($user, $model, $action)
    {
        activities_logs::create([
            'id' => (string) Str::uuid(),
            'admin_id' => $user->id,
            'table_id' => $model->id,
            'table_type' => get_class($model),
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
