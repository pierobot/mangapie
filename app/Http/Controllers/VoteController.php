<?php

namespace App\Http\Controllers;

use App\Http\Requests\Vote\VotePatchRequest;
use App\Http\Requests\Vote\VoteCreateRequest;
use App\Http\Requests\Vote\VoteDeleteRequest;

class VoteController extends Controller
{
    public function put(VoteCreateRequest $request)
    {
        $user = $request->user();

        $user->votes()->create([
            'manga_id' => $request->get('manga_id'),
            'rating' => $request->get('rating')
        ]);

        return redirect()->back()->with('success', 'Your vote was successfully created.');
    }

    public function patch(VotePatchRequest $request)
    {
        $user = $request->user();

        $user->votes()->update([
            'id' => $request->get('vote_id'),
            'rating' => $request->get('rating')
        ]);

        return redirect()->back()->with('success', 'Your vote was successfully updated.');
    }

    public function delete(VoteDeleteRequest $request)
    {
        $user = $request->user();

        $user->votes()->find($request->get('vote_id'))->forceDelete();

        return redirect()->back()->with('success', 'Your vote was successfully deleted.');
    }
}
