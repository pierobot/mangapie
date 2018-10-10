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
        return view('admin.index');
    }

    public function users()
    {
        return view('admin.users');
    }

    public function libraries()
    {
        return view('admin.libraries.index');
    }

    public function logs()
    {
        return view('admin.logs.index');
    }

    public function logWarnings()
    {
        return view('admin.logs.warnings');
    }

    public function logErrors()
    {
        return view('admin.logs.errors');
    }

    public function createLibraries()
    {
        return view('admin.libraries.create');
    }

    public function modifyLibraries()
    {
        return view('admin.libraries.modify');
    }

    public function deleteLibraries()
    {
        return view('admin.libraries.delete');
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
