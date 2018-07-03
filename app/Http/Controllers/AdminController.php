<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserEditRequest;
use App\LogParser;
use Illuminate\Http\Request;

use App\Avatar;
use App\Cover;
use App\Library;
use App\LibraryPrivilege;
use App\User;

class AdminController extends Controller
{
    public function index()
    {
        $info = null;

        $admin_count = User::where('admin', '=', true)->get()->count();
        $user_count = User::all()->count();
        $warnings = \LogParser::get('warning');
//      $errors_ = \LogParser::get('error'); // the underscore at the end is to avoid collisions with Laravel's $error variable

        return view('admin.index')
            ->with('info', $info)
            ->with('admin_count', $admin_count)
            ->with('user_count', $user_count)
            ->with('warnings', $warnings);
    }

    public function users()
    {
        $users = User::all();

        return view('admin.users')->with('users', $users);
    }

    public function libraries()
    {
        $libraries = Library::all();

        return view('admin.libraries')->with('libraries', $libraries);
    }
}
