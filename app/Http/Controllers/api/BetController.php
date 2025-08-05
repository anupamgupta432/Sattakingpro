<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BetController extends Controller
{
    public function testbet(){
        $bet = "bet controller is ok";
    return $bet;
   }
    

    public function placeBetJodiCross(Request $request){
    $request->validate([
        'user_id'        => 'required|integer',
        'game_cities_id' => 'required|integer',
        'games_id'       => 'required|integer',
        'bet_list'       => 'required|array|min:1',
        'total_amount'   => 'required|numeric|min:1',
    ]);

    $user_id        = $request->user_id;
    $games_id       = $request->games_id;
    $game_cities_id = $request->game_cities_id;
    $total_amount   = $request->total_amount;
    $bet_list       = $request->bet_list;

    // Step 1: Offer limit check
    $offer = DB::table('offers')->where('offer_name', 'betamount')->first();
    if ($offer && $total_amount > $offer->offer_amount) {
        return response()->json([
            'status' => false,
            'message' => 'Please bet under offers limit.',
        ], 200);
    }

    // Step 2: Wallet check
    $wallet = DB::table('wallets')->where('user_id', $user_id)->first();
    if (!$wallet || $wallet->balance < $total_amount) {
        return response()->json([
            'status' => false,
            'message' => 'Insufficient balance, please recharge.',
        ], 200);
    }

    // Step 3: Game City Time Check
    $currentTime = Carbon::now('Asia/Kolkata')->format('H:i:s');
    $gameCity = DB::table('game_cities')
        ->where('id', $game_cities_id)
        ->where('status', 'active')
        ->whereTime('start_time', '<=', $currentTime)
        ->whereTime('end_time', '>=', $currentTime)
        ->first();

$currentTime = Carbon::now('Asia/Kolkata')->format('H:i:s');

$gameCity = DB::table('game_cities')
    ->where('status', 'active')
    ->where('id', $game_cities_id)
    ->get()
    ->filter(function ($game) use ($currentTime) {
        return (
            ($game->start_time <= $game->end_time && $currentTime >= $game->start_time && $currentTime <= $game->end_time) ||
            ($game->start_time > $game->end_time && ($currentTime >= $game->start_time || $currentTime <= $game->end_time))
        );
    })
    ->first();

if (!$gameCity) {
    return response()->json([
        'status' => false,
        'message' => 'Betting is closed for this game.',
    ], 200);
}


    // Step 4: Place bet + history + update wallet
    
 $now = \Carbon\Carbon::now('Asia/Kolkata');
    DB::beginTransaction();
    try {
        // Insert into mymatch
        DB::table('mymatch')->insert([
            'user_id'        => $user_id,
            'game_cities_id' => $game_cities_id,
            'games_id'       => $games_id,
            'bet_list'       => json_encode($bet_list),
            'total_amount'   => $total_amount,
            'reward'         => 0,
            'winning_no'     => null,
            'winning_status' => 'waiting for result',
            'running_status' => 'running',
            'status'         => 'active',
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);

        // Bet history (single entry)
        $gameType = '';
        if ($games_id == 1) {
            $gameType = 'JODI';
        } elseif ($games_id == 2) {
            $gameType = 'CROSS';
        }

        $description = $gameCity->title . ' FOR ' . $gameType;
       
        DB::table('bet_history')->insert([
            'user_id'     => $user_id,
            'title'       => 'Placed',
            'description' => $description,
            'type'        => 1,  // debit
            'amount'      => $total_amount,
            'time'        => $now->format('H:i:s'),
            'date'        => $now->format('Y-m-d'),
            'status'      => 'active',
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        // Update wallet balance
        DB::table('wallets')
            ->where('user_id', $user_id)
            ->update([
                'balance' => $wallet->balance - $total_amount
            ]);

        DB::commit();
        return response()->json([
            'status' => true,
            'message' => 'Bet placed successfully.',
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Failed to place bet.',
            'error' => $e->getMessage()
        ], 200);
    }
}//ye code sahi chal raha anupam sahi cross jodi

    public function placeBetHaruf(Request $request){
    $request->validate([
        'user_id'        => 'required|integer',
        'game_cities_id' => 'required|integer',
        'bet_list'       => 'required|array|min:1',
        'total_amount'   => 'required|numeric|min:1',
    ]);

    $user_id        = $request->user_id;
    $game_cities_id = $request->game_cities_id;
    $total_amount   = $request->total_amount;
    $bet_list       = $request->bet_list;

    // Step 1: Offer check
    $offer = DB::table('offers')->where('offer_name', 'betamount')->first();
    if ($offer && $total_amount > $offer->offer_amount) {
        return response()->json([
            'status' => false,
            'message' => 'Please bet under offers limit.',
        ], 200);
    }

    // Step 2: Wallet check
    $wallet = DB::table('wallets')->where('user_id', $user_id)->first();
    if (!$wallet || $wallet->balance < $total_amount) {
        return response()->json([
            'status' => false,
            'message' => 'Insufficient balance, please recharge.',
        ], 200);
    }

    // Step 3: Game City time check
$currentTime = Carbon::now('Asia/Kolkata')->format('H:i:s');

$gameCity = DB::table('game_cities')
    ->where('status', 'active')
    ->where('id', $game_cities_id)
    ->get()
    ->filter(function ($game) use ($currentTime) {
        return (
            ($game->start_time <= $game->end_time && $currentTime >= $game->start_time && $currentTime <= $game->end_time) ||
            ($game->start_time > $game->end_time && ($currentTime >= $game->start_time || $currentTime <= $game->end_time))
        );
    })
    ->first();

if (!$gameCity) {
    return response()->json([
        'status' => false,
        'message' => 'Betting is closed for this game.',
    ], 200);
}


    DB::beginTransaction();
    try {
        foreach ($bet_list as $games_id => $bets) {
            $games_id = (int) $games_id;
            $individualAmount = collect($bets)->sum('amount');
            $now = \Carbon\Carbon::now('Asia/Kolkata');
            // Insert mymatch
            DB::table('mymatch')->insert([
                'user_id'        => $user_id,
                'game_cities_id' => $game_cities_id,
                'games_id'       => $games_id,
                'bet_list'       => json_encode($bets),
                'total_amount'   => $individualAmount,
                'reward'         => 0,
                'winning_no'     => null,
                'winning_status' => 'waiting for result',
                'running_status' => 'running',
                'status'         => 'active',
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);

            // Description: ANDAR for 3, BAHAR for 4
            $side = $games_id == 3 ? 'ANDAR' : ($games_id == 4 ? 'BAHAR' : 'UNKNOWN');
            
            DB::table('bet_history')->insert([
                'user_id'     => $user_id,
                'title'       => 'Placed',
                'description' => $gameCity->title . ' FOR ' . $side,
                'type'        => 1,
                'amount'      => $individualAmount,
                'time'        => $now->format('H:i:s'),
                'date'        => $now->format('Y-m-d'),
                'status'      => 'active',
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        // Update wallet
        DB::table('wallets')
            ->where('user_id', $user_id)
            ->update([
                'balance' => $wallet->balance - $total_amount
            ]);

        DB::commit();
        return response()->json([
            'status' => true,
            'message' => 'Bet placed successfully.',
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Failed to place bet.',
            'error'   => $e->getMessage(),
        ], 200);
    }
}//ye code bh sahi hai haruf ke liye 
    
}
