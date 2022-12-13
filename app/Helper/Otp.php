<?php

namespace App\Helper;

use App\Models\Otp as OtpToken;

class Otp
{
    public static function generate()
    {
        $no = (string) random_int(0000, 9999);
        $no = "$no";

        if ( strlen($no) != 4 ) {
            return self::generate();
        }

        if (OtpToken::where('token', $no)->count() > 0 ) {
            return self::generate();
        }

        return $no;
    }
}
