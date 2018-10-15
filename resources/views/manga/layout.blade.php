@extends ('layout')

@section ('title')
    Information &middot; {{ $manga->name }}
@endsection

@section ('content')
    {{--<h3 class="d-block d-sm-none text-center">--}}
        {{--<b>Information &middot; {{ $manga->name  }}</b>--}}

        {{--@maintainer--}}
        {{--<div class="row">--}}
            {{--<div class="col">--}}
                {{--<a href="{{ \URL::action('MangaEditController@index', [$manga]) }}">--}}
                    {{--<b>Edit</b>--}}
                {{--</a>--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--@endmaintainer--}}
    {{--</h3>--}}

    {{--<h2 class="d-none d-sm-block text-center">--}}
        {{--<b>Information &middot; {{ $manga->name  }}</b>--}}

        {{--@maintainer--}}
        {{--<div class="row">--}}
            {{--<div class="col">--}}
                {{--<a href="{{ \URL::action('MangaEditController@index', [$manga]) }}">--}}
                    {{--<b>Edit</b>--}}
                {{--</a>--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--@endmaintainer--}}
    {{--</h2>--}}

    @include ('shared.success')
    @include ('shared.errors')

    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex d-sm-none flex-column">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ $manga->name }}</h5>
                    </div>

                    <img class="card-img-bottom" src="{{ URL::action('CoverController@mediumDefault', [$manga]) }}">
                </div>
            </div>

            <div class="d-none d-sm-flex flex-column">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">{{ $manga->name }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <img class="img-fluid" src="{{ URL::action('CoverController@mediumDefault', [$manga]) }}">
                            </div>

                            <div class="col-sm-8">
                                @component ('manga.components.information', ['user' => $user, 'manga' => $manga])
                                @endcomponent
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
