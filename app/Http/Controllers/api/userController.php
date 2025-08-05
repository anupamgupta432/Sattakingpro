<?php

namespace App\Http\Controllers\api;

use voku\helper\HtmlDomParser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
class userController extends Controller
{
    function testuser() {
        $win = "user profile result api is ok";
        return $win;
    }
    
    public function getUserProfile(Request $request){
    try {
        $user_id = $request->input('user_id');

        if (!$user_id) {
            return response()->json([
                'success' => false,
                'message' => 'User ID is required.'
            ], 200);
        }

        // Step 1: Get user
        $user = DB::table('users')->where('id', $user_id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 200);
        }

        // Step 2: Count how many times this user's referral code was used
        $referralUsedCount = DB::table('users')
            ->where('used_referral_code', $user->referral_code)
            ->count();

        // Step 3: Add referral usage count to user object
        $user->referral_used_count = $referralUsedCount;

        // Step 4: Append base URL to image (if exists)
        if (!empty($user->image)) {
            $baseUrl = url('/');
            $user->image = $baseUrl . $user->image;
        }

        return response()->json([
            'success' => true,
            'message' => 'User profile fetched successfully.',
            'data' => $user
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ], 200);
    }
}

    public function edituserProfile(Request $request) {
    $userId = $request->input('user_id');

    // Step 1: Check if user exists
    $user = DB::table('users')->where('id', $userId)->first();
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Please enter valid Id'
        ], 200);
    }

    $dataToUpdate = [];

    // Step 2: Name validation
    if ($request->has('name') && !empty($request->name)) {
        if (!preg_match("/^[a-zA-Z\s]+$/", $request->name)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter valid name, only letters and spaces are allowed.'
            ], 200);
        }
        $dataToUpdate['name'] = $request->name;
    }

    // Step 3: Email validation
    if ($request->has('email') && !empty($request->email)) {
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid email address.'
            ], 200);
        }

        $exists = DB::table('users')
            ->where('email', $request->email)
            ->where('id', '!=', $userId)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Email already taken.'
            ], 200);
        }

        $dataToUpdate['email'] = $request->email;
    }

    // Step 4: Image validation & saving
    if ($request->has('image') && !empty($request->image)) {
        $image = $request->image;
        $imageName = 'p' . $userId . '.png';
        $imagePath = public_path('profile/' . $imageName);

        // Ensure folder exists
        if (!file_exists(public_path('profile'))) {
            mkdir(public_path('profile'), 0777, true);
        }

        // Extract base64 image and decode
        $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
        $image = str_replace(' ', '+', $image);
        file_put_contents($imagePath, base64_decode($image));

        // Set path in DB
        $dataToUpdate['image'] = '/profile/' . $imageName;
    }

    // Step 5: If no valid data to update
    if (empty($dataToUpdate)) {
        return response()->json([
            'success' => false,
            'message' => 'No valid fields provided for update.'
        ], 200);
    }

    // Step 6: Update
    DB::table('users')->where('id', $userId)->update($dataToUpdate);

    return response()->json([
        'success' => true,
        'message' => 'Profile updated successfully.',
        'updated_fields' => $dataToUpdate
    ], 200);
}//image bhi hai

}