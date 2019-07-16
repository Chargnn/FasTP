<?php

namespace App\Http\Controllers;

use Mockery\Exception;

class FtpController extends Controller
{
    // Show ftp connexion form
    public function connect_form(){
        return view('ftp-login.index');
    }

    // Ftp connexion action
    public function connect(){
        $conn = ftp_connect(request('host'), request('port'));

        if(!$conn)
            return redirect('/connect')->withErrors('Can\'t connect to ftp');

        if (ftp_login($conn, request('username'), request('password'))) {
            return redirect('/listing');
        } else {
            return redirect('/connect')->withErrors('Credentials are invalid');
        }
    }
}
