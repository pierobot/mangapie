@extends ('layout')

@section ('title')
    Reader &middot; {{ $archive_name }}
@endsection

@section ('custom_navbar_right')
    <li class="clickable navbar-link"><a href="{{ URL::action('MangaController@index', [$id]) }}"><span class="glyphicon glyphicon-book white"></span> Information</a></li>
@endsection

@section ('content')
    @include ('shared.errors')

    @if ($page_count !== 0)
        <div class="row text-center">
            {{ Html::image(URL::action('ReaderController@image', [$id, $archive->getId(), $page]), 'image', ['class' => 'reader-image']) }}
        </div>

        <div class="row">
            <div class="col-xs-12">
                <table class="table table-va-middle">
                    <tbody>
                        <tr>
                            <td class="col-xs-2 text-center">
                                <div class="hidden-xs">
                                    @if ($ltr)
                                        @if ($has_prev_page)
                                            <a href="{{ $prev_url }}">
                                                <span class="glyphicon glyphicon-arrow-left glyphicon-size-4x"></span>
                                            </a>
                                        @endif
                                    @else
                                        @if ($has_next_page)
                                            <a href="{{ $next_url }}">
                                                <span class="glyphicon glyphicon-arrow-right glyphicon-size-4x"></span>
                                            </a>
                                        @endif
                                    @endif
                                </div>

                                <div class="visible-xs">
                                    @if ($ltr)
                                        @if ($has_prev_page)
                                            <a href="{{ $prev_url }}">
                                                <span class="glyphicon glyphicon-arrow-left glyphicon-size-2x"></span>
                                            </a>
                                        @endif
                                    @else
                                        @if ($has_next_page)
                                            <a href="{{ $next_url }}">
                                                <span class="glyphicon glyphicon-arrow-right glyphicon-size-2x"></span>
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </td>

                            <td class="col-xs-8 text-center">
                                <div class="hidden-xs">
                                    <h2>
                                        <b>Page {{ $page }} of {{ $page_count }}</b>
                                    </h2>
                                </div>

                                <div class="visible-xs">
                                    <h4>
                                        <b>Page {{ $page }} of {{ $page_count }}</b>
                                    </h4>
                                </div>
                            </td>

                            <td class="col-xs-2 text-center">
                                <div class="hidden-xs">
                                    @if ($ltr)
                                        @if ($has_next_page)
                                            <a href="{{ $next_url }}">
                                                <span class="glyphicon glyphicon-arrow-right glyphicon-size-4x"></span>
                                            </a>
                                        @endif
                                    @else
                                        @if ($has_prev_page)
                                            <a href="{{ $prev_url }}">
                                                <span class="glyphicon glyphicon-arrow-left glyphicon-size-4x"></span>
                                            </a>
                                        @endif
                                    @endif
                                </div>

                                <div class="visible-xs">
                                    @if ($ltr)
                                        @if ($has_next_page)
                                            <a href="{{ $next_url }}">
                                                <span class="glyphicon glyphicon-arrow-right glyphicon-size-2x"></span>
                                            </a>
                                        @endif
                                    @else
                                        @if ($has_prev_page)
                                            <a href="{{ $prev_url }}">
                                                <span class="glyphicon glyphicon-arrow-left glyphicon-size-2x"></span>
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if ($preload !== false)
        <div id="preload" style="display: none;">
            @foreach ($preload as $index => $preload_url)
                <img id="{{ $index }}" data-src="{{ $preload_url }}">
            @endforeach
        </div>
        @endif

        <div class="hidden">
            @if($ltr)
                @if ($has_prev_page)
                    <a href="{{ $prev_url }}" id="prev-image"><span class="glyphicon glyphicon-chevron-left white"></span> Previous</a>
                @else
                    <a href="#" id="prev-image"><span class="glyphicon glyphicon-chevron-left white"></span> Previous</a>
                @endif

                @if ($has_next_page)
                    <a href="{{ $next_url }}" id="next-image"><span class="glyphicon glyphicon-chevron-right white"></span> Next</a>
                @else
                    <a href="#" id="next-image"><span class="glyphicon glyphicon-chevron-right white"></span> Next</a>
                @endif
            @else
                @if ($has_next_page)
                    <a href="{{ $next_url }}" id="next-image"><span class="glyphicon glyphicon-chevron-left white"></span> Next</a>
                @else
                    <a href="#" id="next-image"><span class="glyphicon glyphicon-chevron-left white"></span> Next</a>
                @endif

                @if ($has_prev_page)
                    <a href="{{ $prev_url }}" id="prev-image"><span class="glyphicon glyphicon-chevron-right white"></span> Previous</a>
                @else
                    <a href="#" id="prev-image"><span class="glyphicon glyphicon-chevron-right white"></span> Previous</a>
                @endif
            @endif
            @endif
        </div>
@endsection

@section ('scripts')
    <script type="text/javascript">
        $(function () {

            // set up handler for key events
            $(document).on('keyup', function (e) {
                if (e.keyCode === 37 || e.keyCode === 65) {
                    // left arrow or a
                    @if ($ltr == false)
                        window.location = $('#next-image').attr('href');
                    @else
                        window.location = $('#prev-image').attr('href');
                    @endif
                } else if (e.keyCode === 39 || e.keyCode === 68) {
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
                const x = eventData.offsetX;
                const width = $('.reader-image').width();

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
