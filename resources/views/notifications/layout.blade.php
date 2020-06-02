@extends ('layout')

@section ('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section ('content')

    @yield ('notification-content')
@endsection

