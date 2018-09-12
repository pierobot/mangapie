@extends ('layout')

@section ('title')
    Information &middot; {{ $manga->name }}
@endsection

@section ('custom_navbar_right')
@endsection

@section ('content')
    <h3 class="d-block d-sm-none text-center">
        <b>Information &middot; {{ $manga->name  }}</b>

        @maintainer
        <div class="row">
            <div class="col">
                <a href="{{ \URL::action('MangaEditController@index', [$manga]) }}">
                    <b>Edit</b>
                </a>
            </div>
        </div>
        @endmaintainer
    </h3>

    <h2 class="d-none d-sm-block text-center">
        <b>Information &middot; {{ $manga->name  }}</b>

        @maintainer
        <div class="row">
            <div class="col">
                <a href="{{ \URL::action('MangaEditController@index', [$manga]) }}">
                    <b>Edit</b>
                </a>
            </div>
        </div>
        @endmaintainer
    </h2>

    @include ('shared.success')
    @include ('shared.errors')

    <div class="row">
        <div class="col col-sm-4">
            <div class="card">
                <img class="card-img-top" src="{{ URL::action('CoverController@mediumDefault', [$manga]) }}">
            </div>
        </div>

        <div class="d-none d-sm-block col-sm-8">
            @component ('manga.components.information', [
                'user' => $user,
                'manga' => $manga
            ])
            @endcomponent
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col">
            @yield ('navtabs-content')
        </div>
    </div>
@endsection
