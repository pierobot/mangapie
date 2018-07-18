<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserEditRequest;
use App\Image;
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

    public function patchImages(Request $request)
    {
        $queuedSuccessfully = ! empty(\Queue::push(new \App\Jobs\CleanupImageDisk()));

        $response = redirect()->action('AdminController@index');

        if (! $queuedSuccessfully)
            return $response->withErrors('Unable to queue the cleanup job.');

        session()->flash('success', 'Successfully queued the cleanup job. Check the queue worker(s) for progress.');

        return $response;
    }

    public function deleteImages(Request $request)
    {
        $dirs = Image::disk()->directories();
        $dirCount = count($dirs);
        $dirDeletedCount = 0;

        foreach ($dirs as $dir) {
            $deletedSuccessfully = Image::disk()->deleteDirectory($dir);
            if ($deletedSuccessfully)
                ++$dirDeletedCount;
        }

        $response = redirect()->action('AdminController@index');

        if ($dirDeletedCount !== $dirCount)
            return $response->withErrors('Unable to completely wipe the images disk.');

        session()->flash('success', 'Successfully wiped the images disk.');

        return $response;
    }
}
