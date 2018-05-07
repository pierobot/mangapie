@extends ('layout')

@section ('title')
    Reader &middot; {{ $archive_name }}
@endsection

@section ('custom_navbar_right')
    <li class="clickable navbar-link"><a href="{{ URL::action('MangaController@index', [$id]) }}"><span class="glyphicon glyphicon-book white"></span> Information</a></li>

@if ($page_count != 0)

    @if($ltr == false)
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
    @else
        @if ($has_prev_page)
            <li class="clickable navbar-link"><a href="{{ $prev_url }}" id="prev-image"><span class="glyphicon glyphicon-chevron-left white"></span> Previous</a></li>
        @else
            <li class="navbar-link disabled"><a href="#" id="prev-image"><span class="glyphicon glyphicon-chevron-left white"></span> Previous</a></li>
        @endif

        @if ($has_next_page)
            <li class="clickable navbar-link"><a href="{{ $next_url }}" id="next-image"><span class="glyphicon glyphicon-chevron-right white"></span> Next</a></li>
        @else
            <li class="navbar-link disabled"><a href="#" id="next-image"><span class="glyphicon glyphicon-chevron-right white"></span> Next</a></li>
        @endif
    @endif

    <li class="dropdown">
        <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="glyphicon glyphicon-file"></span>&nbsp;Page
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
    @include ('shared.errors')

    @if ($page_count !== 0)
        <div class="row text-center">
            {{--<a id="image" href="{{ $has_next_page ? $next_url : "#" }}">--}}
                {{ Html::image(URL::action('ReaderController@image', [$id, rawurlencode($archive_name), $page]), 'image', ['class' => 'reader-image']) }}
            {{--</a>--}}
        </div>

        @if ($preload !== false)
        <div id="preload" style="display: none;">
            @foreach ($preload as $preload_url)
                <img id="{{ $preload_url['id'] }}" data-src="{{ $preload_url['url'] }}">
            @endforeach
        </div>
        @endif
    @endif
@endsection

@section ('scripts')
    <script type="text/javascript">
        $(function () {

            // set up handler for key events
            $(document).on('keyup', function (e) {
                if (e.keyCode == 37 || e.keyCode == 65) {
                    // left arrow or a
                    @if ($ltr == false)
                        window.location = $('#next-image').attr('href');
                    @else
                        window.location = $('#prev-image').attr('href');
                    @endif
                } else if (e.keyCode == 39 || e.keyCode == 68) {
                    // right arrow or d
                    @if ($ltr == false)
                        window.location = $('#prev-image').attr('href');
                    @else
                        window.location = $('#next-image').attr('href');
                    @endif
                }
            });

            // go through each img in #preload and load it
            $('#preload > img').each(function () {
                $(this).attr('src', $(this).attr('data-src'));
            });

            $('.reader-image').click(function (eventData) {
                var x = eventData.screenX;
                /*
                    offsetX will give us an offset relative to the image
                    screenX will guve us an offset relative to the viewport

                    If the user zooms in on the right hand side of the image the following happens:
                        - Using offsetX
                            - On LTR
                                - Left side click
                                    - window.location changes to the next image
                                - Right side click
                                    - window.location changes to the next image
                        - Using screenX
                            - On LTR
                                - Left side click
                                    - window.location changes to the previous image
                                - Right side click
                                    window.location changes to the next image

                    I assume the vast majority of people would want the behavior of screenX.
                */

                var width = $('.reader-image').width();

                if (x < (width / 2)) {
                    // left side click
                    @if ($ltr == false)
                        window.location = $('#next-image').attr('href');
                    @else
                        window.location = $('#prev-image').attr('href');
                    @endif
                } else {
                    // right side click
                    @if ($ltr == false)
                        window.location = $('#prev-image').attr('href');
                    @else
                        window.location = $('#next-image').attr('href');
                    @endif
                }
            });
        });
    </script>
@endsection
