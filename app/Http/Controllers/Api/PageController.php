<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerate;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\ProfileResource;
use App\Notifications\GeneralNotification;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\NotificationResource;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\TransferFormValidatioin;
use App\Http\Resources\TransactionDetailResource;
use App\Http\Resources\NotificationDetailResource;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function profile () {
        $user = Auth::guard('api')->user();
        $data = new ProfileResource($user);
        return success('success', $data);
    }

    public function transaction (Request $request) {
        $user = Auth::guard('api')->user();
        $transactions = Transaction::with('user', 'source')->orderBy('created_at', 'DESC')->where('user_id', $user->id);
        if ($request->date) {
            $transactions = $transactions->whereDate('created_at', $request->date);
        }
        if ($request->type) {
            $transactions = $transactions->where('type', $request->type);
        }
        $transactions = $transactions->paginate(5);
        // resource additional 
        $data = TransactionResource::collection($transactions)->additional(['result' => true, 'message' => 'success']);
        return $data;
    }

    public function transactionDetail ($trx_id) {
        $authUser = Auth::guard('api')->user();
        $transaction = Transaction::with('user', 'source')->where('user_id', $authUser->id)->where('trx_id', $trx_id)->first();
        if(!$transaction) {
            return fail('Could not found transaction. ', null, 404);
        }
        $data = new TransactionDetailResource($transaction);
        return success('success', $data);

    }

    public function notification () {
        $user = auth()->user();
        $notifications = $user->notifications()->paginate(5);

        return NotificationResource::collection($notifications)->additional(['result' => 1, 'message' => 'success']);
    }

    // public function show ($id) {
    //     $user = Auth::guard('web')->user();
    //     $notification = $user->notifications()->where('id', $id)->firstOrFail();
    //     $notification->markAsRead();
    //     return view('frontend.notification_detail', compact('notification'));
    // }
    public function notificationDetail ($id) {
        $user = auth()->user(); 
        $notification = $user->notifications()->where('id', $id)->first();
        if(!$notification) {
            return fail('Could not found notification.', null, 404);
        }
        $notification->markAsRead();

        $data = new NotificationDetailResource($notification);
        return success('success', $data);
    }

    public function toAccountVerify (Request $request) {
        $user = Auth::guard('api')->user();
        if($user->phone !=  $request->phone){
            $user = User::where('phone', $request->phone)->first();
            if($user){
                return success('success', ['name' => $user->name, 'phone' => $user->phone]);
            }
        }
        return fail('Invalid Phone Number.', null, 404);
    }

    public function transferConfirm (Request $request){
        $validator = Validator::make($request->all(), [
            'to_phone' => ['required'],
            'amount' => ['required', 'integer'],
            'hash_value' => ['required']
        ], [
            'to_phone.required' => "Please fill the accepter's phone number.",
            'amount.required' => 'Please fill the amount field.',
            'hash_value.required' => 'The given data is invalid.'
        ]);

        if($validator->fails()) {
            return fail($validator->errors()->first(), null, 404);
        }

        $from_account = auth()->user();

        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $str = $to_phone.$amount.$description;
        $hash_value = $request->hash_value;
        $hash_value_2 = hash_hmac('sha256', $str, 'magicpay123!@#');

        $to_account  = User::where('phone', $to_phone)->where('phone', '!=', $from_account->phone)->first();
       
        if (!$to_account){
            return fail('To account is invallid.', null, 404);
        }
        if ($amount < 1000) {
            return fail('The amount must be at least 1000 MMK.', null, 422);
        }
        if (!$from_account->wallet || !$to_account->wallet){
            return fail('The given data is invalid.', null, 422);
        }
        if ($from_account->wallet->amount < $amount) {
            return fail('The amount is not enough.', null, 422);
        }
        if ($hash_value !== $hash_value_2) {
            return fail('The given data is invalid.', null, 422);
        }

        return success('success', [
            'from_name' => $from_account->name,
            'from_phone' => $from_account->phone,

            'to_name' => $to_account->name,
            'to_phone' => $to_account->phone,

            'amount' => $amount,
            'description'  => $description,
            'hash_value' => $hash_value
        ]);
    }

    public function transferComplete (Request $request){
        $validator = Validator::make($request->all(), [
            'to_phone' => ['required'],
            'amount' => ['required', 'integer'],
            'hash_value' => ['required']
        ], [
            'to_phone.required' => "Please fill the accepter's phone number.",
            'amount.required' => 'Please fill the amount field.',
            'hash_value.required' => 'The given data is invalid.'
        ]);

        if($validator->fails()) {
            return fail($validator->errors()->first(), null, 404);
        }

        $authUser = auth()->user();
        if (!$request->password) {
            return fail('Please fill the password.', null, 422);
        }

        if (!Hash::check($request->password, $authUser->password)) {
            return fail('Password is incorrect.', null, 422);
        }



        $from_account = $authUser;

        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $str = $to_phone.$amount.$description;

        $hash_value = $request->hash_value;
        $hash_value_2 = hash_hmac('sha256', $str, 'magicpay123!@#');

        $to_account  = User::where('phone', $to_phone)->where('phone', '!=', $from_account->phone)->first();
       
        if (!$to_account){
            return fail('To account is invallid.', null, 422);
        }
        if ($amount < 1000) {
            return fail('The amount must be at least 1000 MMK.', null, 422);
        }
        if (!$from_account->wallet || !$to_account->wallet){
            return fail('The given data is invalid.', null, 422);
        }
        if ($from_account->wallet->amount < $amount) {
            return fail('The amount is not enough.', null, 422);
        }
        if ($hash_value !== $hash_value_2) {
            return fail('The given data is invalid.', null, 422);
        }
        
        DB::beginTransaction();

        try {
            $from_account_wallet = $from_account->wallet;
            $from_account_wallet->decrement('amount', $amount);
            $from_account_wallet->update();

            $to_account_wallet = $to_account->wallet;
            $to_account_wallet->increment('amount', $amount);
            $to_account_wallet->update();

            $refNumber = UUIDGenerate::refNumber();

            $from_account_transaction = new Transaction();
            $from_account_transaction->ref_no = $refNumber;
            $from_account_transaction->trx_id = UUIDGenerate::trxId();
            $from_account_transaction->user_id = $from_account->id;
            $from_account_transaction->type = 2;
            $from_account_transaction->amount = $amount;
            $from_account_transaction->source_id = $to_account->id;
            $from_account_transaction->description = $description;
            $from_account_transaction->save();

            $to_account_transaction = new Transaction();
            $to_account_transaction->ref_no = $refNumber;
            $to_account_transaction->trx_id = UUIDGenerate::trxId();
            $to_account_transaction->user_id = $to_account->id;
            $to_account_transaction->type = 1;
            $to_account_transaction->amount = $amount;
            $to_account_transaction->source_id = $from_account->id;
            $to_account_transaction->description = $description;
            $to_account_transaction->save();

            // From Noti
            $title = 'E-money Transfered!';
            $message = 'Your wallet transfered ' . number_format($amount) . ' MMK to ' . $to_account->name . ' ( ' . $to_account->phone . ' ) . ';
            $sourceable_id = $from_account_transaction->id;
            $sourceable_type = Transaction::class;
            $web_link = url("/transaction/$from_account_transaction->trx_id");
            $deep_link = [
                'target'  => 'transaction_detail',
                'parameter' => [
                    'trx_id' => $from_account_transaction->trx_id
                ]
            ];

            Notification::send([$from_account], new GeneralNotification($title, $message, $sourceable_id, $sourceable_type, $web_link, $deep_link));

            // To Noti
            $title = 'E-money Received!';
            $message = 'Your wallet received ' . number_format($amount) . ' MMK from ' . $from_account->name . ' ( ' . $from_account->phone . ' ) . ';
            $sourceable_id = $to_account_transaction->id;
            $sourceable_type = Transaction::class;
            $web_link = url("/transaction/$to_account_transaction->trx_id");
            $deep_link = [
                'target'  => 'transaction_detail',
                'parameter' => [
                    'trx_id' => $to_account_transaction->trx_id
                ]
            ];

            Notification::send([$to_account], new GeneralNotification($title, $message, $sourceable_id, $sourceable_type, $web_link, $deep_link));

            DB::commit();
            return success('Transfer successfully.', ['trx_id' => $from_account_transaction->trx_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return fail('Something wrong. ' . $e->getMessage(), null);
        }

    }

    public function scanAndPayForm (Request $request) {
        $from_account = auth()->user();
        $to_account = User::where('phone', $request->to_phone)->where('phone', '!=', $from_account->phone)->first();
        if(!$to_account){
            return fail('QR code is invalid.', null, 404);
        }
        return success('success', [
            'form_name' => $from_account->name,
            'from_phone' => $from_account->phone,
            'to_name' => $to_account->name,
            'to_phone' => $to_account->phone
        ]);
    }

    public function scanAndPayConfirm (Request $request) {
        $validator = Validator::make($request->all(), [
            'to_phone' => ['required'],
            'amount' => ['required', 'integer'],
            'hash_value' => ['required']
        ], [
            'to_phone.required' => "Please fill the accepter's phone number.",
            'amount.required' => 'Please fill the amount field.',
            'hash_value.required' => 'The given data is invalid.'
        ]);

        if($validator->fails()) {
            return fail($validator->errors()->first(), null, 404);
        }

        $from_account = auth()->user();

        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $str = $to_phone.$amount.$description;

        $hash_value = $request->hash_value;
        $hash_value_2 = hash_hmac('sha256', $str, 'magicpay123!@#');

        $to_account  = User::where('phone', $to_phone)->where('phone', '!=', $from_account->phone)->first();
       
        if (!$to_account){
            return fail('To account is invallid.', null);
        }
        if ($amount < 1000) {
            return fail('The amount must be at least 1000 MMK.', null);
        }
        if ($hash_value !== $hash_value_2) {
            return fail('The given data is invalid.', null);
        }
        if (!$from_account->wallet || !$to_account->wallet){
            return fail('The given data is invalid.', null);
        }
        if ($from_account->wallet->amount < $amount) {
            return fail('The amount is not enough.', null);
        }

        return success('success', [
            'from_name' => $from_account->name,
            'from_phone' => $from_account->phone,

            'to_name' => $to_account->name,
            'to_phone' => $to_account->phone,

            'amount' => $amount,
            'description'  => $description,
            'hash_value' => $hash_value
        ]);
    }


    public function scanAndPayComplete (Request $request){
        $validator = Validator::make($request->all(), [
            'to_phone' => ['required'],
            'amount' => ['required', 'integer'],
            'hash_value' => ['required']
        ], [
            'to_phone.required' => "Please fill the accepter's phone number.",
            'amount.required' => 'Please fill the amount field.',
            'hash_value.required' => 'The given data is invalid.'
        ]);

        if($validator->fails()) {
            return fail($validator->errors()->first(), null, 404);
        }

        $authUser = auth()->user();
        if (!$request->password) {
            return fail('Please fill the password.', null, 404);
        }

        if (!Hash::check($request->password, $authUser->password)) {
            return fail('Password is incorrect.', null, 404);
        }



        $from_account = $authUser;

        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $str = $to_phone.$amount.$description;

        $hash_value = $request->hash_value;
        $hash_value_2 = hash_hmac('sha256', $str, 'magicpay123!@#');

        $to_account  = User::where('phone', $to_phone)->where('phone', '!=', $from_account->phone)->first();
       
        if (!$to_account){
            return fail('To account is invallid.', null);
        }
        if ($amount < 1000) {
            return fail('The amount must be at least 1000 MMK.', null);
        }
        if (!$from_account->wallet || !$to_account->wallet){
            return fail('The given data is invalid.', null);
        }
        if ($from_account->wallet->amount < $amount) {
            return fail('The amount is not enough.', null);
        }
        if ($hash_value !== $hash_value_2) {
            return fail('The given data is invalid.', null);
        }
        
        DB::beginTransaction();

        try {
            $from_account_wallet = $from_account->wallet;
            $from_account_wallet->decrement('amount', $amount);
            $from_account_wallet->update();

            $to_account_wallet = $to_account->wallet;
            $to_account_wallet->increment('amount', $amount);
            $to_account_wallet->update();

            $refNumber = UUIDGenerate::refNumber();

            $from_account_transaction = new Transaction();
            $from_account_transaction->ref_no = $refNumber;
            $from_account_transaction->trx_id = UUIDGenerate::trxId();
            $from_account_transaction->user_id = $from_account->id;
            $from_account_transaction->type = 2;
            $from_account_transaction->amount = $amount;
            $from_account_transaction->source_id = $to_account->id;
            $from_account_transaction->description = $description;
            $from_account_transaction->save();

            $to_account_transaction = new Transaction();
            $to_account_transaction->ref_no = $refNumber;
            $to_account_transaction->trx_id = UUIDGenerate::trxId();
            $to_account_transaction->user_id = $to_account->id;
            $to_account_transaction->type = 1;
            $to_account_transaction->amount = $amount;
            $to_account_transaction->source_id = $from_account->id;
            $to_account_transaction->description = $description;
            $to_account_transaction->save();

            // From Noti
            $title = 'E-money Transfered!';
            $message = 'Your wallet transfered ' . number_format($amount) . ' MMK to ' . $to_account->name . ' ( ' . $to_account->phone . ' ) . ';
            $sourceable_id = $from_account_transaction->id;
            $sourceable_type = Transaction::class;
            $web_link = url("/transaction/$from_account_transaction->trx_id");
            $deep_link = [
                'target'  => 'transaction_detail',
                'parameter' => [
                    'trx_id' => $from_account_transaction->trx_id
                ]
            ];

            Notification::send([$from_account], new GeneralNotification($title, $message, $sourceable_id, $sourceable_type, $web_link, $deep_link));

            // To Noti
            $title = 'E-money Received!';
            $message = 'Your wallet received ' . number_format($amount) . ' MMK from ' . $from_account->name . ' ( ' . $from_account->phone . ' ) . ';
            $sourceable_id = $to_account_transaction->id;
            $sourceable_type = Transaction::class;
            $web_link = url("/transaction/$to_account_transaction->trx_id");
            $deep_link = [
                'target'  => 'transaction_detail',
                'parameter' => [
                    'trx_id' => $to_account_transaction->trx_id
                ]
            ];

            Notification::send([$to_account], new GeneralNotification($title, $message, $sourceable_id, $sourceable_type, $web_link, $deep_link));

            DB::commit();
            return success('Transfer successfully.', ['trx_id' => $from_account_transaction->trx_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return fail('Something wrong. ' . $e->getMessage(), null);
        }

    }

}
