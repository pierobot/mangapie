<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserSettings\PatchPasswordRequest;
use App\Http\Requests\UserSettings\PatchReaderDirectionRequest;
use App\Http\Requests\UserSettings\PutAboutRequest;
use App\Http\Requests\UserSettings\PutDisplayRequest;

class UserSettingsController extends Controller
{
    public function index()
    {
        $user = \Auth::user();

        return view('settings.index')->with('user', $user);
    }

    public function account()
    {
        $user = \Auth::user();

        return view('settings.account')->with('user', $user);
    }

    public function visuals()
    {
        $user = \Auth::user();

        return view('settings.visuals')->with('user', $user);
    }

    public function profile()
    {
        $user = \Auth::user();

        return view('settings.profile')->with('user', $user);
    }

    public function putAbout(PutAboutRequest $request)
    {
        $request->user()->update([
            'about' => $request->get('about')
        ]);

        session()->flash('success', 'About successfully updated.');

        return redirect()->back();
    }

    public function patchReaderDirection(PatchReaderDirectionRequest $request)
    {
        $request->user()->update([
            'read_direction' => $request->get('direction')
        ]);

        session()->flash('success', 'Reading direction successfully updated.');

        return redirect()->back();
    }

    public function patchPassword(PatchPasswordRequest $request)
    {
        $userPassword = $request->user()->password;
        $currentPassword = $request->get('current');
        $newPassword = $request->get('new');

        if (! \Hash::check($currentPassword, $userPassword)) {
            return redirect()->back()->withErrors([
                'password' => 'Current password given was incorrect.'
            ]);
        }

        $request->user()->update([
            'password' => \Hash::make($newPassword)
        ]);

        session()->flash('success', 'Successfully updated password.');

        return redirect()->back();
    }

    public function putDisplay(PutDisplayRequest $request)
    {
        $display = $request->get('display');

        $request->user()->update([
            'display' => $display
        ]);

        return response()->make();
    }
}
