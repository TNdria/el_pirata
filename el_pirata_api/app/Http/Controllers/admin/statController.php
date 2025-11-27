<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\enigmas;
use App\Models\hunting;
use App\Models\User;
use Illuminate\Http\Request;

class statController extends Controller
{
    public function getStats()
    {
        return response()->json([
            'enigmes' => enigmas::count(),
            'user' => User::count(),
            'hunt' => hunting::count(),
            'notes' => 0,
        ]);
    }
}
