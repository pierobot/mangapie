<?php

namespace App\Http\Controllers;

use App\Http\Requests\Library\LibraryCreateRequest;
use App\Http\Requests\Library\LibraryUpdateRequest;

use \App\Library;

class LibraryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Library::class, 'library');
    }

    public function create(LibraryCreateRequest $request)
    {
        $name = $request->input('name');
        $path = $request->input('path');

        // ensure we have a valid path
        if (is_dir($path) == false) {
            return redirect()->back()->withErrors([
                'library' => "'" . $path . "'" . ' does not exist'
            ]);
        }

        // create the library
        $library = Library::create([
            'name' => $name,
            'path' => $path,
        ]);

        if ($library == null) {
            return redirect()->back()->withErrors([
                'library' => 'Unable to create library.'
            ]);
        }

        session()->flash('success', 'Library was successfully created.');

        return redirect()->back();
    }

    public function update(Library $library, LibraryUpdateRequest $request)
    {
        $action = $request->get('action');
        if ($action === 'rename') {
            $library->update([
                'name' => $request->get('name')
            ]);

            session()->flash('success', 'Library was successfully renamed.');
        } else {
            $job = new \App\Jobs\ScanLibrary($library);

            $this->dispatch($job);

            session()->flash('success', 'The library is being refreshed.');
        }

        return redirect()->back();
    }

    public function destroy(Library $library)
    {
        $library->forceDelete();

        session()->flash('success', 'Library was successfully deleted.');

        return redirect()->back();
    }
}
