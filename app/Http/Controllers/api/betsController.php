<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;


class betsController extends Controller
{
       public function placeBetHaruf(Request $request){
        $request->validate([
            'user_id' => 'required|integer',
            'game_cities_id' => 'required|integer',
            'games_id' => 'required|integer',
            'bet_list' => 'required|array',
            'total_amount' => 'required|numeric',
        ]);

        $user_id = $request->user_id;
        $total_amount = $request->total_amount;

        // Step 1: Check offer limit
        $offer = DB::table('offers')->where('offer_name', 'betamount')->first();

        if ($offer && $total_amount > $offer->offer_amount) {
            return response()->json([
                'status' => false,
                'message' => 'Please bet under offers limit.',
            ], 200);
        }

        // Step 2: Check user wallet
        $wallet = DB::table('wallets')->where('user_id', $user_id)->first();

        if (!$wallet || $wallet->balance < $total_amount) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient balance, please recharge.',
            ], 200);
         }
         
           // ✅ Step 2.5: Check if game city is active and within time window
            // $currentTime = Carbon::now()->format('H:i:s');
            $currentTime = Carbon::now('Asia/Kolkata')->format('H:i:s');

            $gameCity = DB::table('game_cities')
            ->where('id', $request->game_cities_id)
            ->where('status', 'active')
            ->whereTime('start_time', '<=', $currentTime)
            ->whereTime('end_time', '>=', $currentTime)
            ->first();

        if (!$gameCity) {
             return response()->json([
                'status' => false,
                'message' => 'Betting is closed for this games',
                ], 200);
            }


 
        // Step 3: Insert bet
        DB::table('mymatch')->insert([
            'user_id' => $user_id,
            'game_cities_id' => $request->game_cities_id,
            'games_id' => $request->games_id,
            'bet_list' => json_encode($request->bet_list),
            'total_amount' => $total_amount,
            'reward' => 0,
            'winning_no' => null,
            'winning_status' => 'waiting for result',
            'running_status' => 'running',
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Step 4: Deduct balance from wallet
        DB::table('wallets')
            ->where('user_id', $user_id)
            ->update(['balance' => $wallet->balance - $total_amount]);

        return response()->json([
            'status' => true,
            'message' => 'Bet placed successfully.',
        ]);
    }

        function tess(){
            
            return "ok";
        }
}
