<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ChartController extends Controller
{
    public function getChartsByDate1(Request $request) {
        try {
            $date = $request->input('date'); // e.g. ?date=2025-07-23

            if (!$date) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide a date (YYYY-MM-DD)'
                ], 200);
            }

            $records = DB::table('charts')
                ->join('game_cities', 'charts.game_cites_id', '=', 'game_cities.id')
                ->whereDate('charts.created_at', $date)
                ->select(
                    'charts.result',
                    'charts.message',
                    'charts.status',
                    'game_cities.title as city_title',
                    'game_cities.image as city_image'
                )
                ->get();

            // Add base URL to image
            $baseUrl = url('/'); // This will return http://127.0.0.1:8000 or app URL

            foreach ($records as $record) {
                if (!empty($record->city_image)) {
                    $record->city_image = $baseUrl . '/' . ltrim($record->city_image, '/');
                }
            }

            if ($records->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No charts found for the given date',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Charts fetched successfully',
                'data' => $records
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching data',
                'error' => $e->getMessage()
            ], 200);
        }
    }
    
//     public function getChartsByDate(Request $request) {
//     try {
//         $date = $request->input('date'); // e.g. ?date=2025-07-23

//         if (!$date) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Please provide a date (YYYY-MM-DD)'
//             ], 200);
//         }

//         $records = DB::table('live_results')
//             ->join('game_cities', 'live_results.game_cites_id', '=', 'game_cities.id')
//             ->whereDate('live_results.created_at', $date)
//             ->select(
//                 DB::raw("CASE 
//                             WHEN live_results.admin_result IS NOT NULL AND live_results.admin_result != '' 
//                             THEN live_results.admin_result 
//                             ELSE live_results.today_result 
//                          END AS result"),
//                 'live_results.game',
//                 'live_results.time',
//                 'game_cities.title as city_title',
//                 'game_cities.image as city_image',
//                 'game_cities.start_time',
//                 'game_cities.end_time',
//                 'game_cities.result_time'
//             )
//             ->get();

//         // Add base URL to image
//         $baseUrl = url('/');

//         foreach ($records as $record) {
//             if (!empty($record->city_image)) {
//                 $record->city_image = $baseUrl . '/' . ltrim($record->city_image, '/');
//             }
//         }

//         if ($records->isEmpty()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'No live results found for the given date',
//                 'data' => []
//             ], 200);
//         }

//         return response()->json([
//             'success' => true,
//             'message' => 'Live results fetched successfully',
//             'data' => $records
//         ], 200);

//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Error fetching data',
//             'error' => $e->getMessage()
//         ], 200);
//     }
// }

public function getChartsByDate(Request $request)
{
    try {
        $date = $request->input('date'); // e.g. 2025-08-03

        if (!$date) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a date (YYYY-MM-DD)'
            ], 200);
        }

        $records = DB::table('live_results')
            ->join('game_cities', 'live_results.game_cites_id', '=', 'game_cities.id')
            ->whereDate('live_results.created_at', $date)
            ->select(
                DB::raw("CASE 
                            WHEN live_results.admin_result IS NOT NULL AND live_results.admin_result != '' 
                            THEN live_results.admin_result 
                            ELSE live_results.today_result 
                         END AS result"),
                'live_results.game',
                'live_results.time',
                'game_cities.title as city_title',
                'game_cities.image as city_image',
                'game_cities.start_time',
                'game_cities.end_time',
                'game_cities.result_time'
            )
            ->get();

        $baseUrl = url('/');
        $now = Carbon::now('Asia/Kolkata'); // ✅ Corrected to use Indian timezone

        foreach ($records as $record) {
            // Fix image URL
            if (!empty($record->city_image)) {
                $record->city_image = $baseUrl . '/' . ltrim($record->city_image, '/');
            }

            $record->status_show = 0; // Default

            try {
                if (!empty($record->end_time) && !empty($record->result_time)) {
                    $endTime = Carbon::parse($date . ' ' . trim($record->end_time), 'Asia/Kolkata');
                    $resultTime = Carbon::parse($date . ' ' . trim($record->result_time), 'Asia/Kolkata');

                    // If result_time is less than end_time, assume result_time is on the next day
                    if ($resultTime->lt($endTime)) {
                        $resultTime->addDay();
                    }

                    if ($now->between($endTime, $resultTime)) {
                        $record->status_show = 1;
                        Log::info("✔ status_show = 1 for {$record->city_title} | Now: $now | End: $endTime | Result: $resultTime");
                    } else {
                        Log::info("❌ status_show = 0 for {$record->city_title} | Now: $now | End: $endTime | Result: $resultTime");
                    }
                } else {
                    Log::warning("⚠️ Missing end_time or result_time for {$record->city_title}");
                }
            } catch (\Exception $e) {
                Log::error("⚠️ Time parsing failed for {$record->city_title}: " . $e->getMessage());
            }
        }

        if ($records->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No live results found for the given date',
                'data' => []
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Live results fetched successfully',
            'data' => $records
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching data',
            'error' => $e->getMessage()
        ], 200);
    }
}




}

