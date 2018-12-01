@extends ('layout')

@section ('title')
    Reader &middot; {{ $archive->name }}
@endsection

@section ('content')
    @include ('shared.errors')

    <div class="alert alert-warning" id="nojs-nostorage">
        It appears you either have javascript disabled or are denying mangapie access to the session storage.
        For a better viewing experience, enable javascript or permit session storage to mangapie.<br>
    </div>

    @php
        $readDirection = auth()->user()->read_direction;

        $previousArchive = $archive->getPreviousArchive();
        $previousArchivePageCount = ! empty($previousArchive) ? $previousArchive->getPageCount() : false;
        $nextArchive = $archive->getNextArchive();

        $previousArchiveUrl = ! empty($previousArchive) ?
            URL::action('ReaderController@index', [$manga, $previousArchive, $previousArchivePageCount]) :
            '';
        $nextArchiveUrl = ! empty($nextArchive) ?
            URL::action('ReaderController@index', [$manga, $nextArchive, 1]) :
            '';

        $nextUrl = false;
        $previousUrl = false;

        if ($page <= $pageCount) {
            if ($page === $pageCount) {
                $nextUrl = ! empty($nextArchive) ?
                    URL::action('ReaderController@index', [$manga, $nextArchive, 1]) :
                    false;
            } else {
                $nextUrl = URL::action('ReaderController@index', [$manga, $archive, $page + 1]);
            }
        }

        if ($page >= 1) {
            if ($page === 1) {
                $previousUrl = ! empty($previousArchive) ?
                    URL::action('ReaderController@index', [$manga, $previousArchive, $previousArchivePageCount]) :
                    false;
            } else {
                $previousUrl = URL::action('ReaderController@index', [$manga, $archive, $page - 1]);
            }
        }

        $preload = $archive->getPreloadUrls($page);
    @endphp

    @if ($pageCount !== 0)
        <div class="row text-center" style="padding-bottom: 60px;">
            <div class="col-12">
                <img id="reader-image" class="h-auto w-100" src="{{ URL::action('ReaderController@image', [$manga, $archive, $page]) }}">
            </div>
        </div>
    @endif

    @if ($preload !== false)
        <div id="preload" style="display: none;">
            @foreach ($preload as $index => $preload_url)
                <img id="{{ $page + 1 + $index }}" data-src="{{ $preload_url }}">
                @endforeach
        </div>
    @endif
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
                        <a class="nav-link" href="{{ URL::action('MangaController@index', [$manga->id]) }}">
                            <h4>
                                <span class="fa fa-book-open"></span>
                                &nbsp;<strong>{{ $manga->name }}</strong>
                            </h4>
                        </a>
                    </li>
                    <li class="nav-item text-center">
                        <label class="text-muted">
                            <span class="fa fa-file-archive"></span>
                            &nbsp;<span class="text-muted" id="span-archive-name">{{ $archive->name }}</span>
                        </label>
                    </li>

                    <li class="nav-item">
                        <div class="card text-center bg-transparent border-0">
                            <div class="card-body">
                                <div class="btn-group btn-group-lg">
                                    @if ($readDirection === 'ltr')
                                        <a class="btn btn btn-secondary @if (! $previousUrl) disabled @endif" href="{{ $previousUrl ? $previousUrl : "" }}" id="a-left">
                                            <span class="fa fa-chevron-left"></span>
                                        </a>
                                    @elseif ($readDirection === 'rtl')
                                        <a class="btn btn btn-secondary @if (! $nextUrl) disabled @endif" href="{{ $nextUrl ? $nextUrl : "" }}" id="a-left">
                                            <span class="fa fa-chevron-left"></span>
                                        </a>
                                    @endif

                                    <div class="btn-group gtn-group-lg dropup" id="dropdown-page">
                                        <button class="btn btn-secondary" data-toggle="dropdown">
                                            <span id="span-page-text">{{ $page }} of {{ $pageCount }}&nbsp;</span>
                                            <span class="fa fa-chevron-up"></span>
                                        </button>
                                        <div class="dropdown-menu bg-secondary position-absolute">
                                            @for ($i = 1; $i <= $pageCount; $i++)
                                                <a class="dropdown-item" href="{{ URL::action('ReaderController@index', [$manga, $archive, $i]) }}">{{ $i }}</a>
                                            @endfor
                                        </div>
                                    </div>

                                    @if ($readDirection === 'ltr')
                                        <a class="btn btn btn-secondary @if (! $nextUrl) disabled @endif" href="{{ $nextUrl ? $nextUrl : "" }}" id="a-right">
                                            <span class="fa fa-chevron-right"></span>
                                        </a>
                                    @elseif ($readDirection === 'rtl')
                                        <a class="btn btn btn-secondary @if (! $previousUrl) disabled @endif" href="{{ $previousUrl ? $previousUrl : "" }}" id="a-right">
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
                $favorite = auth()->user()->favorites->where('manga_id', $manga->id)->first();
            @endphp
            @if (empty($favorite))
                {{ Form::open(['action' => 'FavoriteController@create', 'method' => 'post', 'class' => 'inline-form m-0']) }}
                {{ Form::hidden('manga_id', $manga->id) }}
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

            <a href="{{ URL::action('MangaController@comments', [$manga->id]) }}" title="Go to comments">
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
        const g_mangaId = Number("{{ $manga->id }}");
        const g_archiveId = Number("{{ $archive->id }}");
        const g_archiveName = "{{ $archive->name }}";
        const g_page = Number("{{ $page }}");
        const g_pageCount = Number("{{ $pageCount }}");
        const g_previousArchiveUrl = @if (! empty($previousArchiveUrl)) "{{ $previousArchiveUrl }}" @else {{ 'undefined' }} @endif ;
        const g_nextArchiveUrl = @if (! empty($nextArchiveUrl)) "{{ $nextArchiveUrl }}" @else {{ 'undefined' }} @endif ;

        {{--
            TODO: Allow images from remote disks

            Just use the app url in the config for now.
         --}}
        const g_baseImageUrl = `{{ config('app.url') }}image/${g_mangaId}/${g_archiveId}/`;
        const g_baseReaderUrl = `{{ config('app.url') }}reader/${g_mangaId}/${g_archiveId}/`;

        const g_readerKey = `reader-${g_mangaId}-${g_archiveId}`;

        /**
         * Alters the DOM so that all the available images are preloaded.
         *
         * @return void
         */
        function preloadAll() {
            $('#preload > img').each(function () {
                $(this).attr('src', $(this).attr('data-src'));
            });
        }

        function preloadBuildPrevious(mangaId, archiveId, page) {
            // TODO: implement
        }

        /**
         * Constructs and appends an img child element to #preload.
         * This function will also remove the first preload element.
         *
         * @param mangaId
         * @param archiveId
         * @param page
         */
        function preloadBuildNext(mangaId, archiveId, page) {
            if (typeof mangaId !== "number" || typeof archiveId !== "number" || typeof page !== "number")
                throw "Invalid parameter; expected number.";

            let preload = $("#preload");
            const firstPage = Number(preload.children().first().attr("id"));
            const lastPage = Number(preload.children().last().attr("id"));
            const nextPage = lastPage + 1;

            if (page < firstPage || nextPage > g_pageCount) {
                return;
            }

            const imageUrl = g_baseImageUrl + `${nextPage}`;

            // create the new image and prepend
            let img = $("<img />").attr({
                "id": nextPage,
                "src": imageUrl,
                "data-src": imageUrl
            });

            preload.append(img);
            preload.children().first().remove();
        }

        /**
         * Performs navigation to the previous page.
         *
         * @return void
         */
        function navigatePrevious() {
            let readerData = window.mpReader.storageFind(`${g_mangaId}`, `${g_archiveId}`);
            let page = readerData['page'];
            const pageCount = readerData['page_count'];

            // if this is the first page then we have to go the previous archive, if any.
            if (page === 1) {
                if (g_previousArchiveUrl !== undefined) {
                    window.location = g_previousArchiveUrl;
                } else {
                    alert("There is no page or archive before this one.");
                }

                return;
            }

            // commit to the session storage and decrement the page
            readerData['page'] = --page;

            window.mpReader.storagePut(`${g_mangaId}`, `${g_archiveId}`, readerData);

            // update the history stack
            history.pushState(g_readerKey, '');
            history.replaceState(g_readerKey, '', g_baseReaderUrl + page);

            // update the current image
            $("#reader-image").attr("src", g_baseImageUrl + page);
            $("html, body").animate({scrollTop: '0px'}, 150);

            updateNavigationControls("{{ $readDirection }}", page);

            updateLastReadPage(g_mangaId, g_archiveId, page);
        }

        /**
         * Performs navigation to the next page.
         *
         * @return void
         */
        function navigateNext() {
            let readerData = window.mpReader.storageFind(`${g_mangaId}`, `${g_archiveId}`);
            let page = readerData['page'];
            const pageCount = readerData['page_count'];

            // if this is the last page then we have to go to the next archive, if any.
            if (page === pageCount) {
                if (g_nextArchiveUrl !== undefined) {
                    window.location = g_nextArchiveUrl;
                } else {
                    alert("There is no page or archive after this one.");
                }

                return;
            }

            // commit to the session storage and increment the page
            readerData['page'] = ++page;
            window.mpReader.storagePut(`${g_mangaId}`, `${g_archiveId}`, readerData);

            // update the history stack
            history.pushState(g_readerKey, '');
            history.replaceState(g_readerKey, '', g_baseReaderUrl + page);

            // update the current image
            $("#reader-image").attr("src", g_baseImageUrl + page);
            $("html, body").animate({scrollTop: '0px'}, 150);

            updateNavigationControls("{{ $readDirection }}", page);

            preloadBuildNext(g_mangaId, g_archiveId, page);

            updateLastReadPage(g_mangaId, g_archiveId, page);
        }

        /**
         * Updates the navigation controls.
         *
         * @param direction
         * @param page
         */
        function updateNavigationControls(direction, page) {
            $("#span-page-text").text(`${page} of ${g_pageCount}`);

            let aLeft = $("#a-left");
            let aRight = $("#a-right");
            let aLinksToPreviousArchive = page === 1 && g_previousArchiveUrl !== undefined;
            let aLinksToNextArchive = page === g_pageCount && g_nextArchiveUrl !== undefined;

            if (aLinksToPreviousArchive) {
                if (direction === 'ltr') {
                    aLeft.attr("href", g_previousArchiveUrl);
                } else if (direction === 'rtl') {
                    aRight.attr("href", g_previousArchiveUrl);
                }
            } else if (g_previousArchiveUrl !== undefined) {
                if (direction === 'ltr') {
                    aLeft.attr("href", g_baseReaderUrl + `${page - 1}`);
                } else if (direction === 'rtl') {
                    aRight.attr("href", g_baseReaderUrl + `${page - 1}`);
                }
            }

            if (aLinksToNextArchive) {
                if (direction === 'ltr') {
                    aRight.attr("href", g_nextArchiveUrl);
                } else if (direction === 'rtl') {
                    aLeft.attr("href", g_nextArchiveUrl);
                }
            } else if (g_nextArchiveUrl !== undefined) {
                if (direction === 'ltr') {
                    aRight.attr("href", g_baseReaderUrl + `${page + 1}`);
                } else if (direction === 'rtl') {
                    aLeft.attr("href", g_baseReaderUrl + `${page + 1}`);
                }
            }

            let aLeftShouldBeDisabled = false;
            let aRightShouldBeDisabled = false;

            if (direction === 'ltr') {
                aLeftShouldBeDisabled = page === 1 && g_previousArchiveUrl === undefined;
                aRightShouldBeDisabled = page === g_pageCount && g_nextArchiveUrl === undefined;
            } else if (direction === 'rtl') {
                aRightShouldBeDisabled = page === 1 && g_previousArchiveUrl === undefined;
                aLeftShouldBeDisabled = page === g_pageCount && g_nextArchiveUrl === undefined;
            }

            if (aLeftShouldBeDisabled) {
                aLeft.addClass("disabled");
            } else if (aLeft.hasClass("disabled")) {
                aLeft.removeClass("disabled");
            }

            if (aRightShouldBeDisabled) {
                aRight.addClass("disabled");
            } else if (aRight.hasClass("disabled")) {
                aRight.removeClass("disabled");
            }
        }

        function updateLastReadPage(mangaId, archiveId, page) {
            axios.put("{{ config('app.url') }}reader/history", {
                manga_id: mangaId,
                archive_id: archiveId,
                page: page
            }).catch(error => {
                alert("Unable to update last read page.");
            });
        }

        $(function () {

            preloadAll();

            // initialize the reader session storage
            const storageResult = window.mpReader.storagePut(`${g_mangaId}`, `${g_archiveId}`, {
                page: g_page,
                page_count: g_pageCount
            });

            // hide the warning if javascript is enabled and we have access to session storage
            if (storageResult !== false) {
                $("#nojs-nostorage").hide();
            }

            updateLastReadPage(g_mangaId, g_archiveId, g_page);

            $(window).on("popstate", function (event) {
                if (event.originalEvent.state) {
                    const data = window.mpReader.storageFind(`${g_mangaId}`, `${g_archiveId}`);

                    navigatePrevious();
                }
            });

            $("#a-left").on("click", function (e) {
                e.preventDefault();

                @if ($readDirection === 'rtl')
                    navigateNext();
                @elseif ($readDirection === 'ltr')
                    navigatePrevious();
                @endif
            });

            $("#a-right").on("click", function (e) {
                e.preventDefault();

                @if ($readDirection === 'rtl')
                    navigatePrevious();
                @elseif ($readDirection === 'ltr')
                    navigateNext();
                @endif
            });

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
                        navigateNext();
                    @elseif ($readDirection === 'ltr')
                        navigatePrevious();
                    @endif
                } else if (e.keyCode === 39 || e.keyCode === 68) {
                    // right arrow or d
                    @if ($readDirection === 'rtl')
                        navigatePrevious();
                    @elseif ($readDirection === 'ltr')
                        navigateNext();
                    @endif
                }
            });

            $('#reader-image').click(function (eventData) {
                const x = eventData.offsetX;
                const width = $('#reader-image').width();

                if (x < (width / 2)) {
                    // left side click
                    @if ($readDirection === 'rtl')
                        navigateNext();
                    @elseif ($readDirection === 'ltr')
                        navigatePrevious();
                    @endif
                } else {
                    // right side click
                    @if ($readDirection === 'rtl')
                        navigatePrevious();
                    @elseif ($readDirection === 'ltr')
                        navigateNext();
                    @endif
                }
            });
        });
    </script>
@endsection
