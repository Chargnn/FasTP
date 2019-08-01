<?php

namespace App\Http\Controllers;

use App\Ftp;
use App\Helper;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class TabController extends Controller
{
    public function add_form(){
        if(count(Helper::getEmptyCookies()) === 0){
            return redirect('/')->withErrors(['You have reached the maximum of tab']);
        }
        return view('tab-add.index');
    }

    public function add(){
        $cookies = Helper::searchCookies('ftp_');
        $request_values = ['host' => request('host'),
            'port' => request('port'),
            'username' => request('username'),
            'password' => request('password'),
            'alias' => request('alias') ?: request('host')];

        $cookie = null;
        for($i = 0; $i < count($cookies); $i++){
            if(Cookie::get('ftp_'.$i) === '') {
                $cookie = Cookie::make('ftp_'.$i, json_encode($request_values));
                break;
            }
        }

        if($cookie) {
            return redirect('/')->withCookie($cookie);
        } else {
            return redirect('/')->withErrors(['You have reached the maximum of tab']);
        }
    }

    public function change(){

    }
}
