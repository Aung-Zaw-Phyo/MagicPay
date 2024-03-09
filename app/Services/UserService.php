<?php 

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class UserService {
    public function generateVerificationKey($phone, $email)
    {
        $name = Str::upper(substr(str_replace(' ', '', $email), 0, 3));
        $date = date("Ymd");
        $number = mt_rand(100000, 999999);
        $generatedKey = str_shuffle($name . $date . $number);
        Cache::put('verification_key_'.$generatedKey, $phone, now()->addHours(24));
        return $generatedKey;
    }
}