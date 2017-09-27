<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use PHPHtmlParser\Dom;

use \App\Genre;

class GenreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id)
    {
        $genre = Genre::find($id);

        return $genre;
    }
}
