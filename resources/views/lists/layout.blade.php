@extends ('layout')

@section ('content')
    @include ('shared.errors')
    @include ('shared.success')

    @yield ('list-content')
@endsection
