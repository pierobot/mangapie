<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminCreateUserRequest;
use App\Http\Requests\AdminDeleteUserRequest;
use App\Http\Requests\AdminEditUserRequest;
use Illuminate\Http\Request;

use \App\Library;
use \App\LibraryPrivilege;
use \App\User;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (\Auth::user()->isAdmin() == false)
            return view('errors.403');

        $info = null;

        $admin_count = User::where('admin', '=', true)->get()->count();
        $user_count = User::all()->count();

        return view('admin.index', compact('info',
                                           'admin_count',
                                           'user_count'));
    }

    public function users()
    {
        if (\Auth::user()->isAdmin() == false)
            return view('errors.403');

        $libraries = Library::all();

        return view('admin.users', compact('libraries'));
    }

    public function libraries()
    {
        if (\Auth::user()->isAdmin() == false)
            return view('errors.403');

        $libraries = Library::all();

        return view('admin.libraries', compact('libraries'));
    }

    public function createUser(AdminCreateUserRequest $request)
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
            foreach (\Input::get('libraries') as $library_id) {
                if (Library::find($library_id) == null)
                    continue;

                LibraryPrivilege::create([
                    'user_id' => $user->getId(),
                    'library_id' => $library_id
                ]);
            }

            \Session::flash('success', 'User was successfully created!');
        }

        return \Redirect::action('AdminController@users');
    }

    public function editUser(AdminEditUserRequest $request)
    {
        $user = User::where('name', '=', \Input::get('old-name'))->first();
        if ($user != null) {
            $user->setName(\Input::get('new-name'));

            \Session::flash('success', 'User was successfully edited!');
        }

        return \Redirect::action('AdminController@users');
    }

    public function deleteUser(AdminDeleteUserRequest $request)
    {
        $user = User::where('name', '=', \Input::get('name'))->first();
        if ($user != null) {
            $user->forceDelete();

            \Session::flash('success', 'User was successfully deleted!');
        }

        return \Redirect::action('AdminController@users');
    }
}
