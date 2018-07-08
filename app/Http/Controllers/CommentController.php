<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\CommentCreateRequest;

use App\Archive;
use App\Manga;
use App\Comment;

class CommentController extends Controller
{
    public function create(CommentCreateRequest $request)
    {
        $manga = Manga::findOrFail($request->get('manga_id'));

        $comment = $manga->comments()->create([
            'user_id' => \Auth::user()->getId(),
            'text' => $request->get('comment')
        ]);

        if (! empty($comment))
            session()->flash('success', 'Your comment was posted successfully.');
        else
            session()->flash('failure', 'There was an error posting your comment.');

        return redirect()->action('MangaController@comments', [$manga]);
    }
}
