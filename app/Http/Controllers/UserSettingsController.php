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
            'theme' => 'required|regex:/\w+\/\w+/'
        ]);

        if ($validator->fails() === true) {
            return \Redirect::action('UserSettingsController@index')->withErrors($validator, 'update');
        }

        $theme = \Input::get('theme');
        $user = \Auth::user();
        $user->setTheme($theme);

        return \Redirect::action('UserSettingsController@index');
    }
}
