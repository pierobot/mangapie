@extends ('layout')

@section ('title')
    Information &middot; {{ $manga->name }}
@endsection

@section ('content')
    @include ('shared.success')
    @include ('shared.errors')

    <div class="row">
        <div class="col-12">
            <div class="d-flex d-sm-none flex-column">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            {{ $manga->name }}
                        </h5>
                    </div>

                    <img class="card-img" src="{{ URL::action('CoverController@mediumDefault', [$manga]) }}">

                    <div class="d-flex justify-content-center mt-3 mb-3">
                        @include ('manga.shared.actions')
                    </div>
                </div>
            </div>

            <div class="d-none d-sm-flex flex-column">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            {{ $manga->name }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <img class="img-fluid" src="{{ URL::action('CoverController@mediumDefault', [$manga]) }}">

                                <div class="d-flex justify-content-center mt-3 mb-3">
                                    @include ('manga.shared.actions')
                                </div>
                            </div>

                            <div class="col-sm-8">
                                @include ('manga.shared.information')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            @yield ('lower-card')
        </div>
    </div>
@endsection
