<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class OtpHelper
{
    public static function generateOtp()
    {
        return random_int(100000, 999999);
    }

    public static function sendOtp($email, $otp)
    {
        Mail::to($email)->send(new OtpMail($otp));
    }
}
