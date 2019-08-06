@extends ('layout')

@section ('title')
    Information &middot; {{ $manga->name }}
@endsection

@section ('content')
    <div class="container mt-3">
        @include ('shared.success')
        @include ('shared.errors')

        @php
            $completed = ! empty($user->completed->where('manga_id', $manga->id)->first());
            $dropped = ! empty($user->dropped->where('manga_id', $manga->id)->first());
            $onHold = ! empty($user->onHold->where('manga_id', $manga->id)->first());
            $reading = ! empty($user->reading->where('manga_id', $manga->id)->first());
        @endphp

        <div class="d-flex d-sm-none flex-column">
            <div class="row">
                <div class="col-12 text-center">
                    <h3 class="mb-3">
                        <strong>{{ $manga->name }}</strong>
                    </h3>

                    {{--@if ($user->admin || $user->maintainer)--}}
                    {{--<a href="{{ action('MangaEditController@covers', [$manga]) }}" style="position: relative; left: 50%;">--}}
                    {{--<span class="fa fa-edit"></span>--}}
                    {{--</a>--}}
                    {{--@endif--}}
                </div>
            </div>

            <div class="row">
                <div class="col-12 text-center">
                    <img class="img-fluid" src="{{ URL::action('CoverController@mediumDefault', [$manga]) }}">
                </div>
            </div>
        </div>

        <div class="d-none d-sm-flex flex-sm-column">
            <div class="row">
                <div class="col-12">
                    <h1 class="mb-3">
                        <strong>{{ $manga->name }}</strong>
                    </h1>

                    <div class="row">
                        <div class="col-4">
                            <img class="img-fluid" src="{{ URL::action('CoverController@mediumDefault', [$manga]) }}">
                        </div>

                        <div class="col-8">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    @include ('manga.shared.information.genres')
                                </div>

                                <div class="col-12 mb-3">
                                    @include ('manga.shared.information.associated_names')
                                </div>

                                <div class="col-12 mb-3">
                                    @include ('manga.shared.information.authors')
                                </div>

                                <div class="col-12 mb-3">
                                    @include ('manga.shared.information.artists')
                                </div>
                            </div>
                        </div>

                        {{--<div class="col-9">--}}
                        {{--@if ($user->admin || $user->maintainer)--}}
                        {{--<a href="{{ action('MangaEditController@covers', [$manga]) }}">--}}
                        {{--<span class="fa fa-edit fa-2x"></span>--}}
                        {{--</a>--}}
                        {{--@endif--}}
                        {{--</div>--}}
                    </div>

                    <div class="row mt-3">
                        <div class="col-4">
                            @include ('manga.shared.information.ratings')
                        </div>

                        <div class="col-8">
                            @include ('manga.shared.information.actions')
                        </div>

                        @admin
                        <div class="col-12 mb-3">
                            @include ('manga.shared.information.path')
                        </div>
                        @endadmin


                        <div class="col-12 mb-3">
                            @include ('manga.shared.information.description')
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="row mt-3">
            <div class="col-12">
                @yield ('lower-card')
            </div>
        </div>
    </div>
@endsection
