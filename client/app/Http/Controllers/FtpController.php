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
            ftp_pasv($conn, true);
            $files = ftp_nlist($conn, ".");
            return view('index')->with('file_list', $files)->with('conn', $conn);
        } else {
            return redirect('/connect')->withErrors('Credentials are invalid');
        }
    }
}
