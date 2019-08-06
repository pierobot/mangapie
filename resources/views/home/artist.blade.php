@extends ('layout')

@section ('title')
    {{ $artist->getName() }} &colon;&colon; Mangapie
@endsection

@section ('custom_navbar_right')
@endsection

@section ('content')
    <div class="container mt-3">
        @include ('shared.errors')
        @include ('shared.index')
    </div>
@endsection
