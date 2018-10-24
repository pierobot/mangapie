<?php

namespace App\Http\Controllers;

use App\Http\Requests\Watch\WatchCreateRequest;
use App\Http\Requests\Watch\WatchDeleteRequest;
use App\WatchReference;

class WatchController extends Controller
{
    public function create(WatchCreateRequest $request)
    {
        $user = $request->user();

        $user->watchReferences()->create([
            'manga_id' => $request->get('manga_id'),
        ]);

        session()->flash('success', 'You are now watching this manga.');

        return redirect()->back();
    }

    public function delete(WatchDeleteRequest $request)
    {
        WatchReference::find($request->get('watch_reference_id'))->forceDelete();

        session()->flash('success', 'You are no longer watching this manga.');

        return redirect()->back();
    }
}
