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

        $user->votes()->updateOrCreate([
            'manga_id' => $request->get('manga_id')
        ], [
            'rating' => $request->get('rating')
        ]);

        return response()->make();
    }

    public function delete(VoteDeleteRequest $request)
    {
        $user = $request->user();

        $user->votes()->find($request->get('vote_id'))->forceDelete();

        return redirect()->back()->with('success', 'Your vote was successfully deleted.');
    }
}
