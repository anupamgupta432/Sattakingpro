<?php

namespace App\Http\Controllers\api;

use voku\helper\HtmlDomParser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
class NotifyController extends Controller
{
    function testNotify() {
        $win = "Notification result api is ok";
        return $win;
    }
    
    public function getUserNotifications(Request $request){
    $user_id = $request->input('user_id');

    if (!$user_id) {
        return response()->json([
            'success' => false,
            'message' => 'User ID is required.'
        ], 200);
    }

    $notifications = DB::table('notifications')
        ->where('user_id', $user_id)
        ->orderBy('created_at', 'desc')
        ->get();

    if ($notifications->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No notifications found for this user.'
        ], 200);
    }

    return response()->json([
        'success' => true,
        'message' => 'Notifications retrieved successfully.',
        'data' => $notifications
    ], 200);
}
     
    public function markNotificationAsRead(Request $request){
    $id = $request->input('notification_id');

    if (!$id) {
        return response()->json([
            'success' => false,
            'message' => 'Notification ID is required.'
        ], 200);
    }

    DB::table('notifications')->where('id', $id)->update(['is_read' => 1]);

    return response()->json([
        'success' => true,
        'message' => 'Notification marked as read.'
    ], 200);
}

}    