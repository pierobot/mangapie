<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \App\Manga;
use \App\MangaInformation;

class MangaEditController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id)
    {
        if (\Auth::user()->isAdmin() == false && \Auth::user()->isMaintainer() == false) {
            return view('error.403');
        }

        $manga = Manga::find($id);
        if ($manga == null) {
            return view('error.404');
        }

        $name = $manga->getName();
        $info = MangaInformation::find($id);
        if ($info != null) {
            $mu_id = $info->getMangaUpdatesId();
        }

        $archives = $manga->getArchives('ascending');

        return view('manga.edit')->with('id', $id)
                                 ->with('mu_id', $mu_id)
                                 ->with('name', $name)
                                 ->with('archives', $archives);
    }
}
