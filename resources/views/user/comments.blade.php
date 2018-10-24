@section ('title')
    {{ $user->name }}&apos;s Comments :: Mangapie
@endsection

@extends ('user.layout')

@section ('profile-content')
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <a class="nav-link" href="{{ URL::action('UserController@activity', [$user]) }}">Activity</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="{{ URL::action('UserController@comments', [$user]) }}">Comments</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            @if (! empty($comments))
                <div class="row">
                    @foreach ($comments as $comment)
                        <div class="col-12 col-sm-6">
                            <div class="media mb-3">
                                <a href="{{ URL::action('MangaController@comments', [$comment->manga]) }}">
                                    <img height="100" class="rounded mr-3" src="{{ URL::action('CoverController@smallDefault', [ $comment->manga]) }}">
                                </a>

                                <div class="media-body">
                                    <a href="{{ URL::action('MangaController@comments', [$comment->manga]) }}">
                                        <h5>{{ $comment->manga->name }}</h5>
                                    </a>

                                    <blockquote class="blockquote">
                                        <p>{{ $comment->text }}</p>
                                    </blockquote>

                                    <span class="text-muted">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                There are no comments to display.
            @endif
        </div>
    </div>
@endsection
