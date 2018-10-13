<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserDeleteRequest;
use App\Http\Requests\User\UserEditRequest;
use Illuminate\Http\Request;

use App\User;
use App\LibraryPrivilege;

class UserController extends Controller
{
    public function index(User $user)
    {
        $recentFavorites = $user->favorites->sortByDesc('updated_at')->take(4)->load('manga');
        $recentReads = $user->readerHistory->sortByDesc('updated_at')->unique('manga_id')->take(4)->load('manga');

        return view('user.activity')
            ->with('user', $user)
            ->with('recentFavorites', $recentFavorites)
            ->with('recentReads', $recentReads);
    }

    public function comments(User $user)
    {
        $comments = $user->comments->sortByDesc('created_at')->take(10)->load('manga');

        return view('user.comments')
            ->with('user', $user)
            ->with('comments', $comments);
    }

    public function activity(User $user)
    {
        $recentFavorites = $user->favorites->sortByDesc('updated_at')->take(4)->load('manga');
        $recentReads = $user->readerHistory->sortByDesc('updated_at')->unique('manga_id')->take(4)->load('manga');

        return view('user.activity')
            ->with('user', $user)
            ->with('recentFavorites', $recentFavorites)
            ->with('recentReads', $recentReads);
    }

    public function avatar(User $user)
    {
        $filePath = storage_path('app/public/avatars') . DIRECTORY_SEPARATOR . $user->getId();
        $accelPath = '/avatars' . DIRECTORY_SEPARATOR . $user->getId();

        return response()->make('', 200, [
            'Content-Type' => \Image::make($filePath)->mime,
            'X-Accel-Redirect' => $accelPath,
            'X-Accel-Charset' => 'utf-8'
        ]);
    }

    public function create(UserCreateRequest $request)
    {
        // create the user
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => \Hash::make($request->get('password')),
            'admin' => $request->get('admin') == null ? false : $request->get('admin'),
            'maintainer' => $request->get('maintainer') == null ? false : $request->get('maintainer')
        ]);

        if ($user != null) {
            // create the privileges for each library
            $libraryIds = $request->get('libraries');

            foreach ($libraryIds as $libraryId) {
                LibraryPrivilege::create([
                    'user_id' => $user->getId(),
                    'library_id' => $libraryId
                ]);
            }

            $request->session()->flash('success', 'User was successfully created!');
        }

        return redirect()->back();
    }

    public function edit(UserEditRequest $request)
    {
        $user = User::where('name', $request->get('name'))->first();
        $user->update([
            'name' => $request->get('new-name')
        ]);

        $request->session()->flash('success', 'User was successfully edited!');

        return redirect()->back();
    }

    public function delete(UserDeleteRequest $request)
    {
        $user = User::where('name', $request->get('name'))->first();
        $user->forceDelete();

        $request->session()->flash('success', 'User was successfully deleted!');

        return redirect()->back();
    }
}
