@extends ('layout')

@section ('title')
    Edit &middot; {{ $manga->name }}
@endsection

@section ('custom_navbar_right')
@endsection

@section ('content')
    <div class="d-block d-sm-none">
        <h3 class="text-center"><b>Edit &middot; <a href="{{ URL::action('MangaController@index', [$manga]) }}">{{ $manga->name }}</a></b></h3>
    </div>
    <div class="d-none d-sm-block">
        <h2 class="text-center"><b>Edit &middot; <a href="{{ URL::action('MangaController@index', [$manga]) }}">{{ $manga->name }}</a></b></h2>
    </div>

    @include ('shared.success')
    @include ('shared.errors')

    <div class="row">
        <div class="col-md-3">
            @yield('side-top-menu')
        </div>
        <div class="col-md-9">
            @yield('tab-content')
        </div>
    </div>
@endsection