<?php

namespace App\Http\Controllers;

use App\Http\Requests\Vote\VoteCreateRequest;
use App\Vote;

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

    public function destroy(Vote $vote)
    {
        $vote->forceDelete();

        return redirect()->back()->with('success', 'Your vote was successfully deleted.');
    }
}
