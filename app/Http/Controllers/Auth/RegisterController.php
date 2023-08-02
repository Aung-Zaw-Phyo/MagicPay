<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\FirebaseNoti;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerate;
use App\Http\Controllers\Controller;
use App\Models\Otp;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Validator as ValidationValidator;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    // protected function validator(array $data)
    // {
    //     return Validator::make($data, [
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         'phone' => ['required', 'string', 'unique:users'],
    //         'password' => ['required', 'string', 'min:8', 'confirmed'],
    //     ]);
    // }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    // protected function create(array $data)
    // {
    //     return User::create([
    //         'name' => $data['name'],
    //         'email' => $data['email'],
    //         'phone' => $data['phone'],
    //         'password' => Hash::make($data['password']),
    //     ]);
    // }

    protected function registered(Request $request, $user)
    {
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
        
        return redirect($this->redirectTo);
    }




    public function register (Request $request) 
    {
        if(!$request->device_token){
            return redirect()->back()->withErrors(['device_token' => 'Something wrong. Please check your internet connection.'])->withInput();
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $otp_number = $this->generateOTP($request->device_token);
        if($otp_number) {

            FirebaseNoti::sendNotification($request->device_token, 'Magic Pay', 'Your OTP number is ' . $otp_number);
            return redirect('otp')->with('registration_data', $data);
            // if($this->sendSMS($request->phone, $otp_number, "$otp_number is your OTP number.")) {
            //     return redirect('otp')->with('registration_data', $data);
            // }else{
            //     return redirect()->back()->withInput();
            // }
        }else{
            return redirect()->back()->withInput();
        }
    }

    public function otp ()
    {
        // if (!Session::get('registration_data')) {
        //     return redirect('register')->withInput();
        // }
        $data = Session::get('registration_data');
        return view('auth.otp_verify', compact('data'));
    }

    public function post_register (Request $request)
    {
        if(!$request->device_token){
            return redirect()->back()->withErrors(['device_token' => 'Something wrong. Please check your internet connection.'])->withInput();
        }
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'otp' => ['required', 'min:6', 'max:6']
        ]);
        if($validator->fails()) {
            if($request->name) {
                return redirect()->back()->withErrors(['otp' => $validator->errors()->first()])->withInput();
            }
            return redirect('/register')->withErrors(['otp' => $validator->errors()->first()])->withInput();
        }
        $otp = Otp::where('device_token', $request->device_token)->orderBy('id', 'DESC')->first();
        if($otp && $otp->otp_number == $request->otp){
            Otp::where('device_token', $request->device_token)->delete();
            // $user = User::create([
            //     'name' => $request->name,
            //     'email' => $request->email,
            //     'phone' => $request->phone,
            //     'password' => Hash::make($request->password),
            //     'ip' => $request->ip(),
            //     'user_agent' => $request->server('HTTP_USER_AGENT'),
            //     'login_at' => date('Y-m-d H:i:s')
            // ]);
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = $request->password;
            $user->ip = $request->ip();
            $user->user_agent = $request->server('HTTP_USER_AGENT');
            $user->device_token = $request->device_token;
            $user->login_at = date('Y-m-d H:i:s');
            $user->save();
            Wallet::firstOrCreate(
                [
                    'user_id' => $user->id
                ],
                [
                    'account_number' => UUIDGenerate::account_number(),
                    'amount' => 10000
                ]
            );
            auth()->login($user);
            return redirect('/');
        }else {
            return redirect()->back()->withErrors(['otp' => 'Your OTP is invalid!'])->withInput();
        }

    }



    public function generateOTP($token)
    {
        $generateOtp = mt_rand(100000,999999);
        $otpDb = new Otp();
        // $otpDb->phone = $phone;
        $otpDb->device_token = $token;
        $otpDb->otp_number = $generateOtp;
        if ($otpDb->save())
        {
            return $generateOtp;
        }else{
            return false;
        }
    }

    public static function sendSMS($phone,$otp,$message)
    {
        $token=config('sms_poh.access_token');
        //send sms here
        try {
            $client = new Client();
            $response=$client->request('POST', 'https://smspoh.com/api/v2/send', [
                'headers'=>[
                    'Authorization'=>"Bearer $token",
                ],
                'json' =>[
                    'message'=> $message ,
                    'to'=>$phone,
                    'sender'=>"",
                ]
            ] );
            $result=json_decode($response->getBody()->getContents(), true);
            return $result;
        } catch (ClientException $e) {
            if ($e->getCode()===401) {
                throw new AuthorizationException(
                    "Authorization failed. Please provide correct token for SmsPoh in configuration file.",
                    401
                );
            } elseif ($e->getCode()===403) {
                throw new BadRequestException(
                    $e->getMessage(),
                    403
                );
            }
        }
    }

}
