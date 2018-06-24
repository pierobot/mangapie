@extends ('layout')

@section ('title')
    Information &middot; {{ $name }}
@endsection

@section ('custom_navbar_right')
@endsection

@section ('content')

    <h3 class="visible-xs text-center">
        <b>Information &middot; {{ $name }}</b>

        @maintainer
        <div class="row">
            <a href="{{ \URL::action('MangaEditController@index', [$id]) }}">
                <b>Edit</b>
            </a>
        </div>
        @endmaintainer
    </h3>

    <h2 class="visible-sm visible-md visible-lg visible-xl text-center">
        <b>Information &middot; {{ $name }}</b>

        @maintainer
        <div class="row">
            <a href="{{ \URL::action('MangaEditController@index', [$id]) }}">
                <b>Edit</b>
            </a>
        </div>
        @endmaintainer
    </h2>

    @include ('shared.success')
    @include ('shared.errors')

    <div class="row">
        <div class="col-xs-12 col-sm-4">
            {{ Html::image(URL::action('ThumbnailController@mediumDefault', [$id]), '', ['class' => 'information-img center-block']) }}
        </div>

        <div class="hidden-xs col-sm-8">
            <ul class="list-group">
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-xs-3 col-md-2">
                            <span class="glyphicon glyphicon-tags"></span>&nbsp;
                            <b>Genres</b>
                        </div>
                        <div class="col-xs-9 col-md-10">
                            @if ($genres != null)
                                <div class="row">
                                    @foreach ($genres as $genre)
                                        <div class="col-sm-4 col-md-3 col-lg-3">
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
                            @if ($assoc_names != null)
                                <div class="row">
                                    @foreach ($assoc_names as $assoc_name)
                                        <div class="col-sm-4 col-md-3 col-lg-3">
                                            {{ $assoc_name->getName() }}
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
                            @if ($authors != null)
                                <div class="row">
                                    @foreach ($authors as $author)
                                        <div class="col-sm-4 col-md-3 col-lg-3">
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
                            @if ($artists != null)
                                <div class="row">
                                    @foreach ($artists as $artist)
                                        <div class="col-sm-4 col-md-3 col-lg-3">
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
                            @if ($description != null)
                                {{ Html::decode($description) }}
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
                            @if ($year != null)
                                {{ $year }}
                            @else
                                Unable to find year.
                            @endif
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
                                <div class="col-xs-6 col-sm-4 col-md-3">
                                    {{ Form::open(['action' => 'FavoriteController@update']) }}

                                    {{ Form::hidden('id', $id) }}
                                    @if ($is_favorited == false)
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

                                    {{ Form::hidden('id', $id) }}

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
                            {{ $path }}
                        </div>
                    </div>
                </li>
                @endadmin
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="hidden-xs col-sm-12">
            <hr>
            <table class="table table-hover table-va-middle" style="word-break: break-all; ">
                <thead>
                <tr>
                    <th class="col-sm-6 col-md-7">
                        <a href="{{ \URL::action('MangaController@index', [$id, $sort == 'ascending' ? 'descending' : 'ascending']) }}">Filename&nbsp;
                            @if ($sort == 'ascending')
                                <span class="glyphicon glyphicon-triangle-bottom"></span>
                            @else
                                <span class="glyphicon glyphicon-triangle-top"></span>
                            @endif
                        </a>
                    </th>
                    <th class="col-sm-2 col-md-1">Status</th>
                    <th class="col-sm-2 col-md-2">Last Read</th>
                    <th class="col-sm-2 col-md-1 visible-md visible-lg">Size</th>
                </tr>
                </thead>

                <tbody>
                @if (empty($archives) === false)
                    @foreach ($archives as $archive)
                        <tr>
                            @php ($history = \App\ReaderHistory::where('user_id', \Auth::user()->getId())
                                                               ->where('manga_id', $id)
                                                               ->where('archive_name', $archive->getSize())
                                                               ->first())
                            @endphp

                            <td class="col-sm-6 col-md-7">
                                <a href="{{ URL::action('ReaderController@index', [$id, rawurlencode($archive->getName()), $history != null ? $history->getPage() : 1]) }}">
                                    <div>
                                        {{ $archive->getName() }}
                                    </div>
                                </a>
                            </td>
                            <td class="col-sm-2 col-md-1">
                                <div class="row">
                                    <div class="col-xs-12 span-label-ib">
                                        @if ($history != null)
                                            @if ($history->getPage() < $history->getPageCount())
                                                <span class="label label-warning" title="pg. {{ $history->getPage() }} of {{ $history->getPageCount() }}">Incomplete</span>
                                            @else
                                                <span class="label label-success" title="pg. {{ $history->getPage() }} of {{ $history->getPageCount() }}">Complete</span>
                                            @endif
                                        @else
                                            <span class="label label-default">Unread</span>
                                        @endif
                                    </div>
                                    <div class="col-xs-12 span-label-ib">
                                        @if (\Auth::user()->watchNotifications->where('manga_id', $id)
                                                                              ->where('archive_id', $archive->getId())
                                                                              ->first() != null)
                                            <span class="label label-success">&nbsp;New!&nbsp;</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="col-sm-1 col-md-2">
                                {{ $history != null ? $history->getLastUpdated()->diffForHumans() : "Never" }}
                            </td>
                            <td class="col-sm-1 col-md-1 visible-md visible-lg">
                                {{ $archive->getSize() }}
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>

        <hr>
    </div>

    <div class="row">
        <div class="col-xs-12 hidden-sm hidden-md hidden-lg hidden-xl">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#information-content" data-toggle="tab"><span class="glyphicon glyphicon-info-sign"></span>&nbsp;&nbsp;Information</a></li>
                <li><a href="#files-content" data-toggle="tab"><span class="glyphicon glyphicon-folder-open"></span>&nbsp;&nbsp;Files</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="information-content">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-xs-3">
                                    <b>Genres</b>
                                </div>
                                <div class="col-xs-9">
                                    @if ($genres != null)
                                        <div class="row">
                                            @foreach ($genres as $genre)
                                                <div class="col-xs-6">
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
                                <div class="col-xs-3">
                                    <b>Names</b>
                                </div>
                                <div class="col-xs-9">
                                    <div class="row">
                                        @if ($assoc_names != null)
                                            @foreach ($assoc_names as $assoc_name)
                                                <div class="col-xs-6">
                                                    {{ $assoc_name->getName() }}
                                                </div>
                                            @endforeach
                                        @else
                                            Unable to find associated names.
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-xs-3">
                                    <b>Authors</b>
                                </div>
                                <div class="col-xs-9">
                                    <div class="row">
                                        @if ($authors != null)
                                            @foreach ($authors as $author)
                                                <div class="col-xs-6">
                                                    <a href="{{ \URL::action('AuthorController@index', [$author->getName()]) }}">
                                                        {{ $author->getName() }}</label>
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            Unable to find authors.
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-xs-3">
                                    <b>Artists</b>
                                </div>
                                <div class="col-xs-9">
                                    <div class="row">
                                        @if ($artists != null)
                                            @foreach ($artists as $artist)
                                                <div class="col-xs-6">
                                                    <a href="{{ \URL::action('ArtistController@index', [$artist->getName()]) }}">
                                                        {{ $artist->getName() }}
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            Unable to find artists.
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-xs-3">
                                    <b>Summary</b>
                                </div>
                                <div class="col-xs-9">
                                    @if ($description != null)
                                        {{ Html::decode($description) }}
                                    @else
                                        Unable to find description.
                                    @endif
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-xs-3">
                                    <b>Year</b>
                                </div>
                                <div class="col-xs-9">
                                    @if ($year != null)
                                        {{ $year }}
                                    @else
                                        Unable to find year.
                                    @endif
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-xs-3">
                                    <b>Actions</b>
                                </div>
                                <div class="col-xs-9">
                                    {{ Form::open(['action' => 'FavoriteController@update']) }}

                                    {{ Form::hidden('id', $id) }}
                                    @if ($is_favorited == false)
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
                            </div>
                        </li>

                        @admin
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-xs-3">
                                    <b>Path</b>
                                </div>
                                <div class="col-xs-9">
                                    {{ $path }}
                                </div>
                            </div>
                        </li>
                        @endadmin

                    </ul>
                </div>

                <div class="tab-pane" id="files-content">
                    <table class="table table-hover table-condensed table-va-middle" style="word-break: break-all; ">
                        <thead>
                        <tr>
                            <th class="col-xs-6">
                                <a href="{{ \URL::action('MangaController@index', [$id, $sort == 'ascending' ? 'descending' : 'ascending']) }}">Filename&nbsp;
                                    @if ($sort == 'ascending')
                                        <span class="glyphicon glyphicon-triangle-top"></span>
                                    @else
                                        <span class="glyphicon glyphicon-triangle-bottom"></span>
                                    @endif
                                </a>
                            </th>
                            <th class="col-xs-2">Status</th>
                            <th class="col-sm-2">Read</th>
                            <th class="col-xs-2 visible-sm visible-md visible-lg">Size</th>
                        </tr>
                        </thead>

                        <tbody>
                        @if (empty($archives) === false)
                            @foreach ($archives as $archive)
                                <tr>
                                    @php ($history = \App\ReaderHistory::where('user_id', \Auth::user()->getId())
                                                                       ->where('manga_id', $id)
                                                                       ->where('archive_name', $archive->getName())
                                                                       ->first())
                                    @endphp

                                    <td class="col-xs-6">
                                        <a href="{{ URL::action('ReaderController@index', [$id, rawurlencode($archive->getName()), $history != null ? $history->getPage() : 1]) }}">
                                            {{ $archive->getName() }}
                                        </a>
                                    </td>
                                    <td class="col-xs-2">
                                        <div class="row">
                                            <div class="col-xs-12 span-label-ib">
                                                @if ($history != null)
                                                    @if ($history->getPage() < $history->getPageCount())
                                                        <span class="label label-warning" title="pg. {{ $history->getPage() }} of {{ $history->getPageCount() }}">Incomplete</span>
                                                    @else
                                                        <span class="label label-success" title="pg. {{ $history->getPage() }} of {{ $history->getPageCount() }}">Complete</span>
                                                    @endif
                                                @else
                                                    <span class="label label-default">Unread</span>
                                                @endif
                                            </div>
                                            <div class="col-xs-12 span-label-ib">
                                                @if (\Auth::user()->watchNotifications->where('manga_id', $id)
                                                                                      ->where('archive_id', $archive->getId())
                                                                                      ->first() != null)
                                                    <span class="label label-success">&nbsp;New!&nbsp;</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="col-xs-2">
                                        {{ $history != null ? $history->getLastUpdated()->diffForHumans() : "Never" }}
                                    </td>
                                    <td class="col-xs-2 visible-sm visible-md visible-lg">
                                        {{ $archive->getSize() }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
