<?php

namespace App\Http\Controllers;

use App\Ftp;
use Illuminate\Support\Facades\Cookie;

class HomeController extends Controller
{
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
            ftp_pasv($conn, true);
            $file_list = ftp_nlist($conn, ".");
            return view('index')->with('file_list', $file_list)
                                      ->with('conn', $conn);
        } else {
            return redirect('/connect')->withErrors('Credentials are invalid');
        }
    }
}
