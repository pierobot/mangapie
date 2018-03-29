@extends ('layout')

@section ('title')
    Favorites
@endsection

@section ('custom_navbar_right')
    @include ('shared.searchbar')
    @include ('shared.libraries')
@endsection

@section ('content')
    <h3 class="text-center">
        <b>Favorites&nbsp;({{ $total }})</b>
    </h3>

    @include ('shared.errors')
    @include ('shared.index')
@endsection

@section ('scripts')
    @include ('shared.autocomplete')
@endsection
