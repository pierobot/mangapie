<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserSettingsRequest;
use Illuminate\Http\Request;

use \App\Theme;

class UserSettingsController extends Controller
{
    public function index()
    {
        $user = \Auth::user();
        $current_theme = $user->getTheme();
        // get all the themes but without the paths
        $theme_collections = Theme::all(false);

        return view('user.settings', compact('current_theme', 'theme_collections'));
    }

    public function update(UserSettingsRequest $request)
    {
        $user = \Auth::user();
        $action = \Input::get('action');
        if ($action == 'password.update') {
            // make sure the old password matches the current one
            if (\Hash::check(\Input::get('old-password'), $user->getPassword()) == false) {
                return \Redirect::action('UserSettingsController@index')->withErrors([
                    'password' => 'Old password does not match.'
                ]);
            }

            $user->setPassword(\Hash::make(\Input::get('new-password')));

            \Session::flash('success', 'Successfully updated password.');
        } else if ($action == 'reader.update') {
            $user->setLtr(\Input::get('ltr'));
            $user->save();

            \Session::flash('success', 'Successfully updated reading direction.');

        } elseif ($action == 'theme.update') {
            $theme = \Input::get('theme');
            if (Theme::exists($theme) == false) {
                return \Redirect::action('UserSettingsController@index')->withErrors([
                    'theme' => 'The selected theme is invalid.'
                ]);
            }

            $user->setTheme($theme);

            \Session::flash('success', 'Successfully updated theme.');
        }

        return \Redirect::action('UserSettingsController@index');
    }
}
