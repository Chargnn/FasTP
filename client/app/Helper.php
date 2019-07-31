<?php

namespace App;

use Illuminate\Support\Facades\Cookie;


class Helper
{
    public static function searchCookies($startWith){
        $cookies = Cookie::get();

        $array = [];
        foreach($cookies as $key => $value){
            if(starts_with($key, $startWith)){
                $array[] = $value;
            }
        }

        return $array;
    }

    public static function checkCookieValid($cookie){
        return boolval(Cookie::get($cookie));
    }
}