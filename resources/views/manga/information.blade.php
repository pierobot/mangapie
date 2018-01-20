@extends ('layout')

@section ('title')
    Information &middot; {{ $name }}
@endsection

@section ('custom_navbar_right')
    @if (\Auth::user()->isAdmin() || \Auth::user()->isMaintainer())
        <li class="clickable navbar-link"><a href="{{ URL::action('MangaEditController@index', [$id]) }}"><span class="glyphicon glyphicon-pencil"></span> Edit</a></li>
    @endif
@endsection

@section ('content')

    <h3 class="visible-xs text-center">
        <b>Information &middot; {{ $name }}</b>
    </h3>

    <h2 class="visible-sm visible-md visible-lg visible-xl text-center">
        <b>Information &middot; {{ $name }}</b>
    </h2>

    <div class="row text-center">
        {{ Form::open(['action' => 'FavoriteController@update']) }}

        {{ Form::hidden('id', $id) }}
        @if ($is_favorited == false)
            {{ Form::hidden('action', 'favorite') }}
            {{ Form::button('', ['class' => 'btn btn-success glyphicon glyphicon-heart',
                                 'type' => 'submit']) }}
        @else
            {{ Form::hidden('action', 'unfavorite') }}
            {{ Form::button('', ['class' => 'btn btn-danger glyphicon glyphicon-heart',
                                 'type' => 'submit']) }}
        @endif

        {{ Form::close() }}
    </div>

    @include ('shared.success')
    @include ('shared.errors')

    {{ Html::image(URL::action('ThumbnailController@mediumDefault', [$id]), '', ['class' => 'information-img center-block']) }}

    <hr>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#information-content" data-toggle="tab"><span class="glyphicon glyphicon-info-sign"></span>&nbsp;&nbsp;Information</a></li>
        <li><a href="#files-content" data-toggle="tab"><span class="glyphicon glyphicon-folder-open"></span>&nbsp;&nbsp;Files</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="information-content">
            <ul class="list-group">
                <li class="list-group-item">
                    <h4><span class="glyphicon glyphicon-info-sign"></span>&nbsp;&nbsp;<b>Description</b></h4>
                    @if ($description != null)
                        {{ Html::decode($description) }}
                    @else
                        Unable to find description.
                </li>
                @endif

                <li class="list-group-item">
                    <h4><span class="glyphicon glyphicon-book"></span>&nbsp;&nbsp;<b>Type</b></h4>
                    @if ($type != null)
                        <span class="label label-default">{{ $type }}</span>
                    @else
                        Unable to find type.
                    @endif
                </li>

                <li class="list-group-item">
                    <h4><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp;<b>Associated Names</b></h4>
                    @if ($assoc_names != null)
                        @foreach ($assoc_names as $assoc_name)
                            <span class="label label-default">{{ $assoc_name->getName() }}</span>
                        @endforeach
                    @else
                        Unable to find associated names.
                    @endif
                </li>

                <li class="list-group-item">
                    <h4><span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;<b>Genres</b></h4>
                    @if ($genres != null)
                        @foreach ($genres as $genre)
                            <span class="label label-default">{{ $genre->getName() }}</span>
                        @endforeach
                    @else
                        Unable to find genres.
                    @endif
                </li>

                <li class="list-group-item">
                    <h4><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;<b>Authors</b></h4>
                    @if ($authors != null)
                        @foreach ($authors as $author)
                            <span class="label label-default">{{ $author->getName() }}</span>
                        @endforeach
                    @else
                        Unable to find authors.
                    @endif
                </li>

                <li class="list-group-item">
                    <h4><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;<b>Artists</b></h4>
                    @if ($artists != null)
                        @foreach ($artists as $artist)
                            <span class="label label-default">{{ $artist->getName() }}</span>
                        @endforeach
                    @else
                        Unable to find artists.
                    @endif
                </li>

                <li class="list-group-item">
                    <h4><span class="glyphicon glyphicon-calendar"></span>&nbsp;&nbsp;<b>Year</b></h4>
                    @if ($year != null)
                        <span class="label label-default">{{ $year }}</span>
                    @else
                        Unable to find year.
                    @endif
                </li>

                @if (\Auth::user()->isAdmin() || \Auth::user()->isMaintainer())
                <li class="list-group-item">
                    <h4><span class="glyphicon glyphicon-saved"></span>&nbsp;&nbsp;<b>Last Updated</b></h4>
                    @if ($lastUpdated != null)
                        <span class="label label-default">{{ $lastUpdated }}</span>
                    @else
                        Unable to find date of last update.
                    @endif
                </li>
                @endif

                @if (\Auth::user()->isAdmin() == true)
                    <li class="list-group-item">
                        <h4><span class="glyphicon glyphicon-hdd"></span>&nbsp;&nbsp;<b>Path</b></h4>
                        {{ $path }}
                    </li>
                @endif

            </ul>
        </div>

        <div class="tab-pane" id="files-content">
            <div class="container-fluid">
                <div class="row">
                        <table class="table table-hover table-condensed" style="word-break: break-all; ">
                            <thead>
                            <tr>
                                <th class="col-xs-8">
                                    <a href="{{ \URL::action('MangaInformationController@index', [$id, $sort == 'ascending' ? 'descending' : 'ascending']) }}">Filename&nbsp;
                                        @if ($sort == 'ascending')
                                            <span class="glyphicon glyphicon-triangle-top"></span>
                                        @else
                                            <span class="glyphicon glyphicon-triangle-bottom"></span>
                                        @endif
                                    </a>
                                </th>
                                <th class="col-xs-2">Size</th>
                                <th class="col-sm-2 visible-sm visible-md visible-lg">Modified</th>
                            </tr>
                            </thead>

                            <tbody>
                            @if (empty($archives) === false)
                                @foreach ($archives as $archive)
                                    <tr>
                                        <td class="col-xs-8">
                                            <a href="{{ URL::action('ReaderController@index', [$id, rawurlencode($archive['name']), 1]) }}">
                                                {{ $archive['name'] }}
                                            </a>
                                        </td>
                                        <td class="col-xs-2">
                                            {{ $archive['size'] }}
                                        </td>
                                        <td class="col-sm-2 visible-sm visible-md visible-lg">
                                            {{ $archive['modified'] }}
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
