<?php

namespace App\Http\Middleware;

use App\Mail\AccountVerificationMail;
use App\Models\User;
use App\Services\UserService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use ResponseHelper;

class CheckVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $phone = $request->input('phone');
        $user = User::where('phone', $phone)->first();
        if ($user && !$user->isVerify) {
            $key = (new UserService)->generateVerificationKey($user->phone, $user->email);
            Mail::to($user->email)->send(new AccountVerificationMail($key));

            $hashed_code = Crypt::encryptString($user->phone);
            return redirect()->route('account.status', $hashed_code);
        }
        return $next($request);
    }
}
