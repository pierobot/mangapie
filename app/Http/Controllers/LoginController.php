<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    //
    public function index() {
        // if the user is already logged in then redirect to index
        if (\Auth::id() != null)
            return \Redirect::action('MangaController@index');

        return view('login');
    }

    public function login() {
        $username = \Input::get('username');
        $password = \Input::get('password');

        if (\Auth::attempt(['name' => $username, 'password' => $password]) == false) {

            \Session::flash('login-failure', 'You have given invalid credentials. Please try again.');

            return view('login');
        }

        return \Redirect::action('MangaController@index');
    }

    public function logout() {
        \Auth::logout();

        return \Redirect::action('LoginController@login');
    }
}
