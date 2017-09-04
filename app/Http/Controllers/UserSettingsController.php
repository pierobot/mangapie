<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \App\Theme;

class UserSettingsController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = \Auth::user();
        $current_theme = $user->getTheme();
        // get all the themes but without the paths
        $theme_collections = Theme::all(false);

        return view('user.settings', compact('current_theme', 'theme_collections'));
    }

    public function update(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'old-password' => 'string',
            'new-password' => 'string',
            'confirm-password' => 'same:new-password',
            'theme' => 'regex:/\w+\/\w+/',
            'action' => 'required|string'
        ]);

        if ($validator->fails() === true) {
            return \Redirect::action('UserSettingsController@index')->withErrors($validator, 'update');
        }

        $user = \Auth::user();
        $action = \Input::get('action');
        if ($action == 'updatepassword') {

            // make sure the old password matches the current one
            if (\Hash::check(\Input::get('old-password'), $user->getPassword())) {

                $user->setPassword(\Hash::make(\Input::get('new-password')));

                \Session::flash('edit-alert-success', 'Successfully updated password.');
            }
        } elseif ($action == 'updatetheme') {

            $theme = \Input::get('theme');

            $user->setTheme($theme);

            \Session::flash('theme-alert-success', 'Successfully updated theme.');
        }

        return \Redirect::action('UserSettingsController@index');
    }
}
