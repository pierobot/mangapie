@extends ('layout')

@section ('stylesheets')
    <link href="{{ URL::to('/public/css/manga/reader.css') }}" rel="stylesheet">
@endsection

@section ('custom_navbar_right')
    <li class="clickable navbar-link"><a href="{{ URL::action('MangaInformationController@index', [$id]) }}"><span class="glyphicon glyphicon-book white"></span> Information</a></li>

@if ($page_count !== false)
    @if ($page == $page_count)
        <li class="navbar-link disabled"><a href="#" id="next-image"><span class="glyphicon glyphicon-chevron-left white"></span> Next</a></li>
    @else
        <li class="clickable navbar-link"><a href="{{ URL::action('ReaderController@index', [$id, rawurlencode($archive_name), $page + 1]) }}" id="next-image"><span class="glyphicon glyphicon-chevron-left white"></span> Next</a></li>
    @endif
    @if ($page == 1)
        <li class="navbar-link disabled"><a href="#" id="prev-image"><span class="glyphicon glyphicon-chevron-right white"></span> Previous</a></li>
    @else
        <li class="clickable navbar-link"><a href="{{ URL::action('ReaderController@index', [$id, rawurlencode($archive_name), $page - 1]) }}" id="prev-image"><span class="glyphicon glyphicon-chevron-right white"></span> Previous</a></li>
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
    @if ($prev_url === false && $next_url === true)
        <a href="{{ URL::action('ReaderController@index', [$id, rawurlencode($archive_name), $page + 1]) }}">
    @elseif ($prev_url === true && $next_url === true)
        <a href="{{ URL::action('ReaderController@index', [$id, rawurlencode($archive_name), $page + 1]) }}" prev_url="{{ URL::action('ReaderController@index', [$id, rawurlencode($archive_name), $page - 1]) }}">
        <!-- <a href="{{ $next_url}}" prev_url="{{ $prev_url }}"> -->
    @elseif ($prev_url === true && $next_url === false && $page < $page_count)
        <a href="{{ URL::action('ReaderController@index', [$id, rawurlencode($archive_name), $page - 1]) }}">
    @endif
            {{ Html::image(URL::action('ReaderController@image', [$id, rawurlencode($archive_name), $page]), 'image', ['class' => 'reader-image center-block']) }}
        </a>
    </div>
@else

<div class="alert alert-danger">Unable to get images from archive.</div>

@endif

@endsection

@section ('scripts')

{{-- <script src="http://hammerjs.github.io/dist/hammer.min.js" type="text/javascript"></script> --}}
<script src="{{ URL::to('/public/js/manga/reader.js') }}" type="text/javascript"></script>

@endsection
