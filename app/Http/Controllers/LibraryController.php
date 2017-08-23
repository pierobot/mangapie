<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use \App\Library;

class LibraryController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function update(Request $request) {
        
        // Ensure the form data is valid
        $this->validate($request, [
            'action' => 'required|string|max:10',
            'ids'=> 'required|array'
        ]);
        
        $library = Library::find(\Input::get('ids'))->first();
        if ($library != null)
            $library->scan();

        // Redirect to the admin index page
        return \Redirect::action('AdminController@index');
    }
}
