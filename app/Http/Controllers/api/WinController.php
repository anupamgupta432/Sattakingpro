<?php

namespace App\Http\Controllers\api;

use voku\helper\HtmlDomParser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
class WinController extends Controller
{
   function testwin() {
        $win = "win result api is ok";
        return $win;
    }
    
  public function declareWinners1(Request $request){
    $request->validate([
        'game_cities_id' => 'required|integer',
        'games_id' => 'required|integer',
    ]);

    $gameCitiesId = $request->game_cities_id;
    $gamesId = $request->games_id;

    $liveResult = DB::table('live_results')
        ->where('game_cites_id', $gameCitiesId)
        ->whereDate('date', Carbon::today())
        ->first();

    if (!$liveResult) {
        return response()->json([
            'success' => false,
            'message' => 'No result found for this game city today.'
        ], 200);
    }

    $result = trim($liveResult->admin_result ?: $liveResult->today_result);

    if ($result === '' || $result === 'XX' || $result === null) {
        return response()->json([
            'success' => false,
            'message' => 'Result not yet announced.'
        ], 200);
    }

    $offer = DB::table('offers')->where('offer_name', 'wincondition')->first();
    $winMultiplier = $offer ? floatval($offer->offer_amount) : 1.0;

    $bets = DB::table('mymatch')
        ->where('game_cities_id', $gameCitiesId)
        ->where('games_id', $gamesId)
        ->whereDate('created_at', Carbon::today())
        ->where('running_status', 'running')
        ->where('status', 'active')
        ->get();

    foreach ($bets as $bet) {
        $matched = false;
        $reward = 0;

        $betList = json_decode($bet->bet_list, true);

        foreach ($betList as $item) {
            if ($item['number'] == $result) {
                $matched = true;
                $reward = floatval($item['amount']) * $winMultiplier;
                break;
            }
        }

        DB::table('mymatch')->where('id', $bet->id)->update([
            'winning_no' => $matched ? $result : null,
            'reward' => $reward,
            'winning_status' => $matched ? 'reward given' : 'no reward',
            'running_status' => $matched ? 'complete' : 'off',
            'updated_at' => now(),
        ]);

        if ($matched) {
            // Update wallet balance
            DB::table('wallets')
                ->where('user_id', $bet->user_id)
                ->increment('balance', $reward);

            // Get game city and title info
            $gameCityData = DB::table('game_cities')->where('id', $gameCitiesId)->first();
            $gameCityTitle = $gameCityData ? $gameCityData->title : 'Unknown City';
            $gameType = $gamesId == 1 ? 'JODI GAME' : ($gamesId == 2 ? 'CROSS GAME' : 'UNKNOWN GAME');
            $description = $gameCityTitle . ' FOR ' . $gameType;

            // Insert into bet history
            DB::table('bet_history')->insert([
                'user_id' => $bet->user_id,
                'title' => 'Winning',
                'description' => $description,
                'type' => 0,
                'amount' => $reward,
                'time' => now()->format('H:i:s'),
                'date' => now()->format('Y-m-d'),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Winners calculated, wallets updated, and history stored.'
    ], 200);
}//add winning amount in wallet + history create bet_history table

  public function declareWinners2(Request $request){
    $request->validate([
        'game_cities_id' => 'required|integer',
        'games_id' => 'required|integer',
    ]);

    $gameCitiesId = $request->game_cities_id;
    $gamesId = $request->games_id;
    $today = Carbon::now('Asia/Kolkata')->format('Y-m-d');

    $liveResult = DB::table('live_results')
        ->where('game_cites_id', $gameCitiesId)
        ->whereDate('date', Carbon::now('Asia/Kolkata'))
        ->first();

    if (!$liveResult) {
        return response()->json([
            'success' => false,
            'message' => 'No result found for this game city today.',
            'date'=> Carbon::now('Asia/Kolkata')
        ], 200);
    }

    $result = trim($liveResult->admin_result ?: $liveResult->today_result);

    // if ($result === '' || $result === 'XX' || $result === null) {
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Result not yet announced.'
    //     ], 200);
    // }
    
    if ($result === '' || $result === 'XX' || $result === null) {
    $allBets = DB::table('mymatch')
        ->where('game_cities_id', $gameCitiesId)
        // ->where('games_id', $gamesId)
        ->whereDate('created_at', $today)
        ->where('running_status', 'running')
        ->where('status', 'active')
        ->get();

    $numberTotals = [];

    // Initialize all numbers 00 to 99 with 0
    for ($i = 0; $i <= 99; $i++) {
        $formatted = str_pad($i, 2, '0', STR_PAD_LEFT);
        $numberTotals[$formatted] = 0;
    }

    foreach ($allBets as $bet) {
        $betList = json_decode($bet->bet_list, true);
        foreach ($betList as $item) {
            $num = $item['number'];
            $amt = floatval($item['amount']);
            if (isset($numberTotals[$num])) {
                $numberTotals[$num] += $amt;
            }
        }
    }

    // Sort by value to get least bet amount
    asort($numberTotals);
    $minAmount = reset($numberTotals);
    $possibleWinners = array_keys($numberTotals, $minAmount);

    // Choose one (first) number as the winning number
    $generatedResult = $possibleWinners[0];

    // Store in live_results
    DB::table('live_results')
        ->where('id', $liveResult->id)
        ->update([
            'admin_result' => $generatedResult,
            'updated_at' => now()
        ]);

       $result = $generatedResult;
    }


    $offer = DB::table('offers')->where('offer_name', 'wincondition')->first();
    $winMultiplier = $offer ? floatval($offer->offer_amount) : 1.0;

    $bets = DB::table('mymatch')
        ->where('game_cities_id', $gameCitiesId)
        ->where('games_id', $gamesId)
        ->whereDate('created_at', $today)
        ->where('running_status', 'running')
        ->where('status', 'active')
        ->get();

    foreach ($bets as $bet) {
        $matched = false;
        $reward = 0;

        $betList = json_decode($bet->bet_list, true);

        foreach ($betList as $item) {
            if ($item['number'] == $result) {
                $matched = true;
                $reward = floatval($item['amount']) * $winMultiplier;
                break;
            }
        }

        DB::table('mymatch')->where('id', $bet->id)->update([
            'winning_no' => $matched ? $result : null,
            'reward' => $reward,
            'winning_status' => $matched ? 'reward given' : 'no reward',
            'running_status' => $matched ? 'complete' : 'off',
            // 'created_at' => $existing ? $existing->created_at : Carbon::now('Asia/Kolkata'),
            'updated_at' => Carbon::now('Asia/Kolkata')

        ]);

        if ($matched) {
            // Update wallet balance
            DB::table('wallets')
                ->where('user_id', $bet->user_id)
                ->increment('balance', $reward);

            // Get game city and title info
            $gameCityData = DB::table('game_cities')->where('id', $gameCitiesId)->first();
            $gameCityTitle = $gameCityData ? $gameCityData->title : 'Unknown City';
            $gameType = $gamesId == 1 ? 'JODI GAME' : ($gamesId == 2 ? 'CROSS GAME' : 'UNKNOWN GAME');
            $description = $gameCityTitle . ' FOR ' . $gameType;

            // Insert into bet history
            DB::table('bet_history')->insert([
                'user_id' => $bet->user_id,
                'title' => 'Winning',
                'description' => $description,
                'type' => 0,
                'amount' => $reward,
                'time' => now()->format('H:i:s'),
                'date' => now()->format('Y-m-d'),
                'status' => 'active',
                'created_at' => Carbon::now('Asia/Kolkata'),
                'updated_at' => Carbon::now('Asia/Kolkata')

            ]);
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Winners calculated, wallets updated, and history stored.',
        'data' => $result,
        'date'=>Carbon::now('Asia/Kolkata'),
        'Carbondate'=>$today
        
        
    ], 200);
}//jodi cross

//   public function declareWinners(Request $request){
//     $request->validate([
//         'game_cities_id' => 'required|integer',
//         'games_id' => 'required|integer',
//     ]);

//     $gameCitiesId = $request->game_cities_id;
//     $gamesId = $request->games_id;
//     $today = Carbon::now('Asia/Kolkata')->format('Y-m-d');

//     $liveResult = DB::table('live_results')
//         ->where('game_cites_id', $gameCitiesId)
//         ->whereDate('date', Carbon::now('Asia/Kolkata'))
//         ->first();

//     if (!$liveResult) {
//         return response()->json([
//             'success' => false,
//             'message' => 'No result found for this game city today.',
//             'date'=> Carbon::now('Asia/Kolkata')
//         ], 200);
//     }

//     $result = trim($liveResult->admin_result ?: $liveResult->today_result);
    
//   if ($result === '' || $result === 'XX' || $result === null) {
//     $allBets = DB::table('mymatch')
//         ->where('game_cities_id', $gameCitiesId)
//         ->whereDate('created_at', $today)
//         ->whereIn('games_id', [1, 2, 3, 4])
//         ->where('running_status', 'running')
//         ->where('status', 'active')
//         ->get();

//     $numberTotals = [];

//     // Initialize all numbers 00 to 99 with 0
//     for ($i = 0; $i <= 99; $i++) {
//         $formatted = str_pad($i, 2, '0', STR_PAD_LEFT);
//         $numberTotals[$formatted] = 0;
//     }

//     foreach ($allBets as $bet) {
//         $gameId = $bet->games_id;
//         $betList = json_decode($bet->bet_list, true);

//         foreach ($betList as $item) {
//             $number = str_pad($item['number'], 2, '0', STR_PAD_LEFT);
//             $amount = floatval($item['amount']);

//             foreach ($numberTotals as $key => $total) {
//                 if (in_array($gameId, [1, 2]) && $number === $key) {
//                     $numberTotals[$key] += $amount;
//                 } elseif ($gameId == 3 && substr($key, 0, 1) === $number) {
//                     $numberTotals[$key] += $amount;
//                 } elseif ($gameId == 4 && substr($key, 1, 1) === $number) {
//                     $numberTotals[$key] += $amount;
//                 }
//             }
//         }
//     }

//     // Sort to get least bet amount
//     asort($numberTotals);
//     $minAmount = reset($numberTotals);
//     $possibleWinners = array_keys($numberTotals, $minAmount);

//     // Final result
//     $generatedResult = $possibleWinners[0];

//     // Store result in live_results
//     DB::table('live_results')
//         ->where('id', $liveResult->id)
//         ->update([
//             'admin_result' => $generatedResult,
//             'updated_at' => now()
//         ]);

//     $result = $generatedResult;
// }



//     $offer = DB::table('offers')->where('offer_name', 'wincondition')->first();
//     $winMultiplier = $offer ? floatval($offer->offer_amount) : 1.0;

//     $bets = DB::table('mymatch')
//         ->where('game_cities_id', $gameCitiesId)
//         ->where('games_id', $gamesId)
//         ->whereDate('created_at', $today)
//         ->where('running_status', 'running')
//         ->where('status', 'active')
//         ->get();

//     foreach ($bets as $bet) {
//         $matched = false;
//         $reward = 0;

//         $betList = json_decode($bet->bet_list, true);

//         // foreach ($betList as $item) {
//         //     if ($item['number'] == $result) {
//         //         $matched = true;
//         //         $reward = floatval($item['amount']) * $winMultiplier;
//         //         break;
//         //     }
//         // }//jodi cross for true condition
//         foreach ($betList as $item) {
//     $number = str_pad($item['number'], 2, '0', STR_PAD_LEFT);
//     $amount = floatval($item['amount']);

//     if (
//         ($gamesId == 1 || $gamesId == 2) && $number == $result
//     ) {
//         $matched = true;
//         $reward = $amount * $winMultiplier;
//         break;
//     } elseif (
//         $gamesId == 3 && substr($result, 0, 1) == $number
//     ) {
//         $matched = true;
//         $reward = $amount * $winMultiplier;
//         break;
//     } elseif (
//         $gamesId == 4 && substr($result, 1, 1) == $number
//     ) {
//         $matched = true;
//         $reward = $amount * $winMultiplier;
//         break;
//     }
// }


//         DB::table('mymatch')->where('id', $bet->id)->update([
//             'winning_no' => $matched ? $result : null,
//             'reward' => $reward,
//             'winning_status' => $matched ? 'reward given' : 'no reward',
//             'running_status' => $matched ? 'complete' : 'off',
//             // 'created_at' => $existing ? $existing->created_at : Carbon::now('Asia/Kolkata'),
//             'updated_at' => Carbon::now('Asia/Kolkata')

//         ]);

//         if ($matched) {
//             // Update wallet balance
//             DB::table('wallets')
//                 ->where('user_id', $bet->user_id)
//                 ->increment('balance', $reward);

//             // Get game city and title info
//             $gameCityData = DB::table('game_cities')->where('id', $gameCitiesId)->first();
//             $gameCityTitle = $gameCityData ? $gameCityData->title : 'Unknown City';
//             $gameType = [1 => 'JODI', 2 => 'CROSS', 3 => 'ANDAR', 4 => 'BAHAR'][$gamesId] ?? 'UNKNOWN GAME';
//             $description = $gameCityTitle . ' FOR ' . $gameType;
//             $now = \Carbon\Carbon::now('Asia/Kolkata');
//             // Insert into bet history
//             DB::table('bet_history')->insert([
//                 'user_id' => $bet->user_id,
//                 'title' => 'Winning',
//                 'description' => $description,
//                 'type' => 0,
//                 'amount' => $reward,
//                 'time' => $now->format('H:i:s'),
//                 'date' => $now->format('Y-m-d'),
//                 'status' => 'active',
//                 'created_at' => Carbon::now('Asia/Kolkata'),
//                 'updated_at' => Carbon::now('Asia/Kolkata')

//             ]);
//         }
//     }

//     return response()->json([
//         'success' => true,
//         'message' => 'Winners calculated, wallets updated, and history stored.',
//         'data' => $result,
//         'date'=>Carbon::now('Asia/Kolkata'),
//         'Carbondate'=>$today
        
        
//     ], 200);
// }//Horuf with both id request condition ye condition bilkul sahi hai ek ek citis ke liye



//     public function declareWinners(Request $request){
//     $today = Carbon::now('Asia/Kolkata')->format('Y-m-d');
//     $now = Carbon::now('Asia/Kolkata');

//     $gameCityIds = DB::table('game_cities')
//     ->where('status', 'active')
//     ->whereRaw("ABS(TIMESTAMPDIFF(SECOND, result_time, TIME(?))) <= 1200", [$now->format('H:i:s')])
//     ->pluck('id')
//     ->toArray();

//     $gameIds = DB::table('games')
//         ->where('status', 'active')
//         ->pluck('id')
//         ->toArray();

//     foreach ($gameCityIds as $gameCitiesId) {
//         foreach ($gameIds as $gamesId) {
//             $liveResult = DB::table('live_results')
//                 ->where('game_cites_id', $gameCitiesId)
//                 ->whereDate('date', $today)
//                 ->first();

//             if (!$liveResult) continue;

//             $result = trim($liveResult->admin_result ?: $liveResult->today_result);

//             if ($result === '--' || $result === 'XX' || $result === null||$result === '') {
//                 $allBets = DB::table('mymatch')
//                     ->where('game_cities_id', $gameCitiesId)
//                     ->whereDate('created_at', $today)
//                     ->where('running_status', 'running')
//                     ->where('status', 'active')
//                     ->whereIn('games_id', [1, 2, 3, 4])
//                     ->get();

//                 $numberTotals = [];
//                 for ($i = 0; $i <= 99; $i++) {
//                     $formatted = str_pad($i, 2, '0', STR_PAD_LEFT);
//                     $numberTotals[$formatted] = 0;
//                 }

//                 foreach ($allBets as $bet) {
//                     $gameId = $bet->games_id;
//                     $betList = json_decode($bet->bet_list, true);

//                     foreach ($betList as $item) {
//                         $number = str_pad($item['number'], 2, '0', STR_PAD_LEFT);
//                         $amount = floatval($item['amount']);

//                         foreach ($numberTotals as $key => $total) {
//                             if (in_array($gameId, [1, 2]) && $number === $key) {
//                                 $numberTotals[$key] += $amount;
//                             } elseif ($gameId == 3 && substr($key, 0, 1) === $number) {
//                                 $numberTotals[$key] += $amount;
//                             } elseif ($gameId == 4 && substr($key, 1, 1) === $number) {
//                                 $numberTotals[$key] += $amount;
//                             }
//                         }
//                     }
//                 }

//                 asort($numberTotals);
//                 $minAmount = reset($numberTotals);
//                 $possibleWinners = array_keys($numberTotals, $minAmount);

//                 $generatedResult = $possibleWinners[0];

//                 DB::table('live_results')
//                     ->where('id', $liveResult->id)
//                     ->update([
//                         'admin_result' => $generatedResult,
//                         'updated_at' => $now
//                     ]);

//                 $result = $generatedResult;
//             }

//             $offer = DB::table('offers')->where('offer_name', 'wincondition')->first();
//             $winMultiplier = $offer ? floatval($offer->offer_amount) : 1.0;

//             $bets = DB::table('mymatch')
//                 ->where('game_cities_id', $gameCitiesId)
//                 ->where('games_id', $gamesId)
//                 ->whereDate('created_at', $today)
//                 ->where('running_status', 'running')
//                 ->where('status', 'active')
//                 ->get();

//             foreach ($bets as $bet) {
//                 $matched = false;
//                 $reward = 0;
//                 $betList = json_decode($bet->bet_list, true);

//                 foreach ($betList as $item) {
//                     $number = str_pad($item['number'], 2, '0', STR_PAD_LEFT);
//                     $amount = floatval($item['amount']);

//                     if (($gamesId == 1 || $gamesId == 2) && $number == $result) {
//                         $matched = true;
//                         $reward = $amount * $winMultiplier;
//                         break;
//                     } elseif ($gamesId == 3 && substr($result, 0, 1) == $number) {
//                         $matched = true;
//                         $reward = $amount * $winMultiplier;
//                         break;
//                     } elseif ($gamesId == 4 && substr($result, 1, 1) == $number) {
//                         $matched = true;
//                         $reward = $amount * $winMultiplier;
//                         break;
//                     }
//                 }

//                 DB::table('mymatch')->where('id', $bet->id)->update([
//                     'winning_no' => $matched ? $result : null,
//                     'reward' => $reward,
//                     'winning_status' => $matched ? 'reward given' : 'no reward',
//                     'running_status' => $matched ? 'complete' : 'off',
//                     'updated_at' => Carbon::now('Asia/Kolkata')
//                 ]);

//                 if ($matched) {
//                     DB::table('wallets')
//                         ->where('user_id', $bet->user_id)
//                         ->increment('balance', $reward);

//                     $gameCityData = DB::table('game_cities')->where('id', $gameCitiesId)->first();
//                     $gameCityTitle = $gameCityData ? $gameCityData->title : 'Unknown City';
//                     $gameType = [1 => 'JODI', 2 => 'CROSS', 3 => 'ANDAR', 4 => 'BAHAR'][$gamesId] ?? 'UNKNOWN GAME';
//                     $description = $gameCityTitle . ' FOR ' . $gameType;

//                     DB::table('bet_history')->insert([
//                         'user_id' => $bet->user_id,
//                         'title' => 'Winning',
//                         'description' => $description,
//                         'type' => 0,
//                         'amount' => $reward,
//                         'time' => $now->format('H:i:s'),
//                         'date' => $now->format('Y-m-d'),
//                         'status' => 'active',
//                         'created_at' => $now,
//                         'updated_at' => $now
//                     ]);
//                 }
//             }
//         }
//     }

//     return response()->json([
//         'success' => true,
//         'message' => 'Winners calculated, wallets updated, and history stored.',
//         'date' => $today,
//         'game_cites_id'=>$gameCityIds,
//         'games_id'=>$gameIds
//     ], 200);
// }//auto ganrate result  for all games, cities  bilkul sahi hai

public function declareWinners(Request $request){
    $today = Carbon::now('Asia/Kolkata')->format('Y-m-d');
    $now = Carbon::now('Asia/Kolkata');

    $gameCityIds = DB::table('game_cities')
        ->where('status', 'active')
        ->whereRaw("ABS(TIMESTAMPDIFF(SECOND, result_time, TIME(?))) <= 240", [$now->format('H:i:s')])
        ->pluck('id')
        ->toArray();

    $gameIds = DB::table('games')
        ->where('status', 'active')
        ->pluck('id')
        ->toArray();

    foreach ($gameCityIds as $gameCitiesId) {
    $liveResult = DB::table('live_results')
        ->where('game_cites_id', $gameCitiesId)
        ->whereDate('date', $today)
        ->first();

    if (!$liveResult) continue;

    $result = trim($liveResult->admin_result ?: $liveResult->today_result);

    if ($result === '--' || $result === 'XX' || $result === null || $result === '') {
        // Step 1: Get all active bets across all games
        $allBets = DB::table('mymatch')
            ->where('game_cities_id', $gameCitiesId)
            ->whereDate('created_at', $today)
            ->where('running_status', 'running')
            ->where('status', 'active')
            ->whereIn('games_id', [1, 2, 3, 4])
            ->get();

        // Step 2: Prepare possible numbers 00–99
        $numberPayouts = [];
        for ($i = 0; $i <= 99; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            $numberPayouts[$num] = 0;
        }

      // Step 2: Prepare possible numbers 00–99
$numberPayouts = [];
for ($i = 0; $i <= 99; $i++) {
    $num = str_pad($i, 2, '0', STR_PAD_LEFT);
    $numberPayouts[$num] = 0;
}

// Step 3: Get win multiplier
$offer = DB::table('offers')->where('offer_name', 'wincondition')->first();
$winMultiplier = $offer ? floatval($offer->offer_amount) : 1.0;

// Step 4: Calculate total payout for each number (across all games)
foreach ($allBets as $bet) {
    $betList = json_decode($bet->bet_list, true);
    $gameId = $bet->games_id;

    foreach ($betList as $item) {
        $betNumber = str_pad($item['number'], 2, '0', STR_PAD_LEFT);
        $amount = floatval($item['amount']);

        foreach ($numberPayouts as $possibleNumber => $currentPayout) {
            if (($gameId == 1 || $gameId == 2) && $possibleNumber == $betNumber) {
                // JODI and CROSS
                $numberPayouts[$possibleNumber] += $amount * $winMultiplier;
            } elseif ($gameId == 3 && substr($possibleNumber, 0, 1) == $betNumber) {
                // ANDAR
                $numberPayouts[$possibleNumber] += $amount * $winMultiplier;
            } elseif ($gameId == 4 && substr($possibleNumber, 1, 1) == $betNumber) {
                // BAHAR
                $numberPayouts[$possibleNumber] += $amount * $winMultiplier;
            }
        }
    }
}

// Step 5: Find number with minimum total payout
asort($numberPayouts);
$generatedResult = array_key_first($numberPayouts);
        // Step 6: Save result
        DB::table('live_results')
            ->where('id', $liveResult->id)
            ->update([
                'admin_result' => $generatedResult,
                'updated_at' => $now
            ]);

        $result = $generatedResult;
    }

    // Step 7: Now use $result to update all matching bets across all game IDs
    foreach ([1, 2, 3, 4] as $gamesId) {
        $bets = DB::table('mymatch')
            ->where('game_cities_id', $gameCitiesId)
            ->where('games_id', $gamesId)
            ->whereDate('created_at', $today)
            ->where('running_status', 'running')
            ->where('status', 'active')
            ->get();

        foreach ($bets as $bet) {
            $matched = false;
            $reward = 0;
            $betList = json_decode($bet->bet_list, true);

            foreach ($betList as $item) {
                $number = str_pad($item['number'], 2, '0', STR_PAD_LEFT);
                $amount = floatval($item['amount']);

                if (($gamesId == 1 || $gamesId == 2) && $number == $result) {
                    $matched = true;
                    $reward = $amount * $winMultiplier;
                    break;
                } elseif ($gamesId == 3 && substr($result, 0, 1) == $number) {
                    $matched = true;
                    $reward = $amount * $winMultiplier;
                    break;
                } elseif ($gamesId == 4 && substr($result, 1, 1) == $number) {
                    $matched = true;
                    $reward = $amount * $winMultiplier;
                    break;
                }
            }

            DB::table('mymatch')->where('id', $bet->id)->update([
                'winning_no' => $matched ? $result : null,
                'reward' => $reward,
                'winning_status' => $matched ? 'reward given' : 'no reward',
                'running_status' => $matched ? 'complete' : 'off',
                'updated_at' => $now
            ]);

            if ($matched) {
                DB::table('wallets')->where('user_id', $bet->user_id)->increment('balance', $reward);

                $gameCityData = DB::table('game_cities')->where('id', $gameCitiesId)->first();
                $gameCityTitle = $gameCityData ? $gameCityData->title : 'Unknown City';
                $gameType = [1 => 'JODI', 2 => 'CROSS', 3 => 'ANDAR', 4 => 'BAHAR'][$gamesId] ?? 'UNKNOWN GAME';

                DB::table('bet_history')->insert([
                    'user_id' => $bet->user_id,
                    'title' => 'Winning',
                    'description' => $gameCityTitle . ' FOR ' . $gameType,
                    'type' => 0,
                    'amount' => $reward,
                    'time' => $now->format('H:i:s'),
                    'date' => $now->format('Y-m-d'),
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }
    }
}


    return response()->json([
        'success' => true,
        'message' => 'Winners calculated, wallets updated, and history stored.',
        'date' => $today,
        'game_cites_id' => $gameCityIds,
        'games_id' => $gameIds,
        'ganrate_result'=>$result
    ], 200);
}


}   