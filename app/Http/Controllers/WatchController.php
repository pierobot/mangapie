<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\WatchRequest;

class WatchController extends Controller
{
    public function update(WatchRequest $request)
    {
        $id = \Request::get('id');
        $action = \Request::get('action');
        $user = \Auth::user();

        if ($action == 'watch') {
            $user->watchReferences()->create([
                'manga_id' => $id,
            ]);

            \Session::flash('success', 'You are now watching this manga.');
        } else {
            $user->watchReferences()->where('manga_id', $id)->forceDelete();

            \Session::flash('success', 'You are no longer watching this manga.');
        }

        return \Redirect::action('MangaController@index', [$id]);
    }
}
