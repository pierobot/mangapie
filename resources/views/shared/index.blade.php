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
            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                <div class="card mt-1 mb-1">
                    <a href="{{ URL::action('MangaController@index', [$manga]) }}">
                        <img class="card-img-top" src="{{ URL::action('CoverController@smallDefault', [$manga]) }}">
                    </a>
                    <div class="card-footer text-center pt-2 pb-0">
                        <div class="card-title" title="{{ $manga->name }}">
                            <a class="card-link" href="{{ URL::action('MangaController@index', [$manga]) }}">{{ $manga->name }}</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
    @endif
</div>

<div class="row">
    <div class="col-12">
        @if (isset($manga_list))
            {{ $manga_list->render('vendor.pagination.bootstrap-4') }}
        @endif
    </div>
</div>