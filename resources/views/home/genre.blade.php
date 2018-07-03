@extends ('layout')

@section ('title')
    {{ $genre->getName() }} &colon;&colon; Mangapie
@endsection

@section ('custom_navbar_right')
@endsection

@section ('content')
    @include ('shared.errors')
    @include ('shared.index')
@endsection
