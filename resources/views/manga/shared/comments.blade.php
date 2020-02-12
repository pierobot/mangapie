<div class="row">
    <div class="col-12">
        @foreach ($manga->comments->sortByDesc('created_at') as $comment)
            <div class="media mb-3 border-bottom">
                <img width="64" src="{{ URL::action('AvatarController@index', [$comment->user]) }}">
                <div class="media-body ml-2">
                    <h5>{{ $comment->user->name }}</h5>
                    <p class="mb-2">{!! e(nl2br($comment->getText())) !!}</p>
                    <span class="text-muted">{{ $comment->created_at->diffForHumans() }}</span>
                    {{ Form::open(['action' => ['CommentController@destroy', $comment], 'method' => 'delete']) }}
                    <button class="btn btn-danger" type="submit"><span class="fa fa-times"></span>&nbsp;Delete</button>
                    {{ Form::close() }}
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h4>Post a comment</h4>

        {{ Form::open(['action' => 'CommentController@create']) }}
        {{ Form::hidden('manga_id', $manga->id) }}

        <div class="form-group">
            {{ Form::textarea('comment', null, [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your comment here...',
                    'style' => 'height: 100px;']) }}
        </div>

        <div class="form-group">
            <div class="form-row">
                <div class="col-12 col-md-3">
                    {{ Form::submit('Comment', ['class' => 'btn btn-primary form-control']) }}
                </div>
            </div>
        </div>

        {{ Form::close() }}
    </div>
</div>
<br>