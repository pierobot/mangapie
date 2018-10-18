<?php

namespace App\Http\Controllers;

use App\Http\Requests\Watch\WatchCreateRequest;
use App\Http\Requests\Watch\WatchDeleteRequest;

class WatchController extends Controller
{
    public function create(WatchCreateRequest $request)
    {
        $user = $request->user();

        $user->watchReferences()->create([
            'manga_id' => $request->get('manga_id'),
        ]);

        \Session::flash('success', 'You are now watching this manga.');

        return redirect()->back();
    }

    public function delete(WatchDeleteRequest $request)
    {
        $user = $request->user();

        $user->watchReferences()->where('manga_id', $request->get('manga_id'))->forceDelete();

        \Session::flash('success', 'You are no longer watching this manga.');

        return redirect()->back();
    }
}
