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
        <div class="row mt-3">
            <div class="col">
                <h3><a href="{{ URL::action('MangaController@index', [$manga]) }}">{{ $manga->name }}</a> - Preview - {{ $nameVolCh }}</h3>
            </div>
        </div>

        <div class="row mt-3 mb-3">
            @if ($pageCount >= 1)
                {{-- *** Do NOT use $page as that is reserved for the paginator *** --}}
                @for ($previewPage = 1; $previewPage <= $pageCount; $previewPage++)
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-3">
                        <div class="card">
                            <a href="{{ URL::action('ReaderController@index', [$manga, $archive, $previewPage]) }}">
                                <img class="card-img" src="{{ URL::action('PreviewController@small', [$manga, $archive, $previewPage]) }}">
                            </a>
                        </div>
                    </div>
                @endfor
            @endif
        </div>
    </div>
@endsection
