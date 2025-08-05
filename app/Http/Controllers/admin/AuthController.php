<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use DB;

class AuthController extends Controller
{
    
    function loginform(){
        
        return view("login");
    }

    public function login(Request $request)
    {
        $admin = DB::table('admin')
            ->where('email', $request->email)
            ->where('password', $request->password) // Note: plain text password (not secure)
            ->first();

        if ($admin) {
            Session::put('admin_id', $admin->id);
            return redirect('/admin/dashboard');
        } else {
            return redirect()->back()->with('error', 'Invalid email or password');
        }
    }
}
