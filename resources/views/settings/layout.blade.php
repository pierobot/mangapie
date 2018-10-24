@extends ('layout')

@section ('content')
    <div class="d-flex justify-content-center">
        <h4><strong>Settings</strong></h4>
    </div>

    @include ('shared.success')
    @include ('shared.errors')

    <div class="row">
        <div class="col-12">
            @yield('tab-content')
        </div>
    </div>
@endsection
