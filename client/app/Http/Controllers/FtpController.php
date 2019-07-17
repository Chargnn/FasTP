<?php

namespace App\Http\Controllers;

use App\Ftp;
use Illuminate\Support\Facades\Cookie;

class FtpController extends Controller
{
    /**
     * Show ftp connexion form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function connect_form(){
        if(Cookie::get('ftp') && Cookie::get('ftp') !== ''){
            return redirect('/');
        }
        return view('ftp-login.index');
    }

    /**
     * Ftp connexion action and redirect
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function connect(){
        $request_values = ['host' => request('host'),
                           'port' => request('port'),
                           'username' => request('username'),
                           'password' => request('password')];
        $conn = Ftp::instance(['host' => $request_values['host'], 'port' => $request_values['port']]);

        if(!$conn) {
            return redirect('/connect')->withErrors('Can\'t connect to ftp');
        }

        if (ftp_login($conn, request('username'), request('password'))) {
            return redirect('/')->withCookie(Cookie::make('ftp', json_encode($request_values), 120));
        } else {
            return redirect('/connect')->withErrors('Credentials are invalid');
        }
    }

    /**
     * Show ftp disconnect form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function disconnect_form(){
        return view('ftp-login.quit');
    }

    public function disconnect(){
        return redirect('/connect')->withCookie(Cookie::make('ftp', '', -1));
    }
}
