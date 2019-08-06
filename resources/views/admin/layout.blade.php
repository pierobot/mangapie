@extends ('layout')

@section ('content')
    <div class="container mt-3">
        @include ('shared.success')
        @include ('shared.warnings')
        @include ('shared.errors')

        @yield ('top-menu')
        @yield ('card-content')
    </div>
@endsection
