<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\PutDefaultLibrariesRequest;
use App\Http\Requests\Admin\PatchRegistrationRequest;

use App\Image;

use App\Library;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function dashboard()
    {
        return view('admin.dashboard.index');
    }

    public function statistics()
    {
        return view('admin.dashboard.statistics');
    }

    public function config()
    {
        return view('admin.dashboard.config');
    }

    public function users()
    {
        return view('admin.users.index');
    }

    public function createUsers()
    {
        return view('admin.users.create');
    }

    public function editUsers()
    {
        return view('admin.users.edit');
    }

    public function deleteUsers()
    {
        return view('admin.users.delete');
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

    public function patchRegistration(PatchRegistrationRequest $request)
    {
        if ($request->has('enabled')) {
            \Cache::rememberForever('app.registration.enabled', function () use ($request) {
                return true;
            });
        } else {
            \Cache::forget('app.registration.enabled');
        }

        return redirect()->back()->with('success', 'Registration has been updated.');
    }

    public function putDefaultLibraries(PutDefaultLibrariesRequest $request)
    {
        $libraryIds = $request->has('library_ids') ? $request->get('library_ids') : [];
        $defaultLibraries = [];

        \Cache::forget('app.registration.libraries');

        foreach ($libraryIds as $id) {
            $defaultLibraries[$id] = $id;
        }

        \Cache::rememberForever('app.registration.libraries', function () use ($defaultLibraries) {
            return $defaultLibraries;
        });

        return redirect()->back()->with('success', 'Default libraries have been updated.');
    }
}
