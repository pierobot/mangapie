@extends ('layout')

@section ('title')
    Home &colon;&colon; Mangapie
@endsection

@section ('custom_navbar_right')
@endsection

@section ('content')
    @include ('shared.errors')
    @include ('shared.index')
@endsection
