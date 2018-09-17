@extends ('layout')

@section ('content')
    <div class="d-flex d-md-none">
        <h3 class="text-center"><b>Settings</b></h3>
    </div>
    <div class="d-none d-md-flex">
        <h2 class="text-center"><b>Settings</b></h2>
    </div>

    @include ('shared.success')
    @include ('shared.errors')

    <div class="row">
        <div class="col-12 col-sm-2">
            @yield ('side-top-menu')
        </div>
        <div class="col-12 col-sm-10">
            @yield('tab-content')
        </div>
    </div>
@endsection
