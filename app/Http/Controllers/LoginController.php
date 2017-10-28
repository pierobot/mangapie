<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
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
        if (\Auth::check())
            return \Redirect::action('MangaController@index');

        return view('login');
    }

    public function login(LoginRequest $request)
    {
        if (\Auth::check())
            return \Redirect::action('MangaController@index');

        if ($this->hasTooManyLoginAttempts($request)) {

            $this->fireLockoutEvent($request);

            return \Redirect::action('LoginController@index')
                            ->withErrors(['lockout' => 'You have been locked out. Try again in 3 hours.']);
        }

        $username = \Input::get('username');
        $password = \Input::get('password');

        if (\Auth::attempt(['name' => $username, 'password' => $password]) == false) {

            $this->incrementLoginAttempts($request);

            return \Redirect::action('LoginController@index')
                            ->withErrors(['login' => 'You have given invalid credentials. Please try again.']);
        }

        $this->clearLoginAttempts($request);

        return \Redirect::intended();
    }

    public function logout() {
        \Auth::logout();

        return \Redirect::action('LoginController@login');
    }
}
