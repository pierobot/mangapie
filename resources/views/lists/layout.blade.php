@extends ('layout')

@section ('content')
    <div class="container mt-3">
        @include ('shared.errors')
        @include ('shared.success')

        @yield ('list-content')
    </div>
@endsection
