<?php

namespace App\Http\Controllers;

use App\Ftp;
use Illuminate\Support\Facades\Cookie;

class HomeController extends Controller
{
    /**
     * Index
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function index(){
        $cookie = json_decode(Cookie::get('ftp'));

        if(!$cookie){
            return redirect('/connect');
        }

        $conn = Ftp::instance(['host' => $cookie->host, 'port' => $cookie->port]);

        if(!$conn){
            return redirect('/connect');
        }

        if (ftp_login($conn, $cookie->username, $cookie->password)) {
            if($to = session()->get('path')){
                $to = ftp_pwd($conn).$to;
                ftp_chdir($conn, $to);
            }
            ftp_pasv($conn, true);
            $file_list = ftp_nlist($conn, ".");
            return view('index')->with('file_list', $file_list)
                                      ->with('conn', $conn)
                                      ->with('credentials', $cookie);
        } else {
            return redirect('/connect')->withErrors('Credentials are invalid');
        }
    }
}
