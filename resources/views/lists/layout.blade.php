@extends ('layout')

@section ('title')
    Lists &colon;&colon; Mangapie
@endsection

@section ('content')
    @include ('shared.errors')
    @include ('shared.success')

    @yield ('list-content')
@endsection
