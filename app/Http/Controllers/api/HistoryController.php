<?php

namespace App\Http\Controllers\api;

use voku\helper\HtmlDomParser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
class HistoryController extends Controller
{
   function testHistory() {
        $win = "History result api is ok";
        return $win;
    }
    
   public function betHistory(Request $request){
    $request->validate([
        'user_id' => 'required|integer|exists:users,id'
    ]);

    $user_id = $request->input('user_id');

    $bets = DB::table('bet_history')
        ->where('user_id', $user_id)
        ->orderBy('date', 'desc')
        ->orderBy('time', 'desc')
        ->get();

    if ($bets->isEmpty()) {
        return response()->json([
            'success' => true,
            'message' => 'No History found for this user.',
            'data' => []
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => ' History fetched successfully.',
        'data' => $bets
    ]);
}
   
//   public function trancationHistory(Request $request){
//     $request->validate([
//         'user_id' => 'required|integer|exists:users,id'
//     ]);

//     $user_id = $request->input('user_id');

//     $bets = DB::table('transactions')
//         ->where('user_id', $user_id)
//         ->orderBy('created_at', 'desc')
//         ->get();

//     if ($bets->isEmpty()) {
//         return response()->json([
//             'success' => true,
//             'message' => 'No History found for this user.',
//             'data' => []
//         ]);
//     }

//     return response()->json([
//         'success' => true,
//         'message' => ' History fetched successfully.',
//         'data' => $bets
//     ]);
// } //credit + debit
   
   public function getUserTransactions(Request $request){
    // Step 1: Manual validation with custom JSON response
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|integer|exists:users,id',
        'transaction_type' => 'required|in:credit,debit',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Please enter valid transaction type credit or debit',
            // 'errors' => $validator->errors()
        ], 200); // You can use 422 if you prefer: , 422
    }

    // Step 2: Fetch transactions
    $transactions = DB::table('transactions')
        ->where('user_id', $request->user_id)
        ->where('transaction_type', $request->transaction_type)
        ->orderBy('id', 'desc')
        ->get();

    if ($transactions->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No transactions found for this user and type.'
        ], 200);
    }

    return response()->json([
        'success' => true,
        'message' => 'Transactions retrieved successfully.',
        'data' => $transactions
    ], 200);
}//credit or debit

   public function walletBalance(Request $request) {
    $userId = $request->input('user_id');

    // Step 1: Validate input
    if (!$userId) {
        return response()->json([
            'success' => false,
            'message' => 'User ID is required.',
        ], 200);
    }

    // Step 2: Check if user exists
    $userExists = DB::table('users')->where('id', $userId)->exists();

    if (!$userExists) {
        return response()->json([
            'success' => false,
            'message' => 'No data found.',
        ], 200);
    }

    // Step 3: Fetch balances and rewards
    $walletBalance = DB::table('wallets')
        ->where('user_id', $userId)
        ->value('balance') ?? 0;

    $referralReward = DB::table('referrals')
        ->where(function ($query) use ($userId) {
            $query->where('referred_user_id', $userId)
                  ->orWhere('user_id', $userId);
        })
        ->sum('reward_amount');

    $gameReward = DB::table('transactions')
        ->where('user_id', $userId)
        ->sum('amount');

    $gameBonus = DB::table('transactions')
        ->where('user_id', $userId)
        ->sum('bonus');
        
    $gameQR = DB::table('admin')
    ->value('qr_code');    
    
    $gameUpi = DB::table('admin')
        ->value('upi_id');
    // Step 4: If everything is zero, treat as no data
    if (
        $walletBalance == 0 &&
        $referralReward == 0 &&
        $gameReward == 0 &&
        $gameBonus == 0 &&
        $gameQR == 0 &&
        $gameUpi == 0
    ) {
        return response()->json([
            'success' => false,
            'message' => 'No data found.',
        ], 200);
    }

    // Step 5: Return formatted data
    return response()->json([
        'success' => true,
        'message' => 'Data fetched successfully.',
        'data' => [
            'winning_balance' => (float) $walletBalance,
            'commission'      => (float) $referralReward,
            'deposit'         => (float) $gameReward,
            'bonus'           => (float) $gameBonus,
            'qr_code'         => (string) $gameQR,
            'upi_id'          => (string) $gameUpi,
        ]
    ], 200);
}


}    