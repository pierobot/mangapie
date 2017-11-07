<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserEditRequest;
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

        return view('admin.index')->with('info', $info)
                                  ->with('admin_count', $admin_count)
                                  ->with('user_count', $user_count);
    }

    public function users()
    {
        if (\Auth::user()->isAdmin() == false)
            return view('errors.403');

        $libraries = Library::all();
        $users = User::all();

        return view('admin.users')->with('libraries', $libraries)
                                  ->with('users', $users);
    }

    public function libraries()
    {
        if (\Auth::user()->isAdmin() == false)
            return view('errors.403');

        $libraries = Library::all();

        return view('admin.libraries')->with('libraries', $libraries);
    }
}
