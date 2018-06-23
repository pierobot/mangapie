@extends ('layout')

@section ('title')
    Edit &middot; {{ $name }}
@endsection

@section ('custom_navbar_right')
@endsection

@section ('content')
    <div class="visible-xs">
        <h3 class="text-center"><b>Edit &middot; <a href="{{ URL::action('MangaController@index', [$id]) }}">{{ $name }}</a></b></h3>
    </div>
    <div class="visible-sm visible-md visible-lg">
        <h2 class="text-center"><b>Edit &middot; <a href="{{ URL::action('MangaController@index', [$id]) }}">{{ $name }}</a></b></h2>
    </div>

    @include ('shared.success')
    @include ('shared.errors')

    <div class="row">
        <div class="col-md-2">
            <ul class="nav nav-pills nav-stacked">
                <li role="presentation">
                    <a href="{{ URL::action('MangaEditController@mangaupdates', [$id]) }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mangaupdates</a>
                </li>

                <li role="presentation">
                    <a href="#Information" data-toggle="collapse" data-target="#Information-collapse">
                        <span class="glyphicon glyphicon-info-sign"></span>&nbsp;&nbsp;Information
                    </a>

                    <ul class="nav nav-pills nav-stacked collapse" id="Information-collapse">
                        <li role="presentation"><a href="{{ URL::action('MangaEditController@description', [$id]) }}">Description</a></li>
                        <li role="presentation"><a href="{{ URL::action('MangaEditController@type', [$id]) }}">Type</a></li>
                        <li role="presentation"><a href="{{ URL::action('MangaEditController@names', [$id]) }}">Names</a></li>
                        <li role="presentation"><a href="{{ URL::action('MangaEditController@genres', [$id]) }}">Genres</a></li>
                        <li role="presentation"><a href="{{ URL::action('MangaEditController@authors', [$id]) }}">Authors</a></li>
                        <li role="presentation"><a href="{{ URL::action('MangaEditController@artists', [$id]) }}">Artists</a></li>
                        <li role="presentation"><a href="{{ URL::action('MangaEditController@year', [$id]) }}">Year</a></li>
                    </ul>
                </li>

                <li role="presentation">
                    <a href="{{ URL::action('MangaEditController@covers', [$id]) }}">
                        <span class="glyphicon glyphicon-picture"></span>&nbsp;&nbsp;Covers
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-md-10">
            @yield('tab-content')
        </div>
    </div>
@endsection