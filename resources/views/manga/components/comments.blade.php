<div class="row">
    <div class="col-xs-12">
        <table class="table table-va-middle">
            <thead>
            </thead>
            <tbody>
            @foreach ($manga->comments->sortByDesc('created_at') as $comment)
                <tr>
                    <td class="col-xs-2 col-sm-1">
                        <div class="thumbnail">
                            <img src="{{ URL::action('AvatarController@index', [$comment->user]) }}">
                        </div>
                    </td>

                    <td class="col-xs-10 col-sm-11">
                        <div class="row">
                            <div class="col-xs-12">
                                <a href="{{ URL::action('UserController@profile', [$comment->user]) }}"><b>{{ $comment->user->getName() }}</b></a>
                                {{ \Carbon\Carbon::createFromTimeString($comment->getCreatedAt())->diffForHumans() }}
                            </div>
                            <div class="col-xs-12">
                                <p>{!! e(nl2br($comment->getText())) !!}</p>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <h4>Post a comment</h4>

        {{ Form::open(['action' => 'CommentController@create', 'method' => 'put']) }}
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