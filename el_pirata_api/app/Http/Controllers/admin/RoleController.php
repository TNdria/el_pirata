<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    //
    public function all()
    {
        try {
            $list = Role::get();
            return response()->json(['list' => $list]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
