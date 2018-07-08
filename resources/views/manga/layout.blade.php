@extends ('layout')

@section ('title')
    Information &middot; {{ $manga->name }}
@endsection

@section ('custom_navbar_right')
@endsection

@section ('content')
    <h3 class="visible-xs text-center">
        <b>Information &middot; {{ $manga->name  }}</b>

        @maintainer
        <div class="row">
            <a href="{{ \URL::action('MangaEditController@index', [$manga]) }}">
                <b>Edit</b>
            </a>
        </div>
        @endmaintainer
    </h3>

    <h2 class="hidden-xs text-center">
        <b>Information &middot; {{ $manga->name  }}</b>

        @maintainer
        <div class="row">
            <a href="{{ \URL::action('MangaEditController@index', [$manga]) }}">
                <b>Edit</b>
            </a>
        </div>
        @endmaintainer
    </h2>

    @include ('shared.success')
    @include ('shared.errors')

    <div class="row">
        <div class="col-xs-12 col-sm-4">
            {{ Html::image(URL::action('CoverController@mediumDefault', [$manga]), '', ['class' => 'information-img center-block']) }}
        </div>

        <div class="hidden-xs col-sm-8">
            @component ('manga.components.information', [
                'user' => $user,
                'manga' => $manga
            ])
            @endcomponent
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-xs-12">
            @yield ('navtabs-content')
        </div>
    </div>
@endsection
