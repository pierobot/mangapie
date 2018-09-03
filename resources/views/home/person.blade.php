@extends ('layout')

@section ('title')
    {{ $person->name }} &colon;&colon; Mangapie
@endsection

@section ('custom_navbar_right')
@endsection

@section ('content')
    @include ('shared.errors')
    @include ('shared.index')
@endsection
