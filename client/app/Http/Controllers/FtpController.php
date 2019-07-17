<?php

namespace App\Http\Controllers;

use App\Ftp;

class FtpController extends Controller
{
    /**
     * Show ftp connexion form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function connect_form(){
        return view('ftp-login.index');
    }

    /**
     * Ftp connexion action and redirect
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function connect(){
        $conn = Ftp::instance(['host' => request('host'), 'port' => request('port')]);

        if(!$conn) {
            return redirect('/connect')->withErrors('Can\'t connect to ftp');
        }

        if (ftp_login($conn, request('username'), request('password'))) {
            ftp_pasv($conn, true);
            $files = ftp_nlist($conn, ".");
            return view('index')->with('file_list', $files);
        } else {
            return redirect('/connect')->withErrors('Credentials are invalid');
        }
    }
}
