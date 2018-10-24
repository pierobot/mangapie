@extends ('layout')

@section ('title')
    Reader &middot; {{ $archive_name }}
@endsection

@section ('content')
    @include ('shared.errors')

    @if ($page_count !== 0)
        <div class="row text-center" style="padding-bottom: 60px;">
            <div class="col-12">
                <img id="reader-image" class="h-auto w-100" src="{{ URL::action('ReaderController@image', [$id, $archive, $page]) }}">
            </div>
        </div>
    @endif

    @if ($preload !== false)
    <div id="preload" style="display: none;">
        @foreach ($preload as $index => $preload_url)
            <img id="{{ $index }}" data-src="{{ $preload_url }}">
        @endforeach
    </div>
    @endif

    <div class="hidden">
        @if ($readDirection === 'ltr')
            @if ($has_prev_page)
                <a href="{{ $prev_url }}" id="prev-image"></a>
            @else
                <a href="#" id="prev-image"></a>
            @endif

            @if ($has_next_page)
                <a href="{{ $next_url }}" id="next-image"></a>
            @else
                <a href="#" id="next-image"></a>
            @endif
        @elseif ($readDirection === 'rtl')
            @if ($has_next_page)
                <a href="{{ $next_url }}" id="next-image"></a>
            @else
                <a href="#" id="next-image"></a>
            @endif

            @if ($has_prev_page)
                <a href="{{ $prev_url }}" id="prev-image"></a>
            @else
                <a href="#" id="prev-image"></a>
            @endif
        @endif
    </div>
@endsection

@section ('footer-contents')
    <nav class="navbar navbar-dark bg-dark fixed-bottom">
        <div class="container">

            {{--<div class="collapse navbar-collapse" id="reader-navbar-settings-collapse-div">--}}
                {{--<ul class="nav navbar-nav">--}}
                    {{--<li class="nav-item">--}}
                        {{--<div class="card bg-transparent border-0">--}}
                            {{--<div class="card-body p-0 m-2">--}}
                                {{--{{ Form::open(['action' => 'ReaderSettingsController@put', 'method' => 'put', 'class' => 'inline-form']) }}--}}

                                {{--<div class="input-group">--}}
                                    {{--<div class="input-group-prepend">--}}
                                        {{--<label class="input-group-text" for="direction">Direction</label>--}}
                                    {{--</div>--}}

                                    {{--<select class="custom-select" id="direction">--}}
                                        {{--<option value="ltr">Left-to-Right</option>--}}
                                        {{--<option value="rtl">Right-to-Left</option>--}}
                                    {{--</select>--}}

                                {{--</div>--}}

                                {{--{{ Form::close() }}--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</li>--}}
                {{--</ul>--}}
            {{--</div>--}}

            <div class="collapse navbar-collapse" id="navigation-collapse">
                <ul class="nav navbar-nav">
                    <li class="nav-item text-center">
                        <a class="nav-link" href="{{ URL::action('MangaController@index', [$id]) }}">
                            <h4>
                                <span class="fa fa-book-open"></span>
                                &nbsp;<strong>{{ $name }}</strong>
                            </h4>
                        </a>
                    </li>
                    <li class="nav-item text-center">
                        <label class="text-muted">
                            <span class="fa fa-file-archive"></span>
                            &nbsp;<span class="text-muted">{{ $archive->name }}</span>
                        </label>
                    </li>

                    <li class="nav-item">
                        <div class="card text-center bg-transparent border-0">
                            <div class="card-body">
                                <div class="btn-group btn-group-lg">
                                    @if ($readDirection === 'ltr')
                                        <a class="btn btn btn-secondary @if (! $has_prev_page) disabled @endif" href="{{ $has_prev_page ? $prev_url : "" }}">
                                            <span class="fa fa-chevron-left"></span>
                                        </a>
                                    @elseif ($readDirection === 'rtl')
                                        <a class="btn btn btn-secondary @if (! $has_next_page) disabled @endif" href="{{ $has_next_page ? $next_url : "" }}">
                                            <span class="fa fa-chevron-left"></span>
                                        </a>
                                    @endif

                                    <div class="btn-group gtn-group-lg dropup">
                                        <button class="btn btn-secondary" data-toggle="dropdown">
                                            {{ $page }} of {{ $page_count }}&nbsp;
                                            <span class="fa fa-chevron-up"></span>
                                        </button>
                                        <div class="dropdown-menu bg-secondary position-absolute">
                                            @for ($i = 1; $i <= $page_count; $i++)
                                                <a class="dropdown-item" href="{{ URL::action('ReaderController@index', [$id, $archive, $i]) }}">{{ $i }}</a>
                                            @endfor
                                        </div>
                                    </div>

                                    @if ($readDirection === 'ltr')
                                        <a class="btn btn btn-secondary @if (! $has_next_page) disabled @endif" href="{{ $has_next_page ? $next_url : "" }}">
                                            <span class="fa fa-chevron-right"></span>
                                        </a>
                                    @elseif ($readDirection === 'rtl')
                                        <a class="btn btn btn-secondary @if (! $has_prev_page) disabled @endif" href="{{ $has_prev_page ? $prev_url : "" }}">
                                            <span class="fa fa-chevron-right"></span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            {{-- disabled for now --}}
            <button disabled class="navbar-toggler ml-auto btn btn-secondary" type="button" data-toggle="collapse" data-target="#reader-navbar-settings-collapse-div" aria-expanded="false" title="Open settings">
                <span class="fa fa-cog text-white-50"></span>
            </button>
            <div class="ml-1 mr-1"></div>

            @php
                $favorite = auth()->guard()->user()->favorites->where('manga_id', $id)->first();
            @endphp
            @if (empty($favorite))
                {{ Form::open(['action' => 'FavoriteController@create', 'method' => 'post', 'class' => 'inline-form m-0']) }}
                {{ Form::hidden('manga_id', $id) }}
                <button class="navbar-toggler btn favorite-toggler" type="submit" title="Add to favorites" data-favorited="no">
                    <span class="fa fa-heart"></span>
                </button>
                {{ Form::close() }}
            @else
                {{ Form::open(['action' => 'FavoriteController@delete', 'method' => 'delete', 'class' => 'inline-form m-0']) }}
                {{ Form::hidden('favorite_id', $favorite->id) }}
                <button class="navbar-toggler btn favorite-toggler" type="submit" title="Remove from favorites" data-favorited="yes">
                    <span class="fa fa-heart"></span>
                </button>
                {{ Form::close() }}
            @endif
            <div class="ml-1 mr-1"></div>

            <a href="{{ URL::action('MangaController@comments', [$id]) }}" title="Go to comments">
                <button class="navbar-toggler btn btn-secondary">
                    <span class="fa fa-comments"></span>
                </button>
            </a>
            <div class="ml-1 mr-1"></div>

            <button class="navbar-toggler btn btn-secondary mr-auto" type="button" data-toggle="collapse" data-target="#navigation-collapse" title="Navigation">
                <span class="fa fa-arrows-alt-h"></span>
            </button>
        </div>
    </nav>
@endsection

@section ('scripts')
    <script type="text/javascript">
        $(function () {

            // set up handler for key events
            $(document).on('keyup', function (e) {
                // do not handle key events for typing in searchbar
                $focused = $(':focus');
                if ($focused.attr('id') === $("#searchbar").attr('id') ||
                    $focused.attr('id') === $("#searchbar-small").attr('id'))
                    return;

                // do not handle events where ctrl, alt, or shift are pressed
                if (e.ctrlKey || e.altKey || e.shiftKey)
                    return;

                if (e.keyCode === 37 || e.keyCode === 65) {
                    // left arrow or a
                    @if ($readDirection === 'rtl')
                        window.location = $('#next-image').attr('href');
                    @elseif ($readDirection === 'ltr')
                        window.location = $('#prev-image').attr('href');
                    @endif
                } else if (e.keyCode === 39 || e.keyCode === 68) {
                    // right arrow or d
                    @if ($readDirection === 'rtl')
                        window.location = $('#prev-image').attr('href');
                    @elseif ($readDirection === 'ltr')
                        window.location = $('#next-image').attr('href');
                    @endif
                }
            });

            // go through each img in #preload and load it
            $('#preload > img').each(function () {
                $(this).attr('src', $(this).attr('data-src'));
            });

            $('#reader-image').click(function (eventData) {
                const x = eventData.offsetX;
                const width = $('#reader-image').width();

                if (x < (width / 2)) {
                    // left side click
                    @if ($readDirection === 'rtl')
                        window.location = $('#next-image').attr('href');
                    @elseif ($readDirection === 'ltr')
                        window.location = $('#prev-image').attr('href');
                    @endif
                } else {
                    // right side click
                    @if ($readDirection === 'rtl')
                        window.location = $('#prev-image').attr('href');
                    @elseif ($readDirection === 'ltr')
                        window.location = $('#next-image').attr('href');
                    @endif
                }
            });
        });
    </script>
@endsection
