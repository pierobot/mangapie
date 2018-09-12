<div class="row">
    <div class="col-12">
        @foreach ($manga->comments->sortByDesc('created_at') as $comment)
            <div class="media mt-2 mb-2 border-bottom">
                <img class="d-flex align-self-center" width="64" src="{{ URL::action('AvatarController@index', [$comment->user]) }}">
                <div class="media-body ml-2">
                    <h5>{{ $comment->user->name }}</h5>
                    <p>{!! e(nl2br($comment->getText())) !!}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h4>Post a comment</h4>

        {{ Form::open(['action' => 'CommentController@put', 'method' => 'put']) }}
        {{ Form::hidden('manga_id', $manga->id) }}
        <div class="row">
            <div class="col col-md-5">
                {{ Form::textarea('comment', null, [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your comment here...',
                    'style' => 'height: 100px;']) }}
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-5">
                {{ Form::submit('Comment', ['class' => 'btn btn-success form-control']) }}
            </div>
        </div>

        {{ Form::close() }}
    </div>
</div>
<br>