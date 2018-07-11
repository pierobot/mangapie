<ul class="list-group">
    <li class="list-group-item">
        <div class="row">
            <div class="col-xs-3 col-md-2">
                <span class="glyphicon glyphicon-tags"></span>&nbsp;
                <b>Genres</b>
            </div>
            <div class="col-xs-9 col-md-10">
                @if (! empty($manga->genreReferences))
                    <div class="row">
                        @foreach ($manga->genreReferences as $genreReference)
                            @php
                                $genre = $genreReference->genre;
                            @endphp
                            <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3">
                                <a href="{{ \URL::action('GenreController@index', [$genre->getName()]) }}">
                                    {{ $genre->getName() }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    Unable to find genres.
                @endif
            </div>
        </div>
    </li>

    <li class="list-group-item">
        <div class="row">
            <div class="col-xs-3 col-md-2">
                <span class="glyphicon glyphicon-list-alt"></span>&nbsp;
                <b>Names</b>
            </div>
            <div class="col-xs-9 col-md-10">
                @if (! empty($manga->associatedNameReferences))
                    <div class="row">
                        @foreach ($manga->associatedNameReferences as $associatedNameReference)
                            <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3">
                                {{ $associatedNameReference->associatedName->getName() }}
                            </div>
                        @endforeach
                    </div>
                @else
                    Unable to find associated names.
                @endif
            </div>
        </div>
    </li>

    <li class="list-group-item">
        <div class="row">
            <div class="col-xs-3 col-md-2">
                <span class="glyphicon glyphicon-user"></span>&nbsp;
                <b>Authors</b>
            </div>
            <div class="col-xs-9 col-md-10">
                @if (! empty($manga->authorReferences))
                    <div class="row">
                        @foreach ($manga->authorReferences as $authorReference)
                            @php
                                $author = $authorReference->author;
                            @endphp
                            <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3">
                                <a href="{{ \URL::action('AuthorController@index', [$author->getName()]) }}">
                                    {{ $author->getName() }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    Unable to find authors.
                @endif
            </div>
        </div>
    </li>

    <li class="list-group-item">
        <div class="row">
            <div class="col-xs-3 col-md-2">
                <span class="glyphicon glyphicon-user"></span>&nbsp;
                <b>Artists</b>
            </div>
            <div class="col-xs-9 col-md-10">
                @if (! empty($manga->artistReferences))
                    <div class="row">
                        @foreach ($manga->artistReferences as $artistReference)
                            @php
                                $artist = $artistReference->artist;
                            @endphp
                            <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3">
                                <a href="{{ \URL::action('ArtistController@index', [$artist->getName()]) }}">
                                    {{ $artist->getName() }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    Unable to find artists.
                @endif
            </div>
        </div>
    </li>

    <li class="list-group-item">
        <div class="row">
            <div class="col-xs-3 col-md-2">
                <span class="glyphicon glyphicon-info-sign"></span>&nbsp;
                <b>Summary</b>
            </div>
            <div class="col-xs-9 col-md-10">
                @if (! empty($manga->description))
                    {!! nl2br(e($manga->description)) !!}
                @else
                    Unable to find description.
                @endif
            </div>
        </div>
    </li>

    <li class="list-group-item">
        <div class="row">
            <div class="col-xs-3 col-md-2">
                <span class="glyphicon glyphicon-calendar"></span>&nbsp;
                <b>Year</b>
            </div>
            <div class="col-xs-9 col-md-10">
                @if (! empty($manga->year))
                    {{ $manga->year }}
                @else
                    Unable to find year.
                @endif
            </div>
        </div>
    </li>

    <li class="list-group-item">
        <div class="row">
            <div class="col-xs-3 col-md-2">
                <span class="glyphicon glyphicon-star"></span>
                <b>Rating</b>
            </div>
            <div  class="col-xs-9 col-md-10">
                <div class="row">
                    <div class="col-xs-6 col-sm-4">
                        <label>Average</label><Br>
                        @if ($manga->votes->count() > 0)
                            @php
                                $averageRating = \App\Rating::average($manga);
                                if ($averageRating !== false)
                                    $averageRating = round($averageRating);

                                $userVote = $user->votes->where('manga_id', $manga->id)->first();
                            @endphp

                            <p>
                                {{ $averageRating }}
                                @if (! empty($userVote))
                                    <span class="glyphicon glyphicon-ok text-success" title="You've voted!"></span>
                                @endif
                            </p>
                        @else
                            <p>N/A</p>
                        @endif
                    </div>
                    <div class="col-xs-6 col-sm-4">
                        <label title="Lower bound Wilson score">Wilson&nbsp;<a href="https://www.evanmiller.org/how-not-to-sort-by-average-rating.html">&lbrack;&quest;&rbrack;</a></label>
                        @if ($manga->votes->count() > 0)
                            @php
                                $rating = \App\Rating::get($manga);
                                if ($rating !== false)
                                    $rating = round($rating, 2);
                            @endphp
                            <p title="Lower bound Wilson score">{{$rating }}</p>
                        @else
                            <p title="Lower bound Wilson score">N/A</p>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <label>Vote</label><br>

                        <div class="row">
                            <div class="col-xs-12">
                                @if (empty($userVote))
                                    {{ Form::open(['action' => 'VoteController@put', 'method' => 'put', 'style' => 'display:inline-block;']) }}
                                    {{ Form::hidden('manga_id', $manga->id) }}
                                @else
                                    {{ Form::open(['action' => 'VoteController@patch', 'method' => 'patch', 'style' => 'display:inline-block;']) }}
                                    {{ Form::hidden('vote_id', $userVote->id) }}
                                @endif
                                {{ Form::selectRange('rating', 100, 1, ! empty($userVote) ? $userVote->rating : 70 ) }}
                                <button type="submit" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-ok"></span>&#8203;</button>
                                {{ Form::close() }}

                                @if (! empty($userVote))
                                    {{ Form::open(['action' => 'VoteController@delete', 'method' => 'delete', 'style' => 'display:inline-block']) }}
                                    {{ Form::hidden('vote_id', $userVote->id) }}
                                    <button type="submit" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-remove"></span>&#8203;</button>
                                    {{ Form::close() }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </li>

    <li class="list-group-item">
        <div class="row">
            <div class="col-xs-3 col-md-2">
                <span class="glyphicon glyphicon glyphicon-share"></span>
                <b>Actions</b>
            </div>
            <div class="col-xs-9 col-md-10">
                <div class="row">
                    @php
                        $isFavorited = ! empty($user->favorites->where('manga_id', $manga->id)->first());
                        $isWatching = ! empty($user->watchReferences->where('manga_id', $manga->id)->first());
                    @endphp
                    <div class="col-xs-6 col-sm-4 col-md-3">
                        {{ Form::open(['action' => 'FavoriteController@update']) }}

                        {{ Form::hidden('id', $manga->id) }}
                        @if ($isFavorited == false)
                            {{ Form::hidden('action', 'favorite') }}
                            <button class="btn btn-success" type="submit">
                                <span class="glyphicon glyphicon-heart"></span>&nbsp;Favorite
                            </button>
                        @else
                            {{ Form::hidden('action', 'unfavorite') }}
                            <button class="btn btn-danger" type="submit">
                                <span class="glyphicon glyphicon-remove"></span>&nbsp;Unfavorite
                            </button>
                        @endif

                        {{ Form::close() }}
                    </div>

                    <div class="col-xs-6 col-sm-4 col-md-3">
                        {{ Form::open(['action' => 'WatchController@update']) }}

                        {{ Form::hidden('id', $manga->id) }}

                        @if ($isWatching == false)
                            {{ Form::hidden('action', 'watch') }}
                            <button class="btn btn-success" type="submit" title="Get notifications for new archives">
                                <span class="glyphicon glyphicon-eye-open"></span>&nbsp;Watch
                            </button>
                        @else
                            {{ Form::hidden('action', 'unwatch') }}
                            <button class="btn btn-danger" type="submit" title="Do not get notifications for new archives">
                                <span class="glyphicon glyphicon-eye-close"></span>&nbsp;Unwatch
                            </button>
                        @endif

                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </li>

    @admin
    <li class="list-group-item">
        <div class="row">
            <div class="col-xs-3 col-md-2">
                <span class="glyphicon glyphicon-hdd"></span>&nbsp;
                <b>Path</b>
            </div>
            <div class="col-xs-9 col-md-10">
                {{ $manga->path }}
            </div>
        </div>
    </li>
    @endadmin
</ul>
