<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\api\UserAuthController;
use App\Http\Middleware\AdminAuth;
use App\Http\Controllers\admin\AuthController;





Route::get("loginform",[AuthController::class,"loginform"]);
Route::post("login",[AuthController::class,"login"])->name("login");
// Login page
Route::get('/login', function () {
    return view('login');
})->name('login');//->middleware('guestadmin'); // Optional guest middleware (see step 4)

// Login submission
Route::post('/signin', [UserAuthController::class, 'signin'])->name('signin');

// Logout
Route::get('/logout', [UserAuthController::class, 'logout'])->name('logout');

// Protected index page (dashboard)
Route::middleware(['AdminAuth'])->group(function () {
    Route::get('/', function () {
        $userCount = DB::table('users')->count();
        $activeCount = DB::table('users')->where('status', 'active')->count();

        return view('index', [
            'userCount' => $userCount,
            'activeCount' => $activeCount
        ]);
    });

    Route::get('/welcome', function () {
        return view('welcome');
    })->name('admin.dashboard');

    Route::get('/logout', [UserAuthController::class, 'logout'])->name('logout');
});

// Welcome page (optional - remove if not needed)
Route::get('/admin', function () {
    return view('index');
});




