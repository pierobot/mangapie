@foreach ($manga->comments->sortByDesc('created_at') as $comment)
    <div class="row">
        <div class="col-xs-12 col-md-8 comment-header">
            <a href="{{ URL::action('UserController@profile', [$comment->user]) }}"><b>{{ $comment->user->getName() }}</b></a>
            {{ \Carbon\Carbon::createFromTimeString($comment->getCreatedAt())->diffForHumans() }}
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-md-8">
            <table class="table">
                <tbody>
                    <tr>
                        <div class="row">
                            <td class="col-xs-3 col-sm-2">
                                <div class="thumbnail">
                                    <img src="{{ URL::action('AvatarController@index', [$comment->user]) }}">
                                </div>
                            </td>

                            <td class="col-xs-9 col-sm-10">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <p>{!! e(nl2br($comment->getText())) !!}</p>
                                    </div>
                                </div>
                            </td>
                        </div>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endforeach

<div class="row">
    <div class="col-xs-12">
        <h4>Post a comment</h4>

        {{ Form::open(['action' => 'CommentController@put', 'method' => 'put']) }}
        {{ Form::hidden('manga_id', $manga->id) }}
        <div class="row">
            <div class="col-xs-12 col-md-5">
                {{ Form::textarea('comment', null, [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your comment here...',
                    'style' => 'height: 100px;']) }}
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-5">
                {{ Form::submit('Comment', ['class' => 'btn btn-success form-control']) }}
            </div>
        </div>

        {{ Form::close() }}
    </div>
</div>
<br>