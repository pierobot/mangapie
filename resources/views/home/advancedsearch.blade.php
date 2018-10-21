@extends ('layout')

@section ('title')
    {{ $header }} &colon;&colon; Mangapie
@endsection

@section ('content')
    @include ('shared.errors')
    @include ('shared.index')
@endsection
