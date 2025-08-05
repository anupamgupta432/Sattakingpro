<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;




class MymatchController extends Controller
{
public function getLiveMatches(){
       $currentTime = Carbon::now('Asia/Kolkata')->format('H:i:s');

    $matches = DB::table('game_cities')
        ->where('status', 'active')
        ->whereTime('start_time', '<=', $currentTime)
        ->whereTime('end_time', '>=', $currentTime)
        ->get();

    if ($matches->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No live matches found',
            'data' => []
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Live matches data fetched',
        'data' => $matches
    ]);
}

public function getMyMatchess(Request $request){
    $user_id = $request->user_id;

        if (!$request->user_id) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is required'
        ], 200);
    }
    $matches = DB::table('mymatch')
        ->join('game_cities', 'mymatch.game_cities_id', '=', 'game_cities.id')
        ->join('games', 'mymatch.games_id', '=', 'games.id')
        ->select(
            'mymatch.id',
            'game_cities.title as game_city_name',
            'games.name as game_name',
            'mymatch.bet_list',
            'mymatch.total_amount',
            'mymatch.reward',
            'mymatch.winning_no',
            'mymatch.winning_status',
            'mymatch.running_status',
            'mymatch.status',
            'mymatch.created_at'
        )
        ->where('mymatch.user_id', $user_id)
        ->orderBy('mymatch.id', 'desc')
        ->get()
        ->map(function ($match) {
            $match->bet_list = json_decode($match->bet_list);
            return $match;
        });

    if ($matches->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No mymatch data'
        ], 200);
    }

    return response()->json([
        'success' => true,
        'data' => $matches
    ], 200);
}

}