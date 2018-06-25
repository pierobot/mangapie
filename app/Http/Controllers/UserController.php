<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserEditRequest;
use Illuminate\Http\Request;

use App\User;
use App\Library;
use App\LibraryPrivilege;

class UserController extends Controller
{
    public function index(User $user)
    {
        return view('user.index')->with('user', $user);
    }

    public function profile(User $user)
    {
        return view('user.profile')->with('user', $user);
    }

    public function activity(User $user)
    {
        $recentFavorites = $user->favorites->sortByDesc('created_at')->take(6)->load('manga');
        $recentReads = $user->readerHistory->sortByDesc('created_at')->take(6)->load('manga');

        return view('user.activity')
            ->with('user', $user)
            ->with('recentFavorites', $recentFavorites)
            ->with('recentReads', $recentReads);
    }

    public function create(UserCreateRequest $request)
    {
        // create the user
        $user = User::create([
            'name' => \Input::get('name'),
            'email' => \Input::get('email'),
            'password' => \Hash::make(\Input::get('password')),
            'admin' => \Input::get('admin') == null ? false : \Input::get('admin'),
            'maintainer' => \Input::get('maintainer') == null ? false : \Input::get('maintainer')
        ]);

        if ($user != null) {
            // create the privileges for each library
            $libraryIds = \Input::get('libraries');

            foreach ($libraryIds as $libraryId) {
                LibraryPrivilege::create([
                    'user_id' => $user->getId(),
                    'library_id' => $libraryId
                ]);
            }

            \Session::flash('success', 'User was successfully created!');
        }

        return \Redirect::action('AdminController@users');
    }

    public function edit(UserEditRequest $request)
    {
        $user = User::where('name', '=', \Input::get('old-name'))->first();
        if ($user != null) {
            $user->setName(\Input::get('new-name'));

            \Session::flash('success', 'User was successfully edited!');
        }

        return \Redirect::action('AdminController@users');
    }

    public function delete(UserDeleteRequest $request)
    {
        $user = User::where('name', '=', \Input::get('name'))->first();
        if ($user != null) {
            $user->forceDelete();

            \Session::flash('success', 'User was successfully deleted!');
        }

        return \Redirect::action('AdminController@users');
    }
}
