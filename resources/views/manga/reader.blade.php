@extends ('layout')

@section ('title')
    Reader &middot; {{ $name }}
@endsection

@section ('stylesheets')
    <link href="{{ URL::to('/public/css/manga/reader.css') }}" rel="stylesheet">
@endsection

@section ('custom_navbar_right')
    <li class="clickable navbar-link"><a href="{{ URL::action('MangaInformationController@index', [$id]) }}"><span class="glyphicon glyphicon-book white"></span> Information</a></li>

@if ($page_count !== false)

    @if ($has_next_page)
        <li class="clickable navbar-link"><a href="{{ $next_url }}" id="next-image"><span class="glyphicon glyphicon-chevron-left white"></span> Next</a></li>
    @else
        <li class="navbar-link disabled"><a href="#" id="next-image"><span class="glyphicon glyphicon-chevron-left white"></span> Next</a></li>
    @endif

    @if ($has_prev_page)
        <li class="clickable navbar-link"><a href="{{ $prev_url }}" id="prev-image"><span class="glyphicon glyphicon-chevron-right white"></span> Previous</a></li>
    @else
        <li class="navbar-link disabled"><a href="#" id="prev-image"><span class="glyphicon glyphicon-chevron-right white"></span> Previous</a></li>
    @endif

    <li class="dropdown">
        <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Page
            <span class="badge">{{ $page }}</span> of
            <span class="badge">{{ $page_count }}</span>
            <span class="glyphicon glyphicon-chevron-down white"></span>
        </a>
        <ul class="dropdown-menu" style="color: black;">
            @for ($i = 1; $i <= $page_count; $i++)
            <li>
                {{ Html::link(URL::action('ReaderController@index', [$id, rawurlencode($archive_name), $i]), $i) }}
            </li>
            @endfor
        </ul>
    </li>
@endif
@endsection

@section ('content')

@if (\Session::has('reader-failure'))

    <div class="alert alert-danger">
        <span class="glyphicon glyphicon-remove"></span>&nbsp; {{ \Session::get('reader-failure') }}
    </div>

@endif

@if ($page_count !== false)
    <div class="row">
        <a href="{{ $has_next_page ? $next_url : "" }}">
            {{ Html::image(URL::action('ReaderController@image', [$id, rawurlencode($archive_name), $page]), 'image', ['class' => 'reader-image center-block']) }}
        </a>
    </div>
@else

<div class="alert alert-danger">Unable to get images from archive.</div>

@endif

@endsection

@section ('scripts')

{{-- <script src="http://hammerjs.github.io/dist/hammer.min.js" type="text/javascript"></script> --}}
{{-- <script src="{{ URL::to('/public/js/manga/reader.js') }}" type="text/javascript"></script> --}}

@endsection
