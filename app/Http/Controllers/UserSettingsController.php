<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserSettingsRequest;
use App\Http\Requests\AvatarUpdateRequest;
use App\Http\Requests\UserUpdateProfileRequest;
use Illuminate\Http\Request;

use App\Theme;
use App\User;

class UserSettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('settings.index')->with('user', $user);
    }

    public function account()
    {
        $user = auth()->user();

        return view('settings.account')->with('user', $user);
    }

    public function visuals()
    {
        $user = auth()->user();

        return view('settings.visuals')->with('user', $user);
    }

    public function profile()
    {
        $user = auth()->user();

        return view('settings.profile')->with('user', $user);
    }

    public function updateProfile(UserUpdateProfileRequest $request)
    {
        $user = auth()->user();

        $user->update([
            'about' => $request->get('about')
        ]);

        return view('settings.profile')->with('user', $user);
    }

    public function update(UserSettingsRequest $request)
    {
        $user = auth()->user();
        $action = $request->get('action');

        if ($action == 'password.update') {
            // make sure the old password matches the current one
            if (\Hash::check($request->get('old-password'), $user->getPassword()) == false) {
                return redirect()->action('UserSettingsController@index')->withErrors([
                    'password' => 'Old password does not match.'
                ]);
            }

            $user->setPassword(\Hash::make($request->get('new-password')));

            $request->session()->flash('success', 'Successfully updated password.');
        } else if ($action == 'reader.update') {
            $user->setLtr($request->get('ltr'));
            $user->save();

            $request->session()->flash('success', 'Successfully updated reading direction.');

        } elseif ($action == 'theme.update') {
            $theme = $request->get('theme');
            if (Theme::exists($theme) == false) {
                return redirect()->action('UserSettingsController@index')->withErrors([
                    'theme' => 'The selected theme is invalid.'
                ]);
            }

            $user->setTheme($theme);

            $request->session()->flash('success', 'Successfully updated theme.');
        }

        return redirect()->action('UserSettingsController@index');
    }
}
