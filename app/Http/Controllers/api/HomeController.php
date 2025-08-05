<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
class HomeController extends Controller
{

    public function getCompanyProfile(){
    try {
        $records = DB::table('company_profiles')->get();

        $customData = [];
        foreach ($records as $item) {
            $key = $item->custom_key ?? 'company_' . $item->id;
            $customData[$key] = $item;
        }
         // Fetch banners data
        // $banners = DB::table('banners')->where('status', 'active')->get(); //ye without url exact data 

        //  $banners = DB::table('banners')->where('status', 'active')->get()->map(function ($banner) {
        //     $banner->image = url($banner->image); // prepend base URL
        //     return $banner;
        // });//ye current url ke liye hai 

        $baseUrl = config('app.url');

        $banners = DB::table('banners')->where('status', 'active')->get()->map(function ($banner) use ($baseUrl) {
            $banner->image = $baseUrl . '/' . ltrim($banner->image, '/');
            return $banner;
        });//ye dynamic base url ke liye hai

        // $game_type = DB::table('game_cities')->where('status', 'active')->get();
        $game_type = DB::table('game_cities')->where('status', 'active')->get()->map(function ($game_cities) use ($baseUrl) {
            $game_cities->image = $baseUrl . '/' . ltrim($game_cities->image, '/');
            return $game_cities;
        });//ye dynamic base url ke liye hai        
        return response()->json([
            'success' => true,
            'message' => 'All data fetched successfully',
            'data' => $customData,
            'banner' =>$banners,
            'game_type' => $game_type
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch company profile',
            'error' => $e->getMessage()
        ], 200);
    }
}//ye sahi chal raha iseme custome key ke roop me data ayega

    public function getliveGames(){
    // $currentTime = Carbon::now()->format('H:i:s');
    $currentTime = Carbon::now('Asia/Kolkata')->format('H:i:s');

    // $games = DB::table('game_cities')
    //     ->where('status', 'active')
    //     ->whereTime('start_time', '<=', $currentTime)
    //     ->whereTime('end_time', '>=', $currentTime)
    //     ->get();//ye mifnigh crossing game support nahi kata hai
    
    $games = DB::table('game_cities')
    ->where('status', 'active')
    ->get()
    ->filter(function ($game) use ($currentTime) {
        return (
            ($game->start_time <= $game->end_time && $currentTime >= $game->start_time && $currentTime <= $game->end_time) ||
            ($game->start_time > $game->end_time && ($currentTime >= $game->start_time || $currentTime <= $game->end_time))
        );
    })
    ->values(); // Reset index after filter


    if ($games->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No live games found',
            'data' => []
        ], 200);
    }
    
        // Add base URL to image field
    $baseUrl = url('/'); // gives: https://sattakingpro.khiladi11.live/

    $games->transform(function ($game) use ($baseUrl) {
    $image = ltrim($game->image, '/'); // remove leading slash if any
    $game->image = $image ? $baseUrl .'/'. $image : null;
    $game->random_number = rand(1000, 2500);//add rendom 4 digit number
    return $game;
    });


    return response()->json([
        'success' => true,
        'message' => 'Live games data fetched',
        'data' => $games
        
    ], 200);
}

//  public function getBanner()
// {
//     try {
//         $records = DB::table('company_profiles')->get();

//         $customData = [];
//         foreach ($records as $item) {
//             $key = $item->custom_key ?? 'company_' . $item->id;
//             $customData[$key] = $item;
//         }

//         return response()->json([
//             'success' => true,
//             'message' => 'All data fetched successfully',
//             'data' => $customData
//         ], 200);

//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Failed to fetch company profile',
//             'error' => $e->getMessage()
//         ], 200);
//     }
// }

}
