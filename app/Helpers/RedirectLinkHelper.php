<?php

namespace App\Helpers;

class RedirectLinkHelper
{
    public static function get($packageName, $platform = null)
    {
        // $link = config('redirects.' . str_replace('.', '*', $packageName),  "https://uzee.ir/$packageName");
        // if ($platform) {
        //     $link .= ".$platform";
        // }
        // return $link;
        if($platform=="web"){
            return "https://fitvision.ir/FitnessApp/";

        }
        return "fitvision://payment";
    }
}
