<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Models\Wallet;

class UserAuthController extends Controller
{
    //
public function login(Request $request){
    $validator = Validator::make($request->all(), [
        'id' => [
            'required',
            'string',
            'regex:/^[6-9]\d{9}$/'  // Only valid 10-digit numbers starting from 2-9
        ],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid phone number format'
            // 'message' => $validator->errors()
        ], 200); 
    }

    $id = $request->input('id');

    $user = User::where('phone', $id)->first(); 

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found',
            'isregistert' => 1
        ], 200);
    }

    return response()->json([
        'success' => true,
        'message' => 'Login successfully',
        'id' => $user->id,
        'name' => $user->name,
        'isregistert' => 0
    ], 200);
}
    
public function register(Request $request){
    try {

    $validator = Validator::make($request->all(), [
        'name' => ['required', 'regex:/^[a-zA-Z\s]+$/'],
        'phone' => [
           'required',
           'string',
           'regex:/^[2-9]\d{9}$/',
           'unique:users,phone'
        ],
        'email' => 'required|email:rfc,dns|unique:users,email',
        'referral_code' => 'nullable|string'
    ]);


if ($validator->fails()) {
    $errors = $validator->errors();

    $firstError = $errors->first(); // for general fallback

    return response()->json([
        'success' => false,
        'message' =>
            $errors->has('phone') ? 'Phone already exists or is invalid.' :
            ($errors->has('email') ? 'Email already exists or is invalid.' :
            ($errors->has('name') ? 'Name is required and must contain only letters.' :
            $firstError)),
    ], 200);
}


        $inputCode = $request->input('referral_code');

        DB::beginTransaction();

        $referrer = null;
        $referralReward = 0;

        // Step 1: Check referral code validity
        if (!empty($inputCode)) {
            $referrer = DB::table('users')->where('referral_code', $inputCode)->first();

            if (!$referrer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter correct referral code'
                ], 200);
            }

            // Step 2: Get referral reward amount
            $offer = DB::table('offers')->where('offer_name', 'registration_referral')->first();
            if ($offer) {
                $referralReward = $offer->offer_amount;
            }
        }

        // Step 3: Generate unique referral code
        do {
            $newReferralCode = Str::upper(Str::random(6));
        } while (DB::table('users')->where('referral_code', $newReferralCode)->exists());

        // Step 4: Insert new user
        $userId = DB::table('users')->insertGetId([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'referral_code' => $newReferralCode,
            'used_referral_code' => $inputCode ?: null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Step 5: Insert wallet for new user
        DB::table('wallets')->insert([
            'user_id' => $userId,
            'balance' => $referralReward,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Step 6: If referral used, update referrer wallet and log referral
       if ($referrer) {
    // Insert referral record
    DB::table('referrals')->insert([
        'user_id' => $referrer->id,
        'referral_code' => $inputCode,
        'referred_user_id' => $userId,
        'reward_amount' => $referralReward,
        'status' => 'success',
        'referred_at' => now(),
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // Update referrer wallet
    DB::table('wallets')->where('user_id', $referrer->id)->increment('balance', $referralReward);

    // Description for bet history
    $description = 'Referral Bonus amount: ' . $referralReward;

    // insert into bet_history for referrer
    DB::table('bet_history')->insert([
        'user_id' => $referrer->id,
        'title' => 'Referral',
        'description' => $description,
        'type' => 0,
        'amount' => $referralReward,
        'time' => now()->format('H:i:s'),
        'date' => now()->format('Y-m-d'),
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // insert into bet_history for newly registered user
    DB::table('bet_history')->insert([
        'user_id' => $userId,
        'title' => 'Referral',
        'description' => $description,
        'type' => 0,
        'amount' => $referralReward,
        'time' => now()->format('H:i:s'),
        'date' => now()->format('Y-m-d'),
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now()
    ]);
}


        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'user_id' => $userId,
            'referral_code' => $newReferralCode
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            // 'message' => 'Registration failed',
            'message' => $e->getMessage()
        ], 200);
    }
}
// @if (!session('admin_id'))
//     <script>
//         window.location.href = "/login";
//     </script>
// @endif

public function sighin(Request $request){
    $admin = DB::table('admin')->where('email', $request->email)->first();

    if ($admin && Hash::check($request->password, $admin->password)) {
        Session::put('admin_id', $admin->id);
        Session::put('admin_name', $admin->name);

        return redirect()->route('admin.dashboard');
    } else {
        return redirect('/login')->with('error', 'Invalid credentials.');
    }
}

public function logout(){
    Session::forget('admin_id');
    Session::forget('admin_name');
    return redirect('/login')->with('success', 'Logged out successfully.');
}

}


