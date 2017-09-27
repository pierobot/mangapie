<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use \App\Library;

class LibraryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function update(Request $request)
    {
        // Ensure the form data is valid
        $validator = \Validator::make($request->all(), [
            'action' => 'required|string|max:10',
            'ids'=> 'required|array'
        ]);

        if ($validator->fails())
            return \Redirect::action('AdminController@libraries')->withErrors($validator, 'update');
        
        $action = \Input::get('action');
        $libraries = Library::where('id', '=', \Input::get('ids'))->get();

        if ($libraries == null) {

            \Session::flash('library-update-failure', 'Unable to find the selected libraries.');

            return \Redirect::action('AdminController@libraries');
        }

        // Rescan or delete the requested libraries
        foreach ($libraries as $library) {

            if ($action == 'Update') {

                $library->scan();
            } else if ($action == 'Delete') {

                // delete the library and the manga it holds (forceDelete overriden)
                $library->forceDelete();
            } else {

                \Session::flash('library-update-failure', 'Got invalid action.');

                \Redirect::action('AdminController@libraries');
            }
        }

        \Session::flash('library-update-success', 'The selected libraries were successfully ' . $action . 'd');

        return \Redirect::action('AdminController@libraries');
    }

    public function create(Request $request)
    {
        if (\Auth::user()->isAdmin() == false)
            return view('errors.403');

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'path' => 'required|string'
        ]);

        if ($validator->fails())
            return \Redirect::action('AdminController@libraries')->withErrors($validator, 'create');

        $name = \Input::get('name');
        $path = \Input::get('path');

        // ensure we have a valid path
        if (is_dir($request->path) == false) {

            \Session::flash('library-create-failure', "'" . $path . "'" . ' does not exist');

            return \Redirect::action('AdminController@libraries');
        }

        // ensure the name nor path match one that already axists
        $library = Library::where('name', '=', $name)->first();

        if ($library != null) {

            \Session::flash('library-create-failure', 'A library with that name already exists.');

            return \Redirect::action('AdminController@libraries');
        }

        // create the library
        $library = Library::create([
            'name' => $name,
            'path' => $path,
        ]);

        if ($library != null) {

            $library->scan();

            \Session::flash('library-create-success', 'Library was successfully created.');
        } else {
            \Session::flash('library-create-failure', 'Unable to create database row.');
        }

        return \Redirect::action('AdminController@libraries');
    }
}
