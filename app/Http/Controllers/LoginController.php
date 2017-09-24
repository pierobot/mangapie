<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $lockoutTime = 60 * 60 * 3;
    protected $maxLoginAttempts = 5;

    public function index()
    {
        // if the user is already logged in then redirect to index
        if (\Auth::id() != null)
            return \Redirect::action('MangaController@index');

        return view('login');
    }

    public function login(Request $request)
    {
        if ($this->hasTooManyLoginAttempts($request)) {

            $this->fireLockoutEvent($request);
            \Session::flash('login-failure', 'You have been locked out. Try again in 3 hours.');

            return view('login');
        }

        $username = \Input::get('username');
        $password = \Input::get('password');

        if (\Auth::attempt(['name' => $username, 'password' => $password]) == false) {

            $this->incrementLoginAttempts($request);
            \Session::flash('login-failure', 'You have given invalid credentials. Please try again.');

            return view('login');
        }

        $this->clearLoginAttempts($request);

        return \Redirect::action('MangaController@index');
    }

    public function logout() {
        \Auth::logout();

        return \Redirect::action('LoginController@login');
    }
}
