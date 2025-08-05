<?php

namespace App\Http\Controllers\api;

use voku\helper\HtmlDomParser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
class GatewayController extends Controller
{
    function testGateway() {
        $win = "testGateway result api is ok";
        return $win;
    }
    
//     public function rechargeRequest(Request $request){
//     $request->validate([
//         'user_id' => 'required|exists:users,id',
//         'amount' => 'required|numeric|min:1',
//         // 'transaction_type' => 'required|string|max:10',
//         'transaction_id' => 'required|string|max:100',
//         // 'description' => 'nullable|string|max:255',
//         'image' => 'nullable|string', // base64 image optional
//     ]);

//     $userId = $request->user_id;

//     $imagePath = null;
//     if ($request->has('image') && !empty($request->image)) {
//         $image = $request->image;
//         $imageName = 'recharge_' . $userId . '_' . time() . '.png';
//         $directory = public_path('recharge');

//         if (!file_exists($directory)) {
//             mkdir($directory, 0777, true);
//         }

//         $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
//         $image = str_replace(' ', '+', $image);
//         file_put_contents($directory . '/' . $imageName, base64_decode($image));

//         $imagePath = '/recharge/' . $imageName;
//     }
    
//     // 1. Get current wallet balance from 'wallets' table
//       $wallet = DB::table('wallets')->where('user_id', $userId)->first();
//      if (!$wallet) {
//     return response()->json(['success' => false, 'message' => 'Wallet not found.'], 200);
//      }

//     if ($request->transaction_type === 'debit') {
//     if ($wallet->balance < $request->amount) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Insufficient wallet balance.',
//         ], 200);
//     }

//     // 2. Deduct balance
//     DB::table('wallets')->where('user_id', $userId)->update([
//         'balance' => $wallet->balance - $request->amount,
//     ]);
//     }


//     // Insert transaction into database
//     DB::table('transactions')->insert([
//         'user_id' => $userId,
//         'amount' => $request->amount,
//         'bonus' => 0,
//         // 'transaction_type' => $request->transaction_type,
//         'transaction_type' =>'credit',
//         'method' => 'others',
//         'transaction_id' => $request->transaction_id,
//         'description' => $request->description ?? '',
//         'image' => $imagePath,
//         'status' => 'pending',
//         'created_at' => now(),
//         'updated_at' => now(),
//     ]);

//     return response()->json([
//         'success' => true,
//         'message' => 'Request submitted successfully.',
//     ], 200);
// }

public function rechargeRequest(Request $request){
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'amount' => 'required|numeric|min:1,max:2000',
        // 'transaction_type' => 'required|string|in:credit',
        'transaction_id' => 'required|string|regex:/^[a-zA-Z0-9\-_]{10,30}$/',
        'image' => 'nullable|string',
        // 'description' => 'nullable|string|max:255',
    ]);

    $userId = $request->user_id;
    
    // Check if transaction_id already exists
    $exists = DB::table('transactions')
    ->where('transaction_id', $request->transaction_id)
    ->exists();

   if ($exists) {
    return response()->json([
        'success' => false,
        'message' => 'Transaction ID already used.',
    ], 200);
    }


    // Handle image (optional)
    $imagePath = null;
    if ($request->has('image') && !empty($request->image)) {
        $image = $request->image;
        $imageName = 'recharge_' . $userId . '_' . time() . '.png';
        $directory = public_path('recharge');

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
        $image = str_replace(' ', '+', $image);
        $decodedImage = base64_decode($image);

        if ($decodedImage !== false) {
            file_put_contents($directory . '/' . $imageName, $decodedImage);
            $imagePath = '/recharge/' . $imageName;
        }
    }
     $now = \Carbon\Carbon::now('Asia/Kolkata');
    // Insert recharge transaction (credit only)
    DB::table('transactions')->insert([
        'user_id' => $userId,
        'amount' => $request->amount,
        'bonus' => 0,
        'transaction_type' => 'credit',
        'method' => 'others',
        'transaction_id' => $request->transaction_id,
        'description' => $request->description ?? '',
        'image' => $imagePath,
        'status' => 'pending',
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Recharge request submitted successfully.',
    ], 200);
}//credit recharge request

// public function debitRequest(Request $request) {
//     $validator = Validator::make($request->all(), [
//         'user_id'       => 'required|exists:users,id',
//         'amount'        => 'required|numeric|min:1',
//         // 'transaction_id'=> 'nullable|string|max:100',
//         //'image'         => 'nullable|string',
//         'account_name'  => 'nullable|string|max:100|required_with:account_no,ifsc_code,bank_name,branch_name',
//         'account_no'    => 'nullable|string|max:50|required_with:account_name,ifsc_code,bank_name,branch_name',
//         'ifsc_code'     => ['nullable','string','max:20','required_with:account_name,account_no,bank_name,branch_name','regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
//         'bank_name'     => 'nullable|string|max:100|required_with:account_name,account_no,ifsc_code,branch_name',
//         'branch_name'   => 'nullable|string|max:100|required_with:account_name,account_no,ifsc_code,bank_name',
//         'upi_id'        => ['nullable','string','max:100','regex:/^[\w.-]+@[\w.-]+$/'],
//     ]);
    
//     if ($validator->fails()) {
//     return response()->json([
//         'success' => false,
//         'message' => $validator->errors()->first(),  // पहला error message
//     ], 200);
// }


//     // if ($validator->fails()) {//all error msg
//     //     return response()->json([
//     //         'success' => false,
//     //         'message' => collect($validator->errors())->map(function($msg) {
//     //             return $msg[0];
//     //         }),
//     //     ], 200);
//     // }

//     $hasBankDetails = $request->account_name && $request->account_no && $request->ifsc_code && $request->bank_name && $request->branch_name;
//     $hasUpiId = $request->upi_id;

//     if (!$hasBankDetails && !$hasUpiId) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Either bank details or UPI ID is required.',
//         ], 200);
//     }

//     if ($hasBankDetails && $hasUpiId) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Please provide either bank details OR UPI ID, not both.',
//         ], 200);
//     }

//     $userId = $request->user_id;

//     $imagePath = null;
//     if (!empty($request->image)) {
//         $image = preg_replace('/^data:image\/\w+;base64,/', '', $request->image);
//         $image = str_replace(' ', '+', $image);
//         $imageName = 'withdraw_' . $userId . '_' . time() . '.png';
//         $directory = public_path('withdraw');

//         if (!file_exists($directory)) {
//             mkdir($directory, 0777, true);
//         }

//         file_put_contents($directory . '/' . $imageName, base64_decode($image));
//         $imagePath = '/withdraw/' . $imageName;
//     }

//     $wallet = DB::table('wallets')->where('user_id', $userId)->first();
//     if (!$wallet) {
//         return response()->json(['success' => false, 'message' => 'Wallet not found.'], 200);
//     }

//     if ($wallet->balance < $request->amount) {
//         return response()->json(['success' => false, 'message' => 'Insufficient wallet balance.'], 200);
//     }

//     DB::table('wallets')->where('user_id', $userId)->update([
//         'balance' => $wallet->balance - $request->amount,
//     ]);

//     DB::table('transactions')->insert([
//         'user_id'        => $userId,
//         'amount'         => $request->amount,
//         'bonus'          => 0,
//         'transaction_type'=> 'debit',
//         'method'         => $hasUpiId ? 'upi' : 'bank',
//         'transaction_id' => '',
//         'description'    => $request->description ?? '',
//         'image'          => $imagePath,
//         'status'         => 'pending',
//         'account_name'   => $request->account_name ?? null,
//         'account_no'     => $request->account_no ?? null,
//         'ifsc_code'      => $request->ifsc_code ?? null,
//         'bank_name'      => $request->bank_name ?? null,
//         'branch_name'    => $request->branch_name ?? null,
//         'upi_id'         => $request->upi_id ?? null,
//         'created_at'     => now(),
//         'updated_at'     => now(),
//     ]);

//     return response()->json([
//         'success' => true,
//         'message' => 'Withdrawal request submitted successfully.',
//     ], 200);
// }//widrall ke liye image or upi id all validation okSS

public function debitRequest(Request $request) {
    $validator = Validator::make($request->all(), [
        'user_id'       => 'required|exists:users,id',
        'amount' => 'required|numeric|min:100',
        'account_name'  => 'nullable|string|max:100|required_with:account_no,ifsc_code,bank_name',
        'account_no'    => 'nullable|string|max:50|required_with:account_name,ifsc_code,bank_name',
        'ifsc_code'     => ['nullable','string','max:20','required_with:account_name,account_no,bank_name','regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
        'bank_name'     => 'nullable|string|max:100|required_with:account_name,account_no,ifsc_code',
        'mobile_no' => 'required|string|digits:10',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
        ], 200);
    }

    $hasBankDetails = $request->account_name && $request->account_no && $request->ifsc_code && $request->bank_name;

    if (!$hasBankDetails) {
        return response()->json([
            'success' => false,
            'message' => 'Bank details are required.',
        ], 200);
    }

    $userId = $request->user_id;

    $wallet = DB::table('wallets')->where('user_id', $userId)->first();
    if (!$wallet) {
        return response()->json(['success' => false, 'message' => 'Wallet not found.'], 200);
    }

    if ($wallet->balance < $request->amount) {
        return response()->json(['success' => false, 'message' => 'Insufficient wallet balance.'], 200);
    }

    DB::table('wallets')->where('user_id', $userId)->update([
        'balance' => $wallet->balance - $request->amount,
    ]);
    
     $now = \Carbon\Carbon::now('Asia/Kolkata');

    DB::table('transactions')->insert([
        'user_id'        => $userId,
        'amount'         => $request->amount,
        'bonus'          => 0,
        'transaction_type'=> 'debit',
        'method'         => 'bank',
        'transaction_id' => null,
        'description'    => $request->description ?? '',
        'image'          => null,
        'status'         => 'pending',
        'account_name'   => $request->account_name,
        'account_no'     => $request->account_no,
        'ifsc_code'      => $request->ifsc_code,
        'bank_name'      => $request->bank_name,
        'mobile_no'    => $request->mobile_no,
        'upi_id'         => null,
        'created_at'     => $now,
        'updated_at'     => $now,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Withdrawal request submitted successfully.',
    ], 200);
}


// public function updateTransactionStatus(Request $request){
//     $request->validate([
//         'id'     => 'required|exists:transactions,id',
//         'status' => 'required|in:success,failed',
//     ]);

//     $transaction = DB::table('transactions')->where('id', $request->id)->first();

//     if (!$transaction) {
//         return response()->json(['success' => false, 'message' => 'Transaction not found.']);
//     }

//     $userId = $transaction->user_id;
//      $now = \Carbon\Carbon::now('Asia/Kolkata');
//     // Update transaction status
//     DB::table('transactions')->where('id', $request->id)->update([
//         'status' => $request->status,
//         'updated_at' => $now,
//     ]);

//     // If status is 'success' and type is 'credit', update wallet
//     if ($transaction->status === 'pending' && $transaction->transaction_type === 'credit') {
//         $wallet = DB::table('wallets')->where('user_id', $userId)->first();
//         if ($wallet) {
//             DB::table('wallets')->where('user_id', $userId)->update([
//                 'balance' => $wallet->balance + $transaction->amount,
//                 'updated_at' => $now,
//             ]);
//         }
//     }
    
    

//     // Insert into bet_history
//     DB::table('bet_history')->insert([
//         'user_id'    => $userId,
//         'title'      => $transaction->transaction_type === 'credit' ? 'Wallet Credit' : 'Wallet Debit',
//         'description'=> $request->status === 'success'? 'RECHARGE SUCCESS':'RECHARGE FAILED',
//         'type'       => $transaction->transaction_type === 'credit' ? 0 : 1,
//         'amount'     => $transaction->amount,
//         'time'       => $now->format('H:i:s'),
//         'date'       => $now->format('Y-m-d'),
//         'status'     => 'active',
//         'created_at' => $now,
//         'updated_at' => $now,
//     ]);

//     return response()->json(['success' => true, 'message' => 'Transaction processed successfully.']);
// }

public function updateTransactionStatus(Request $request){
    $request->validate([
        'id'     => 'required|exists:transactions,id',
        'status' => 'required|in:success,failed',
    ]);

    $transaction = DB::table('transactions')->where('id', $request->id)->first();

    if (!$transaction) {
        return response()->json(['success' => false, 'message' => 'Transaction not found.']);
    }

    // Only proceed if status is pending
    if ($transaction->status !== 'pending') {
        return response()->json(['success' => false, 'message' => 'Transaction already processed.']);
    }

    $userId = $transaction->user_id;
    $now = \Carbon\Carbon::now('Asia/Kolkata');

    // Update transaction status
    DB::table('transactions')->where('id', $request->id)->update([
        'status' => $request->status,
        'updated_at' => $now,
    ]);

    // If new status is 'success' and type is 'credit', update wallet
    if ($request->status === 'success' && $transaction->transaction_type === 'credit') {
        $wallet = DB::table('wallets')->where('user_id', $userId)->first();
        if ($wallet) {
            DB::table('wallets')->where('user_id', $userId)->update([
                'balance' => $wallet->balance + $transaction->amount,
                'updated_at' => $now,
            ]);
        }
    }
    
    if ($request->status === 'failed' && $transaction->transaction_type === 'debit') {
        $wallet = DB::table('wallets')->where('user_id', $userId)->first();
        if ($wallet) {
            DB::table('wallets')->where('user_id', $userId)->update([
                'balance' => $wallet->balance + $transaction->amount,
                'updated_at' => $now,
            ]);
        }
    }

    // bet_history tabhi banega agar original status pending tha
    DB::table('bet_history')->insert([
        'user_id'    => $userId,
        'title'      => $transaction->transaction_type === 'credit' ? 'Wallet Credit' : 'Wallet Debit',
        'description'=> $request->status === 'success' ? 'SUCCESS' : 'FAILED',
        'type'       => $transaction->transaction_type === 'credit' ? 0 : 1,
        'amount'     => $transaction->amount,
        'time'       => $now->format('H:i:s'),
        'date'       => $now->format('Y-m-d'),
        'status'     => 'active',
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    return response()->json(['success' => true, 'message' => 'Transaction processed successfully.']);
}


}