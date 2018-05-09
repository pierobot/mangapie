@extends ('layout')

@section ('title')
    Favorites :: Mangapie
@endsection

@section ('custom_navbar_right')
    @include ('shared.libraries')
@endsection

@section ('content')
    @include ('shared.errors')
    @include ('shared.index')
@endsection

@section ('scripts')
    @include ('shared.autocomplete')
@endsection
