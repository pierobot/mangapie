<div class="row">
    <div class="col-12">
        @if (isset($header))
            <h3 class="text-center">
                <b>{{ $header }}</b>
            </h3>
        @endif
    </div>
</div>

<div class="row justify-content-center">
    @if (isset($manga_list))
        @foreach ($manga_list as $manga)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card mb-2">
                <div class="row no-gutters bg-dark">
                    <div class="col-4 col-md-5">
                        <img class="card-img" src="{{ URL::action('CoverController@smallDefault', [$manga]) }}">
                    </div>
                    <div class="col-8 col-md-7">
                        <div class="card-body">

                            <div class="d-sm-none">
                                <h3 class="card-title">
                                    <a href="{{ URL::action('MangaController@index', [$manga]) }}">{{ $manga->name }}</a>
                                </h3>
                            </div>

                            <div class="d-none d-sm-flex">
                                <h4 class="card-title">
                                    <a href="{{ URL::action('MangaController@index', [$manga]) }}">{{ $manga->name }}</a>
                                </h4>
                            </div>

                            @foreach ($manga->authorReferences as $authorReference)
                                <a href="{{ URL::action('PersonController@index', [$authorReference->author]) }}">{{ $authorReference->author->name }}</a>
                            @endforeach

                            <p>
                                <span class="fa fa-heart"><small>&nbsp;{{ $manga->favorites->count() }}</small></span>

                                @php ($averageRating = \App\Rating::average($manga))
                                <span class="fa fa-star offset-2"><small>&nbsp;{{ $averageRating == false ? 0 : $averageRating }}</small></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
    @endif
</div>

<div class="row mt-3">
    <div class="col-12">
        @if (isset($manga_list))
            {{ $manga_list->render('vendor.pagination.bootstrap-4') }}
        @endif
    </div>
</div>