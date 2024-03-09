<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\FirebaseNoti;
use App\Helpers\UUIDGenerate;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Mail\AccountVerificationMail;
use App\Models\Otp;
use App\Models\User;
use App\Models\Wallet;
use App\Providers\RouteServiceProvider;
use App\Services\UserService;
use Base62\Base62;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator as ValidationValidator;
use ParagonIE\ConstantTime\Base64UrlSafe;
use phpseclib3\Crypt\AES;
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
                'amount' => 30000
            ]
        );
        
        return redirect($this->redirectTo);
    }




    public function register (RegisterRequest $request) 
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'ip' => $request->ip(),
            'user_agent' => $request->server('HTTP_USER_AGENT'),
            'login_at' => date('Y-m-d H:i:s')
        ]);

        Wallet::firstOrCreate(
            [
                'user_id' => $user->id
            ],
            [
                'account_number' => UUIDGenerate::account_number(),
                'amount' => 30000
            ]
        );

        $key = (new UserService)->generateVerificationKey($request->phone, $request->email);
        Mail::to($request->email)->send(new AccountVerificationMail($key));
        $hashed_code = Crypt::encryptString($request->phone);
        return redirect()->route('account.status', $hashed_code);       
    }



    public function status($hashed_code) 
    {
        try {
            $phone = Crypt::decryptString($hashed_code);
            $user = User::where('phone', $phone)->first();
            if(!$user) {
                abort(404);
            }
            return view('auth.status', [
                'email' => $user->email,
                'phone' => $user->phone,
            ]);
        } catch (\Throwable $th) {
            abort(404);
        }
    }

    public function resend(Request $request)
    {
        $user = User::where('phone', $request->phone)->first();
        if($user) {
            $key = (new UserService)->generateVerificationKey($user->phone, $user->email);
            Mail::to($user->email)->send(new AccountVerificationMail($key));

            return response()->json([
                'status' => 'success',
                'message' =>  'We sent verification link to your email.'
            ]); 
        }

        return response()->json([
            'status' => 'fail',
            'message' => 'Email sending failed!'
        ]);
    }

    public function verification($key)
    {
        $status = false;
        $phone = Cache::get('verification_key_'.$key);
        $user =User::where('phone', $phone)->first();
        if($user) {
            $status = true;
            $user->isVerify = true;
            $user->save();
            auth()->login($user);
        }
        return view('auth.verification', [
            'status' => $status
        ]);
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
