<?php

namespace App\Helpers;

class MobileNumberHelper
{
    public static function trim($mobileNumber)
    {
        $mobileNumber = (string) str_replace(" ", "", $mobileNumber);

        if (strlen($mobileNumber) < 10) {
            return null;
        }

        if (strlen($mobileNumber) == 10) {
            $mobileNumber = '+98' . $mobileNumber;
        } elseif (strlen($mobileNumber) == 11) {
            $mobileNumber = '+98' . mb_substr($mobileNumber, 1);
        } else {
            $mobileNumber = '+' . $mobileNumber;
        }

        return (string)$mobileNumber;
    }

    public static function checkTrimmedNumber($mobileNumber)
    {
        $subString = substr($mobileNumber, 1);
        if (strpos($mobileNumber, '+98') == 0 && is_numeric($subString) && strlen($subString) == 12) {
            return true;
        }
        return false;
    }

    public static function checkMobileNumber($phone)
    {
        $pattern = '/(?:^(009809[\d]{9}|00989[\d]{9}|\+9809[\d]{9}|\+989[\d]{9}|989[\d]{9}|9809[\d]{9}|09[\d]{9}|9[\d]{9}))$/';
        if (!preg_match($pattern, $phone)) {
            return false;
        }
        return true;
    }

    public static function formatMobile($mobile)
    {
        if (!self::checkMobileNumber($mobile)) {
            return null;
        }

        $patern = '/(?:^(009809|00989|\+9809|\+989|989|9809))/';

        $mobile = trim(preg_replace($patern, '09', $mobile));

        if ($mobile[0] == 9 && strlen($mobile) == 10) {
            $mobile = 0 . $mobile;
        }
        return $mobile;
    }
}
