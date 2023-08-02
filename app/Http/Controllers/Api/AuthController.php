<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;

class AuthController extends Controller
{
    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if($validator->fails()) {
            return fail($validator->errors()->first(), null, 422);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);

        $user->ip = $request->ip();
        $user->user_agent = $request->server('HTTP_USER_AGENT');
        $user->login_at = date('Y-m-d H:i:s');

        $user->save();

        Wallet::firstOrCreate(
            [
                'user_id' => $user->id
            ],
            [
                'account_number' => UUIDGenerate::account_number(),
                'amount' => 0
            ]
        );

        $token = $user->createToken('Magic Pay')->accessToken;

        return success('Successfully registered.', ['token' => $token]);
    }

    public function login (Request $request) {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if($validator->fails()) {
            return fail($validator->errors()->first(), null, 422);
        }

        if(Auth::attempt(['phone' => $request->phone, 'password' => $request->password])){
            $user = auth()->user();
            $user->ip = $request->ip();
            $user->user_agent = $request->server('HTTP_USER_AGENT');
            $user->login_at = date('Y-m-d H:i:s');
            $user->update();

            Wallet::firstOrCreate(
                [
                    'user_id' => $user->id
                ],
                [
                    'account_number' => UUIDGenerate::account_number(),
                    'amount' => 0
                ]
            );
            
            $token = $user->createToken('Magic Pay')->accessToken;

            return success('Successfully logined.', ['token' => $token]);
        }

        return fail('These credentials do not match our records.', null, 422);
    }

    public function logout () {
        $user = auth()->user();
        $user->tokens()->delete();
        return success('Successfully logout.', null);
    }

    public function updatePassword (Request $request) {
        $validator = Validator::make($request->all(), [
            'old_password' => ['required'],
            'new_password' => ['required', 'min:6', 'max:20']
        ]);

        if($validator->fails()) {
            return fail($validator->errors()->first(), null, 422);
        }

        $old_password = $request->old_password;
        $new_password = $request->new_password;
        $user = Auth::guard('api')->user();
        if(Hash::check($old_password, $user->password)){
            $user->password = Hash::make($new_password);
            $user->update();

            $title = 'Password Updated';
            $message = 'Your password updated successfully!';
            $sourceable_id = $user->id;
            $sourceable_type = User::class;
            $web_link = url('profile');
            $deep_link = [
                'target'  => 'profile',
                'parameter' => null
            ];

            Notification::send([$user], new GeneralNotification($title, $message, $sourceable_id, $sourceable_type, $web_link, $deep_link));
            return success('Successfully Updated.', null, 200);
        }
        return fail('The old password is not correct.', null, 422);
    } 
}
