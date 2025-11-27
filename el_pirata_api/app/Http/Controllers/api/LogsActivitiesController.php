<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\activities_logs;
use Illuminate\Http\Request;

class LogsActivitiesController extends Controller
{

    public function all()
    {
        try {
            $list = activities_logs::with(['admin:id,name,email'])->get();
            return response()->json(['list' => $list]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
