<?php

namespace App\Http\Controllers;

use App\Ftp;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class FtpController extends Controller
{
    /**
     * Show ftp connexion form.
     * Redirect to ftp root if already connected using cookie
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function connect_form(){
        if(Cookie::get('ftp') && Cookie::get('ftp') !== ''){
            return redirect('/');
        }
        return view('ftp-login.index');
    }

    /**
     * Ftp connexion action and redirect to ftp root.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function connect(){
        // Forget session path to prevent redirecting on invalid path
        session()->forget('path');

        $request_values = ['host' => request('host'),
                           'port' => request('port'),
                           'username' => request('username'),
                           'password' => request('password')];
        $conn = Ftp::connect(['host' => $request_values['host'], 'port' => $request_values['port']]);

        if(!$conn) {
            return redirect('/connect')->withErrors('Can\'t connect to ftp');
        }

        if (ftp_login($conn, request('username'), request('password'))) {
            Ftp::disconnect($conn);
            // Set cookie for 120min and redirect to ftp root
            return redirect('/')->withCookie(Cookie::make('ftp', json_encode($request_values), 120));
        } else {
            return redirect('/connect')->withErrors('Credentials are invalid');
        }
    }

    /**
     * Disconnect action (remove cookie) and redirect to connect_form.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function disconnect(){
        session()->forget('path');
        return redirect('/connect')->withCookie(Cookie::make('ftp', '', -1));
    }

    /**
     * Download ftp file into client's computer.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function download(){
        $cookie = json_decode(Cookie::get('ftp'));
        $path = Session::get('path') ?: '/';

        if(!$cookie){
            return redirect('/connect');
        }

        $conn = Ftp::connect(['host' => $cookie->host, 'port' => $cookie->port]);

        if(!$conn) {
            return redirect('/connect')->withErrors('Can\'t connect to ftp');
        }

        $file = request()->route('file');
        if (ftp_login($conn, $cookie->username, $cookie->password)) {
            if(!ends_with($path, '/')){
                $path = $path.'/';
            }

            ftp_pasv($conn, true);
            $size = ftp_size($conn, $path.$file);

            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . basename($file));
            header("Content-Length: $size");

            ftp_get($conn, 'php://output', $path.$file, FTP_BINARY);
            Ftp::disconnect($conn);
        } else {
            Ftp::disconnect($conn);
            return redirect('/connect')->withErrors('Credentials are invalid');
        }
    }

    /**
     * Delete file from ftp.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(){
        $cookie = json_decode(Cookie::get('ftp'));

        if(!$cookie){
            return redirect('/connect');
        }

        $conn = Ftp::connect(['host' => $cookie->host, 'port' => $cookie->port]);

        if(!$conn) {
            return redirect('/connect')->withErrors('Can\'t connect to ftp');
        }

        $file = request()->route('file');
        if (ftp_login($conn, $cookie->username, $cookie->password)) {
            ftp_pasv($conn, true);
            ftp_delete($conn, $file);

            Ftp::disconnect($conn);
            return redirect('/');
        } else {
            Ftp::disconnect($conn);
            return redirect('/connect')->withErrors('Credentials are invalid');
        }
    }

    /**
     * See content of file without downloading it to client.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function see(){
        $cookie = json_decode(Cookie::get('ftp'));

        if(!$cookie){
            return redirect('/connect');
        }

        $conn = Ftp::connect(['host' => $cookie->host, 'port' => $cookie->port]);

        if(!$conn) {
            return redirect('/connect')->withErrors('Can\'t connect to ftp');
        }

        $file = request()->route('file');
        if (ftp_login($conn, $cookie->username, $cookie->password)) {
            $text = Ftp::getFileToString($conn, $file);
            Ftp::disconnect($conn);
            return view('see')->with('text', $text);
        } else {
            Ftp::disconnect($conn);
            return redirect('/connect')->withErrors('Credentials are invalid');
        }
    }

    /**
     * Upload file to ftp.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function upload(){
        $files = request()->file('uploads');
        $path = Session::get('path') ?: '/';
        $cookie = json_decode(Cookie::get('ftp'));

        if(!$cookie){
            return redirect('/connect');
        }

        $conn = Ftp::connect(['host' => $cookie->host, 'port' => $cookie->port]);

        if(!$conn) {
            return redirect('/connect')->withErrors('Can\'t connect to ftp');
        }

        if (ftp_login($conn, $cookie->username, $cookie->password)) {
            $tempDestination = 'uploads/';
            if(!ends_with($path, '/')){
                $path = $path.'/';
            }
            foreach ($files as $file) {
                $file->move($tempDestination, $file->getClientOriginalName());
                ftp_pasv($conn, true);
                ftp_put($conn, $path.$file->getClientOriginalName(), public_path().'/uploads/'.$file->getClientOriginalName(), FTP_BINARY, FTP_AUTORESUME);
            }

            Ftp::disconnect($conn);
            return redirect('/');
        } else {
            Ftp::disconnect($conn);
            return redirect('/connect')->withErrors('Credentials are invalid');
        }
    }

    /**
     * Change current location to given path.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function browse(){
        $to = request()->path;
        $cookie = json_decode(Cookie::get('ftp'));

        if(!$cookie){
            return redirect('/connect');
        }

        $conn = Ftp::connect(['host' => $cookie->host, 'port' => $cookie->port]);

        if(!$conn) {
            return redirect('/connect')->withErrors('Can\'t connect to ftp');
        }

        if (ftp_login($conn, $cookie->username, $cookie->password)) {
            session(['path' => $to]);

            Ftp::disconnect($conn);
            return redirect()->route('listing');
        } else {
            Ftp::disconnect($conn);
            return redirect('/connect')->withErrors('Credentials are invalid');
        }
    }

    /**
     * Create a directory in current path.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function createDir(){
        $dir = request()->dir;
        $path = session('path') ?: '/';
        $cookie = json_decode(Cookie::get('ftp'));

        if(!$cookie){
            return redirect('/connect');
        }

        $conn = Ftp::connect(['host' => $cookie->host, 'port' => $cookie->port]);

        if(!$conn) {
            return redirect('/connect')->withErrors('Can\'t connect to ftp');
        }

        if (ftp_login($conn, $cookie->username, $cookie->password)) {
            // TODO: figure the error out...
            try {
                ftp_chdir($conn, $path);
                ftp_mkdir($conn, $dir);
            } catch (\Exception $e){

            }
            Ftp::disconnect($conn);
            return redirect('/');
        } else {
            Ftp::disconnect($conn);
            return redirect('/connect')->withErrors('Credentials are invalid');
        }
    }

    /**
     * Search for a file recursively on the ftp server.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function search(){
        $file = request()->file;
        $path = session('path') ?: '/';
        $cookie = json_decode(Cookie::get('ftp'));

        if(!$cookie){
            return redirect('/connect');
        }

        $conn = Ftp::connect(['host' => $cookie->host, 'port' => $cookie->port]);

        if(!$conn) {
            return redirect('/connect')->withErrors('Can\'t connect to ftp');
        }

        if (ftp_login($conn, $cookie->username, $cookie->password)) {
            ftp_pasv($conn, true);
            $search = Ftp::searchFile($conn, $file);
            if($search) {

                Ftp::disconnect($conn);
                return redirect('/')->with('path', $search)->with('search', $file);
            } else {

                Ftp::disconnect($conn);
                return redirect('/')->with('path', $path)->withErrors('Could not found file');
            }
        } else {
            Ftp::disconnect($conn);
            return redirect('/connect')->withErrors('Credentials are invalid');
        }
    }

}
