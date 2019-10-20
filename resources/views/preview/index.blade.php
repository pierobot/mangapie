@extends ('layout')

@section ('title')
    @php
        $volCh = App\Scanner::getVolumesAndChapters($archive->name);
        // If there is no volume or chapter in the name, or if the parsing failed
        // then just use the archive name :shrug:
        if (empty($volCh) || empty($volCh[0]))
            $nameVolCh = $archive->name;
        else
            $nameVolCh = $volCh[0][0];
    @endphp

    Preview &middot; {{ $manga->name }} &middot; {{ $nameVolCh }}
@endsection

@section ('content')
    <div class="container">
        {{--
            This element is placed here to account for the navbar height not being taken into consideration
            by the browser when navigating to the fragment.
        --}}
        <span id="preview-start"></span>

        <div class="row mt-3">
            <div class="col">
                <h3><a href="{{ URL::action('MangaController@index', [$manga]) }}">{{ $manga->name }}</a> - Preview - {{ $nameVolCh }}</h3>
            </div>
        </div>

        <div class="row mt-3 mb-3">
            @if ($pageCount >= 1)
                {{-- *** Do NOT use $page as that is reserved for the paginator *** --}}
                @for ($previewPage = 1; $previewPage <= $pageCount; $previewPage++)
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-3" id="{{ $previewPage }}">
                        <div class="card">
                            <a href="{{ URL::action('ReaderController@index', [$manga, $archive, $previewPage]) }}">
                                <span class="page-indicator-left bg-primary text-dark text-center">{{ $previewPage }}</span>

                                <img class="card-img lazyload"
                                     src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 180 250'%3E%3C/svg%3E"
                                     data-src="{{ URL::action('PreviewController@small', [$manga, $archive, $previewPage]) }}">
                            </a>
                        </div>
                    </div>
                @endfor
            @endif

            <span id="preview-end"></span>

            <a class="btn btn-lg btn-primary fab fab-2" href="#preview-start">
                <span class="fa fa-arrow-up"></span>
            </a>
            <a class="btn btn-lg btn-primary fab fab-1" href="#preview-end">
                <span class="fa fa-arrow-down"></span>
            </a>
        </div>
    </div>
@endsection

@section ('scripts')
    <script type="text/javascript">
        $(function () {
            let lazyLoad = new LazyLoad({
                elements_selector: ".lazyload",
                load_delay: 300
            });
        });
    </script>
@endsection
