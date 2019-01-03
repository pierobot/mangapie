@extends ('layout')

@section ('title')
    Reader &middot; {{ $archive->name }}
@endsection

@section ('header-contents')
    <div class="navbar navbar-dark bg-dark sticky-top">
        {{--This navbar and the main one do not have the same height, and I do not want to set a max-height.--}}
        {{--As a result, the quick search controls become slightly visible below this navbar.--}}
        {{--Adding a top margin somewhat solves this.--}}
        {{--TODO: There should be a better solution to this, right?--}}
        <div class="container mt-1">
            <div class="d-flex d-md-none flex-column w-66">
                <a class="text-truncate" href="{{ action('MangaController@index', [$manga]) }}">{{ $manga->name }}</a>
                <small class="text-muted text-truncate">{{ $archive->name }}</small>
            </div>
            <div class="d-none d-md-flex flex-column w-75">
                <a class="text-truncate" href="{{ action('MangaController@index', [$manga]) }}">{{ $manga->name }}</a>
                <small class="text-muted text-truncate">{{ $archive->name }}</small>
            </div>

            <button class="btn btn-secondary navbar-toggler ml-auto mr-2 disabled" disabled data-toggle="collapse" data-target="#navigation-collapse" title="Open reader settings">
                <span class="fa fa-cog"></span>
            </button>

            <button class="btn btn-secondary navbar-toggler" onclick="toggleMainNavbar();">
                <span class="fa fa-toggle-down" id="navbar-toggler"></span>
            </button>

            <div class="collapse navbar-collapse mt-2" id="navigation-collapse">
                <ul class="nav navbar-nav text-right">
                    <li class="nav-item">
                        <strong>Reading direction</strong>
                    </li>
                    <li class="nav-item">
                        <div class="form-inline d-inline-flex">
                            <div class="form-row">
                                <div class="col-4">
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="direction-rtl" name="direction" value="rtl">
                                        <label class="custom-control-label" for="direction-rtl">Left</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="direction-ltr" name="direction" value="ltr">
                                        <label class="custom-control-label" for="direction-ltr">Right</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="direction-vrt" name="direction" value="vrt">
                                        <label class="custom-control-label" for="direction-vrt">Vertical</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section ('content')
    @include ('shared.errors')

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
        <div class="reader-image-container pt-3">
            <img id="reader-image" class="w-100 h-auto" src="{{ URL::action('ReaderController@image', [$manga, $archive, $page]) }}">
        </div>

        <div class="row justify-content-between mt-2">
            <div class="col text-left mt-auto pr-0">
                @if ($readDirection === 'ltr')
                    <a @if (empty($previousArchiveUrl)) class="btn btn-secondary disabled" @else class="btn btn-secondary" href="{{ ! empty($previousArchiveUrl) ? $previousArchiveUrl : '#' }}" @endif id="a-left">
                        <span class="fa fa-fast-backward"></span>
                    </a>
                @else
                    <a @if (empty($nextArchiveUrl)) class="btn btn-secondary disabled" @else class="btn btn-secondary" href="{{ ! empty($nextArchiveUrl) ? $nextArchiveUrl : '#' }}" @endif id="a-left">
                        <span class="fa fa-fast-backward"></span>
                    </a>
                @endif
            </div>

            <div class="col-8 col-md-9">
                <input id="page-slider" type="text"
                       data-min="1"
                       data-max="{{ $pageCount }}"
                       data-step="1"
                       data-from="{{ $page }}"
                       data-skin="round"
                       data-type="single">
            </div>

            <div class="col text-right mt-auto pl-0">
                @if ($readDirection === 'ltr')
                    <a @if (empty($nextArchiveUrl)) class="btn btn-secondary disabled" @else class="btn btn-secondary" href="{{ ! empty($nextArchiveUrl) ? $nextArchiveUrl : '#' }}" @endif id="a-right">
                        <span class="fa fa-fast-forward"></span>
                    </a>
                @else
                    <a @if (empty($previousArchiveUrl)) class="btn btn-secondary disabled" @else class="btn btn-secondary" href="{{ ! empty($previousArchiveUrl) ? $previousArchiveUrl : '#' }}" @endif id="a-right">
                        <span class="fa fa-fast-forward"></span>
                    </a>
                @endif
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

@section ('scripts')
    <script type="text/javascript">
        const g_mangaId = Number("{{ $manga->id }}");
        const g_archiveId = Number("{{ $archive->id }}");
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
        const g_directionKey = `direction-${g_mangaId}-${g_archiveId}`;

        let g_readDirection = "{{ $readDirection }}";

        let g_slider = undefined;

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
            let readerData = mangapie.sessionStorage.find(g_readerKey);
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

            mangapie.sessionStorage.put(g_readerKey, readerData);

            // update the history stack and update the current URL
            mangapie.history.pushReplace(g_readerKey, g_baseReaderUrl + page);

            // update the current image
            $("#reader-image").attr("src", g_baseImageUrl + page);
            $("html, body").animate({scrollTop: '0px'}, 150);

            updateNavigationControls(g_readDirection, page);

            updateLastReadPage(g_mangaId, g_archiveId, page);
        }

        /**
         * Performs navigation to the next page.
         *
         * @return void
         */
        function navigateNext() {
            let readerData = mangapie.sessionStorage.find(g_readerKey);
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
            mangapie.sessionStorage.put(g_readerKey, readerData);

            // update the history stack and update the current URL
            mangapie.history.pushReplace(g_readerKey, g_baseReaderUrl + page);

            // update the current image
            $("#reader-image").attr("src", g_baseImageUrl + page);
            $("html, body").animate({scrollTop: '0px'}, 150);

            updateNavigationControls(g_readDirection, page);

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
            g_slider.update({
                from: page
            });
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

        function toggleMainNavbar() {
            $(".navbar:first").toggleClass("d-none");
            $("#navbar-toggler").toggleClass("fa-toggle-down fa-toggle-up");
        }

        $(function () {
            preloadAll();

            // initialize the page slider
            let slider = $("#page-slider");
            slider.ionRangeSlider({
                onFinish: function (slider) {
                    window.location = g_baseReaderUrl + slider.from;
                }
            });

            g_slider = slider.data("ionRangeSlider");

            // initialize the reader session storage
            const storageResult = mangapie.sessionStorage.put(g_readerKey, {
                page: g_page,
                page_count: g_pageCount
            });

            updateLastReadPage(g_mangaId, g_archiveId, g_page);

            $(window).on("popstate", function (event) {
                if (event.originalEvent.state) {
                    const data = mangapie.sessionStorage.find(g_readerKey);

                    navigatePrevious();
                }
            });

            $("#a-left").on("click", function (e) {
                e.preventDefault();

                if (g_readDirection === "ltr") {
                    navigatePrevious();
                } else if (g_readDirection === "rtl") {
                    navigateNext();
                }
            });

            $("#a-right").on("click", function (e) {
                e.preventDefault();

                if (g_readDirection === "ltr") {
                    navigateNext();
                } else if (g_readDirection === "rtl") {
                    navigatePrevious();
                }
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
                    if (g_readDirection === "ltr") {
                        navigatePrevious();
                    } else if (g_readDirection === "rtl") {
                        navigateNext();
                    }
                } else if (e.keyCode === 39 || e.keyCode === 68) {
                    // right arrow or d
                    if (g_readDirection === "ltr") {
                        navigateNext();
                    } else if (g_readDirection === "rtl") {
                        navigatePrevious();
                    }
                }
            });

            $('#reader-image').click(function (eventData) {
                const x = eventData.offsetX;
                const width = $('#reader-image').width();

                if (x < (width / 2)) {
                    // left side click
                    if (g_readDirection === "ltr") {
                        navigatePrevious();
                    } else if (g_readDirection === "rtl") {
                        navigateNext();
                    }
                } else {
                    // right side click
                    if (g_readDirection === "ltr") {
                        navigateNext();
                    } else if (g_readDirection === "rtl") {
                        navigatePrevious();
                    }
                }
            });

            // function adjustDirection(direction) {
            //     if (direction === "vrt") {
            //         $(".reader-image-container").attr("data-direction", "vrt");
            //     }
            // }
            //
            // $("#direction-ltr").click(function () {
            //     g_readDirection = "ltr";
            //     mangapie.sessionStorage.put(g_directionKey, g_readDirection);
            //
            //     adjustDirection(g_readDirection);
            // });
            //
            // $("#direction-rtl").click(function () {
            //     g_readDirection = "rtl";
            //     mangapie.sessionStorage.put(g_directionKey, g_readDirection);
            //
            //     adjustDirection(g_readDirection);
            // });
            //
            // $("#direction-vrt").click(function () {
            //     g_readDirection = "vrt";
            //     mangapie.sessionStorage.put(g_directionKey, g_readDirection);
            //
            //     adjustDirection(g_readDirection);
            // });
        });
    </script>
@endsection
