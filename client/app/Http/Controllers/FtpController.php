<?php

namespace App\Http\Controllers;

use App\Ftp;
use http\Env\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Input;

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
     * Disconnect action (remove cookie)
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function disconnect(){
        return redirect('/connect')->withCookie(Cookie::make('ftp', '', -1));
    }

    /**
     * Download ftp file into client's computer
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function download(){
        $cookie = json_decode(Cookie::get('ftp'));

        if(!$cookie){
            return redirect('/connect');
        }

        $conn = Ftp::instance(['host' => $cookie->host, 'port' => $cookie->port]);

        if(!$conn) {
            return redirect('/connect')->withErrors('Can\'t connect to ftp');
        }

        $file = request()->route('file');
        if (ftp_login($conn, $cookie->username, $cookie->password)) {
            ftp_pasv($conn, true);
            $size = ftp_size($conn, $file);

            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . basename($file));
            header("Content-Length: $size");

            ftp_get($conn, 'php://output', $file, FTP_BINARY);

        } else {
            return redirect('/connect')->withErrors('Credentials are invalid');
        }
    }

    /**
     * Delete file from ftp
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(){
        $cookie = json_decode(Cookie::get('ftp'));

        if(!$cookie){
            return redirect('/connect');
        }

        $conn = Ftp::instance(['host' => $cookie->host, 'port' => $cookie->port]);

        if(!$conn) {
            return redirect('/connect')->withErrors('Can\'t connect to ftp');
        }

        $file = request()->route('file');
        if (ftp_login($conn, $cookie->username, $cookie->password)) {
            ftp_pasv($conn, true);
            ftp_delete($conn, $file);
            return redirect('/');
        } else {
            return redirect('/connect')->withErrors('Credentials are invalid');
        }
    }

    public function see(){
        $cookie = json_decode(Cookie::get('ftp'));

        if(!$cookie){
            return redirect('/connect');
        }

        $conn = Ftp::instance(['host' => $cookie->host, 'port' => $cookie->port]);

        if(!$conn) {
            return redirect('/connect')->withErrors('Can\'t connect to ftp');
        }

        $file = request()->route('file');
        if (ftp_login($conn, $cookie->username, $cookie->password)) {
            return view('see')->with('text', Ftp::getFileToString($conn, $file));

        } else {
            return redirect('/connect')->withErrors('Credentials are invalid');
        }
    }
}
