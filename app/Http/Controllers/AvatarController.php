<?php

namespace App\Http\Controllers;

use App\User;
use App\Avatar;

use App\Http\Requests\AvatarUpdateRequest;

class AvatarController extends Controller
{
    public function index(User $user)
    {
        return Avatar::exists($user) ? Avatar::response($user) : Avatar::defaultResponse();
    }

    public function put(AvatarUpdateRequest $request)
    {
        $storeResult = Avatar::save($request->file('avatar'), \Auth::user());

        if ($storeResult !== false)
            $request->session()->flash('success', 'Your avatar has been updated.');
        else
            $request->session()->flash('failure', 'Unable to update your avatar. Contact an admin.');

        return redirect()->action('UserSettingsController@profile');
    }
}
