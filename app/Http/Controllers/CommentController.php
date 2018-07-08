<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\CommentCreateRequest;
use App\Http\Requests\CommentDeleteRequest;

use App\Archive;
use App\Manga;
use App\Comment;

class CommentController extends Controller
{
    public function put(CommentCreateRequest $request)
    {
        $manga = Manga::findOrFail($request->get('manga_id'));

        $comment = $manga->comments()->create([
            'user_id' => \Auth::user()->getId(),
            'text' => $request->get('comment')
        ]);

        $redirect = redirect()->action('MangaController@comments', [$manga]);

        return ! empty($comment) ?
            $redirect->with('success', 'Your comment was posted successfully.') :
            $redirect->withErrors('There was an error posting your comment.');
    }

    public function delete(CommentDeleteRequest $request)
    {
        $comment = Comment::findOrFail($request->get('comment_id'))->with(['user', 'manga']);
        $manga = $comment->manga;

        $redirect = redirect()->action('MangaController@comments', [$manga]);

        if (\Auth::user() !== $comment->user)
            return $redirect->withErrors('You are not authorized to do that.');

        return $redirect->with('success', 'Successfully deleted the comment.');
    }
}
