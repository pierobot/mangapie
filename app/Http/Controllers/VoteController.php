<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoteChangeRequest;
use App\Http\Requests\VoteCreateRequest;
use App\Http\Requests\VoteDeleteRequest;

class VoteController extends Controller
{
    public function put(VoteCreateRequest $request)
    {
        $user = \Auth::user();

        $vote = $user->votes()->create([
            'manga_id' => $request->get('manga_id'),
            'rating' => $request->get('rating')
        ]);

        return ! empty($vote) ?
            back()->with('success', 'Your vote was successfully created.') :
            back()->withErrors('There was an error creating your vote.');
    }

    public function patch(VoteChangeRequest $request)
    {
        $user = \Auth::user();

        $vote = $user->votes()->update([
            'id' => $request->get('vote_id'),
            'rating' => $request->get('rating')
        ]);

        return ! empty($vote) ?
            back()->with('success', 'Your vote was successfully updated.') :
            back()->withErrors('There was an error updating your vote.');
    }

    public function delete(VoteDeleteRequest $request)
    {
        $user = \Auth::user();

        $user->votes()->find($request->get('vote_id'))->forceDelete();

        return back()->with('success', 'Your vote was successfully deleted.');
    }
}
