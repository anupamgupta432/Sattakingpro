<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ApiController;
use App\Http\Controllers\api\UserAuthController;
use App\Http\Controllers\api\HomeController;
use App\Http\Controllers\api\ChartController;
use App\Http\Controllers\api\MymatchController;
use App\Http\Controllers\api\BetController;
use App\Http\Controllers\api\ResultController;
use App\Http\Controllers\api\WinController;
use App\Http\Controllers\api\userController;
use App\Http\Controllers\api\HistoryController;
use App\Http\Controllers\api\NotifyController;
use App\Http\Controllers\api\LeaderController;
use App\Http\Controllers\api\GatewayController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/testgateway', [GatewayController::class, 'testGateway']);
Route::get('/testleader', [LeaderController::class, 'testLeader']);
Route::get('/testnotify', [NotifyController::class, 'testNotify']);
Route::get('/testhistory', [HistoryController::class, 'testHistory']);
Route::get('/testuser', [userController::class, 'testuser']);
Route::get('/testwin', [WinController::class, 'testwin']);
Route::get('/testresult', [ResultController::class, 'testresult']);
Route::get('/testbet', [BetController::class, 'testbet']);
Route::get("/test",[ApiController::class,"test"]);
Route::post('/login', [UserAuthController::class, 'login']);
Route::post('/register', [UserAuthController::class, 'register']);

Route::get('/company-profile', [HomeController::class, 'getCompanyProfile']);
Route::get('/live-games', [HomeController::class, 'getliveGames']);

Route::post('/charts-by-date', [ChartController::class, 'getChartsByDate']);

Route::post('/my-matches', [MymatchController::class, 'getMyMatchess']);
Route::get('/live-matches', [MymatchController::class, 'getLiveMatches']);

Route::post('/placebet', [BetController::class, 'placeBetJodiCross']);
Route::post('/placebet-haruf', [BetController::class, 'placeBetHaruf']);

Route::post('/live-result', [ResultController::class, 'liveResult']);
Route::get('/live-resultonly', [ResultController::class, 'fetchChartResultsOnly']);
Route::get('/store-live-results', [ResultController::class, 'storeliveResultsToDB']);
Route::post('/update-admin-result', [ResultController::class, 'updateAdminResult']);

Route::get('/declare-Winners', [WinController::class, 'declareWinners']);

Route::post('/get-profile', [userController::class, 'getuserProfile']);
Route::post('/edit-profile', [userController::class, 'edituserProfile']);

Route::post('/history-list', [HistoryController::class, 'betHistory']);
Route::post('/transaction-history-list', [HistoryController::class, 'trancationHistory']);
Route::post('/credit-debit-history', [HistoryController::class, 'getUserTransactions']);
Route::post('/wallet-balance', [HistoryController::class, 'walletBalance']);

Route::post('/user-notification', [NotifyController::class, 'getUserNotifications']);

Route::post('/leader-board', [LeaderController::class, 'getLeaderboard']);//leader history

Route::post('/recharge-request', [GatewayController::class, 'rechargeRequest']);
Route::post('/debit-request', [GatewayController::class, 'debitRequest']);//amount withdra
Route::post('/update-trancation', [GatewayController::class, 'updateTransactionStatus']);
