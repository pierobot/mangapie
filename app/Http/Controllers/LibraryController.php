<?php

namespace App\Http\Controllers;

use App\Http\Requests\LibraryCreateRequest;
use App\Http\Requests\LibraryDeleteRequest;
use App\Http\Requests\LibraryUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use \App\Library;
use \App\Manga;

class LibraryController extends Controller
{
    public function create(LibraryCreateRequest $request)
    {
        $name = \Input::get('name');
        $path = \Input::get('path');

        // ensure we have a valid path
        if (is_dir($path) == false) {
            return \Redirect::action('AdminController@libraries')->withErrors([
                'library' => "'" . $path . "'" . ' does not exist'
            ]);
        }

        // create the library
        $library = Library::create([
            'name' => $name,
            'path' => $path,
        ]);

        if ($library == null) {
            return \Redirect::action('AdminController@libraries')->withErrors([
                'library' => 'Unable to create library.'
            ]);
        }

        // scan and populate the library
        $library->scan();

        \Session::flash('success', 'Library was successfully created.');

        return \Redirect::action('AdminController@libraries');
    }

    public function update(LibraryUpdateRequest $request)
    {
        $id = \Input::get('id');

        $library = Library::findOrFail($id);
        $library->scan();

        \Session::flash('success', 'The selected libraries were successfully updated.');

        return \Redirect::action('AdminController@libraries');
    }

    public function delete(LibraryDeleteRequest $request)
    {
        $id = \Input::get('id');

        $library = Library::findOrFail($id);
        $library->forceDelete();

        \Session::flash('success', 'The selected libraries were successfully deleted.');

        return \Redirect::action('AdminController@libraries');
    }
}
