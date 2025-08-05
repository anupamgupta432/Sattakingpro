<?php

namespace App\Http\Controllers\api;

use voku\helper\HtmlDomParser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ResultController extends Controller
{
    function testresult() {
        $result = "live result api is ok";
        return $result;
    }

    public function fetchChartResultsOnly() {
        $allowedGames = [
            "DELHI CITY", "DUBAI BAZAR", "GHAZIABAD",
             "DELHI BAZAR", "SHRI GANESH",
            "FARIDABAD", "GALI", "DESAWAR"
        ];

        $url = "https://satta-king-fast.com";
        $context = stream_context_create(["http" => ["timeout" => 10]]);
        $html = @file_get_contents($url, false, $context);

        if (!$html) {
            return response()->json(['status' => false, 'message' => 'Website not accessible']);
        }

        $dom = HtmlDomParser::str_get_html($html); // correct parser usage

        if (!$dom) {
            return response()->json(['status' => false, 'message' => 'Failed to parse HTML']);
        }

        $resultData = [];

        foreach ($dom->find(".game-result") as $gameResult) {
            $gameName = trim(optional($gameResult->find(".game-name", 0))->plaintext);
            $gameTime = trim(optional($gameResult->find(".game-time", 0))->plaintext);
            $todayNumber = trim(optional($gameResult->find(".today-number", 0))->plaintext);
            $yesterdayNumber = trim(optional($gameResult->find(".yesterday-number", 0))->plaintext);

            if (!$gameName || !in_array($gameName, $allowedGames)) {
                continue;
            }

            $resultData[] = [
                'game' => $gameName,
                'time' => $gameTime,
                'today_result' => $todayNumber,
                'yesterday_result' => $yesterdayNumber,
            ];
        }

        return response()->json([
            'status' => true,
            'data' => $resultData,
        ]);
    }
    
    public function fetchChartResults(){
    $allowedGames = [
        "MOHALI", "ROYAL CHALLENGE", "GHAZIABAD",
        "GURGAON", "DHAN KUBER", "DELHI BAZAR", "SHRI GANESH",
        "FARIDABAD", "GALI", "DESAWAR"
    ];

    $url = "https://satta-king-fast.com";
    $context = stream_context_create(["http" => ["timeout" => 10]]);
    $html = @file_get_contents($url, false, $context);

    if (!$html) {
        return response()->json(['status' => false, 'message' => 'Website not accessible']);
    }

    $dom = \Sunra\PhpSimple\HtmlDomParser::str_get_html($html);
    if (!$dom) {
        return response()->json(['status' => false, 'message' => 'Failed to parse HTML']);
    }

    $date = now()->setTimezone('Asia/Kolkata')->toDateString();
    $insertedData = [];

    foreach ($dom->find(".game-result") as $gameResult) {
        $gameName = trim(optional($gameResult->find(".game-name", 0))->plaintext);
        $gameTime = trim(optional($gameResult->find(".game-time", 0))->plaintext);
        $todayNumber = trim(optional($gameResult->find(".today-number", 0))->plaintext);
        $yesterdayNumber = trim(optional($gameResult->find(".yesterday-number", 0))->plaintext);

        if (!$gameName || !in_array($gameName, $allowedGames)) {
            continue;
        }

        $existingEntry = DB::table('chart_results')
            ->where('gamename', $gameName)
            ->whereDate('date', $date)
            ->first();

        if ($existingEntry) {
            if ($existingEntry->result === 'XX') {
                DB::table('chart_results')
                    ->where('id', $existingEntry->id)
                    ->update([
                        'result' => $todayNumber,
                        'updated_at' => now(),
                    ]);
            }
        } else {
            DB::table('chart_results')->insert([
                'gamename' => $gameName,
                'date' => $date,
                'result_time' => $gameTime,
                'yesterday_number' => $yesterdayNumber,
                'result' => $todayNumber,
                'rds' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $insertedData[] = ['game' => $gameName, 'result' => $todayNumber];
    }

    return response()->json([
        'status' => true,
        'message' => 'Chart results updated.',
        'data' => $insertedData
    ]);
}

//     public function storeliveResultsToDB(){
//     // $allowedGames = [
       
//     //     "DELHI BAZAR", 
//     //     "SHRI GANESH",
//     //      "DELHI DAY",
//     //      "DUBAI BAZAR",
//     //      "GHAZIABAD",
//     //     "FARIDABAD", 
//     //     "GALI", 
//     //     "DESAWAR"
//     // ];//static data
    
//     $allowedGames = DB::table('game_cities')
//     ->where('status', 'active')
//     ->pluck('title')
//     ->map(function ($title) {
//         return strtoupper($title);
//     })
//     ->toArray();

    
//     $url = "https://satta-king-fast.com";
//     $context = stream_context_create(["http" => ["timeout" => 10]]);
//     $html = @file_get_contents($url, false, $context);

//     if (!$html) {
//         return response()->json(['status' => false, 'message' => 'Website not accessible'], 200);
//     }

//     $dom = HtmlDomParser::str_get_html($html);
//     if (!$dom) {
//         return response()->json(['status' => false, 'message' => 'Failed to parse HTML'], 200);
//     }

//     $today = Carbon::now('Asia/Kolkata')->format('y-m-d');
//     $finalData = [];

//     foreach ($dom->find(".game-result") as $gameResult) {
//         $gameName = trim(optional($gameResult->find(".game-name", 0))->plaintext);
//         $gameTime = trim(optional($gameResult->find(".game-time", 0))->plaintext);
//         $todayResult = trim(optional($gameResult->find(".today-number", 0))->plaintext);

//         if (!$gameName || !in_array(strtoupper($gameName), $allowedGames)) {
//             continue;
//         }

//     $gameCity = DB::table('game_cities')
//     ->whereRaw('UPPER(title) = ?', [strtoupper($gameName)])
//     ->first();
//         if (!$gameCity) continue;

//      $existing = DB::table('live_results')
//     ->where('game', $gameName)
//     ->where('date', $today)
//     ->first();

//   DB::table('live_results')->updateOrInsert(
//     ['game' => $gameName, 'date' => $today],
//     [
//         'game_cites_id' => $gameCity->id,
//         'time' => $gameTime,
//         'today_result' => $todayResult,
//         'admin_result' => $existing->admin_result ?? null,
//         'created_at' => $existing ? $existing->created_at : Carbon::now('Asia/Kolkata'),
//         'updated_at' => Carbon::now('Asia/Kolkata')

//     ]
//     );

//         $finalData[] = [
//             'game' => $gameName,
//             'time' => $gameTime,
//             'today_result' => $todayResult,
//             'game_cites_id' => $gameCity->id
//         ];
//     }

//     return response()->json([
//         'success' => true,
//         'message' => 'Live results stored successfully.',
//         'data' => $finalData
//     ], 200);
// } // live result from website ke liye sahi hai

      public function storeliveResultsToDB(){
    // $allowedGames = [
       
    //     "DELHI BAZAR", 
    //     "SHRI GANESH",
    //      "DELHI DAY",
    //      "DUBAI BAZAR",
    //      "GHAZIABAD",
    //     "FARIDABAD", 
    //     "GALI", 
    //     "DESAWAR"
    // ];//static data
    
    $allowedGames = DB::table('game_cities')
    ->where('status', 'active')
    ->pluck('title')
    ->map(function ($title) {
        return strtoupper($title);
    })
    ->toArray();

    
    $url = "https://satta-king-fast.com";
    $context = stream_context_create(["http" => ["timeout" => 10]]);
    $html = @file_get_contents($url, false, $context);

    if (!$html) {
        return response()->json(['status' => false, 'message' => 'Website not accessible'], 200);
    }

    $dom = HtmlDomParser::str_get_html($html);
    if (!$dom) {
        return response()->json(['status' => false, 'message' => 'Failed to parse HTML'], 200);
    }

    $today = Carbon::now('Asia/Kolkata')->format('y-m-d');
    $finalData = [];

    // Step 1: Collect all scraped results in associative array
$scrapedResults = [];
foreach ($dom->find(".game-result") as $gameResult) {
    $gameName = trim(optional($gameResult->find(".game-name", 0))->plaintext);
    $gameTime = trim(optional($gameResult->find(".game-time", 0))->plaintext);
    $todayResult = trim(optional($gameResult->find(".today-number", 0))->plaintext);

    if (!$gameName) continue;

    $scrapedResults[strtoupper($gameName)] = [
        'time' => $gameTime,
        'today_result' => $todayResult
    ];
}

// Step 2: Get all active games from game_cities
$allActiveGames = DB::table('game_cities')
    ->where('status', 'active')
    ->get();

$finalData = [];

foreach ($allActiveGames as $game) {
    $gameTitleUpper = strtoupper($game->title);

    // Check if result available
    $scraped = $scrapedResults[$gameTitleUpper] ?? null;

    $existing = DB::table('live_results')
        ->where('game', $game->title)
        ->where('date', $today)
        ->first();

    // Insert or update result
    DB::table('live_results')->updateOrInsert(
        ['game' => $game->title, 'date' => $today],
        [
            'game_cites_id' => $game->id,
            'time' => $scraped['time'] ?? null,
            'today_result' => $scraped['today_result'] ?? null,
            'admin_result' => $existing->admin_result ?? null,
            'created_at' => $existing ? $existing->created_at : Carbon::now('Asia/Kolkata'),
            'updated_at' => Carbon::now('Asia/Kolkata')
        ]
    );

    $finalData[] = [
        'game' => $game->title,
        'time' => $scraped['time'] ?? null,
        'today_result' => $scraped['today_result'] ?? null,
        'game_cites_id' => $game->id
    ];
}


    return response()->json([
        'success' => true,
        'message' => 'Live results stored successfully.',
        'data' => $finalData
    ], 200);
}
       
    public function updateAdminResult(Request $request){
    $request->validate([
        'game_cites_id' => 'required|integer',
        'admin_result' => 'required|string',
    ]);

    $gameId = $request->game_cites_id;
    $adminResult = $request->admin_result;

    $record = DB::table('live_results')
        ->where('game_cites_id', $gameId)
        ->whereDate('date', Carbon::today())
        ->first();

    if (!$record) {
        return response()->json([
            'success' => false,
            'message' => 'Game record not found for today'
        ], 200);
    }

    // Fetch game timings
    $gameCity = DB::table('game_cities')->where('id', $gameId)->first();
    if (!$gameCity) {
        return response()->json([
            'success' => false,
            'message' => 'Game city not found'
        ], 200);
    }

    $now = Carbon::now('Asia/Kolkata')->format('H:i:s');

    if ($now >= $gameCity->start_time && $now <= $gameCity->end_time) {
        return response()->json([
            'success' => false,
            'message' => 'Bet is running, admin result cannot be updated now.'
        ], 200);
    }

    $todayResult = trim($record->today_result);

    // Check if already announced (numeric result)
    if (is_numeric($todayResult)) {
        return response()->json([
            'success' => false,
            'message' => 'Result already announced'
        ], 200);
    }

    // Only update if result is null, blank or XX
    if ($todayResult === '' || $todayResult === 'XX' || $todayResult === null) {
        DB::table('live_results')
            ->where('id', $record->id)
            ->update([
                'admin_result' => $adminResult,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Admin result updated successfully'
        ], 200);
    }

    return response()->json([
        'success' => false,
        'message' => 'Today\'s result is in unknown format'
    ], 200);
}//Admin pannel

}




