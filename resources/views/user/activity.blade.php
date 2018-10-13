@section ('title')
    {{ $user->name }}&apos;s Activity :: Mangapie
@endsection

@extends ('user.layout')

@section ('profile-content')
    <div class="card mt-3 w-100">
        <div class="card-header">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ URL::action('UserController@activity', [$user]) }}">Activity</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ URL::action('UserController@comments', [$user]) }}">Comments</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <h5 class="card-title">Recent favorites</h5>
            @if (! empty($recentFavorites))
                <div class="row">
                    @foreach ($recentFavorites as $favorite)
                        <div class="col-12 col-sm-6">
                            <div class="media mb-3">
                                <a href="{{ URL::action('MangaController@index', [$favorite->manga]) }}">
                                    <img height="100" class="rounded mr-3" src="{{ URL::action('CoverController@smallDefault', [ $favorite->manga]) }}">
                                </a>

                                <div class="media-body">
                                    <a href="{{ URL::action('MangaController@index', [$favorite->manga]) }}">
                                        <h5>{{ $favorite->manga->name }}</h5>
                                    </a>

                                    <span class="text-muted">{{ $favorite->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                No recent favorites to show.
            @endif

            <hr>

            <h5 class="card-title">Recent reads</h5>
            @if (! empty($recentReads))
                <div class="row">
                    @foreach ($recentReads as $read)
                        <div class="col-12 col-sm-6">
                            <div class="media mb-3">
                                <a href="{{ URL::action('MangaController@index', [$read->manga]) }}">
                                    <img height="100" class="rounded mr-3" src="{{ URL::action('CoverController@smallDefault', [ $read->manga]) }}">
                                </a>

                                <div class="media-body">
                                    <a href="{{ URL::action('MangaController@index', [$read->manga]) }}">
                                        <h5>{{ $read->manga->name }}</h5>
                                    </a>

                                    <span class="text-muted">{{ $read->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
