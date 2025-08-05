<?php

namespace App\Http\Controllers\api;

use voku\helper\HtmlDomParser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
class LeaderController extends Controller
{
   function testLeader() {
        $win = "Leader result api is ok";
        return $win;
    }
    
  public function getLeaderboard(Request $request){
    $leaderboard = DB::table('mymatch')
        ->join('users', 'mymatch.user_id', '=', 'users.id')
        ->select('mymatch.user_id', 'users.name', DB::raw('SUM(mymatch.reward) as total_reward'))
        ->groupBy('mymatch.user_id', 'users.name')
        ->orderByDesc('total_reward')
        ->get();

    if ($leaderboard->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No leaderboard data found.',
        ], 200);
    }

    return response()->json([
        'success' => true,
        'message' => 'Leaderboard fetched successfully.',
        'data' => $leaderboard
    ], 200);
} //all date leader history
   
//   public function getLeaderboard(Request $request){
//     $today = now()->toDateString(); // e.g., "2025-07-29"

//     $leaderboard = DB::table('mymatch')
//         ->join('users', 'mymatch.user_id', '=', 'users.id')
//         ->select('mymatch.user_id', 'users.name', DB::raw('SUM(mymatch.reward) as total_reward'))
//         ->whereDate('mymatch.created_at', $today)
//         ->groupBy('mymatch.user_id', 'users.name')
//         ->orderByDesc('total_reward')
//         ->get();

//     if ($leaderboard->isEmpty()) {
//         return response()->json([
//             'success' => false,
//             'message' => 'No leaderboard data found for today.',
//         ], 200);
//     }

//     return response()->json([
//         'success' => true,
//         'message' => 'Leaderboard fetched successfully.',
//         'data' => $leaderboard
//     ], 200);
// } //current date

}    