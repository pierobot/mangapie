@extends ('layout')

@section ('content')
    @include ('shared.success')
    @include ('shared.warnings')
    @include ('shared.errors')

    @yield ('top-menu')
    @yield ('card-content')
@endsection
