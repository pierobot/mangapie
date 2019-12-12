<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

use App\Http\Requests\CommentCreateRequest;
use App\Http\Requests\CommentDeleteRequest;

use App\Archive;
use App\Manga;
use App\Comment;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Comment::class, 'comment');
    }

    /**
     * @param CommentCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CommentCreateRequest $request)
    {
        /** @var Manga $manga */
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

    /**
     * @param Comment $comment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Comment $comment)
    {
        $comment->forceDelete();

        return redirect()->back()
            ->with('success', 'Successfully deleted the comment.');
    }
}
