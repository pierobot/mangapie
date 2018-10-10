@extends ('layout')

@section ('content')
    @include ('shared.success')
    @include ('shared.warnings')
    @include ('shared.errors')

    <div class="row mt-3">
        <div class="col-12 col-sm-2">
            @yield ('side-top-menu')
        </div>
        <div class="col-12 col-sm-10">
            @yield ('card-content')
        </div>
    </div>
@endsection