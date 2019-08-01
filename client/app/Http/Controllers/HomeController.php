<?php

namespace App\Http\Controllers;

use App\Ftp;
use App\Helper;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Index
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function index(){
        if(count(Helper::searchCookies('ftp_')) <= 0){
            return redirect('/connect');
        }

        $cookie = json_decode(Cookie::get('ftp_0'));
        $search = session('search');
        $path = session('path');

        if(!$cookie){
            return redirect('/connect');
        }

        $conn = Ftp::connect(['host' => $cookie->host, 'port' => $cookie->port]);

        if(!$conn){
            return redirect('/connect');
        }

        if (ftp_login($conn, $cookie->username, $cookie->password)) {
            $aliases = [];
            foreach(Helper::searchCookies('ftp_') as $c){
                if($c){
                    $decode = json_decode($c);
                    $aliases[] = $decode->alias;
                } else {
                    $aliases[] = 'Empty';
                }
            }

            ftp_pasv($conn, true);
            if($path){
                try {
                    ftp_chdir($conn, $path);
                } catch(\Exception $e){
                    ftp_chdir($conn, '/');
                    session(['path' => '/']);
                    return redirect('/browse')->with('path', session('path'));
                }
                $file_list = ftp_nlist($conn, $path);
            } else {
                $file_list = ftp_nlist($conn, '/');
            }
            return view('index')->with('file_list', $file_list)
                                      ->with('conn', $conn)
                                      ->with('search', $search)
                                      ->with('aliases', $aliases)
                                      ->with('currentAlias', $aliases[0]);
        } else {
            return redirect('/connect')->withErrors('Credentials are invalid');
        }
    }
}
