@extends ('layout')

@section ('title')
    {{ $header }} &colon;&colon; Mangapie
@endsection

@section ('content')
    <div class="container mt-3">
        @include ('shared.errors')
        @include ('shared.index')
    </div>
@endsection
