<?php

namespace App\Http\Controllers;

use App\User;
use App\Avatar;

use App\Http\Requests\AvatarUpdateRequest;

class AvatarController extends Controller
{
    public function index(User $user)
    {
        $avatar = new Avatar($user);

        return $avatar->response();
    }

    public function put(AvatarUpdateRequest $request)
    {
        $avatar = new Avatar($request->user());
        $putResult = $avatar->put($request->file('avatar'));

        if ($putResult !== false)
            $request->session()->flash('success', 'Your avatar has been updated.');
        else
            $request->session()->flash('failure', 'Unable to update your avatar. Contact an admin.');

        return redirect()->action('UserSettingsController@profile');
    }
}
