@extends ('layout')

@section ('title')
    Reader &middot; {{ $archive->name }}
@endsection

@section ('content')
    @include ('shared.errors')

    <div class="container-fluid ml-0 mr-0 pl-0 pr-0">
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
            <div class="reader-image-container">
                <img id="reader-image" class="mw-100 h-auto d-block mx-auto" src="{{ URL::action('ReaderController@image', [$manga, $archive, $page]) }}">
            </div>
        @endif

        @if ($preload !== false)
            <div id="preload" style="display: none;">
                @foreach ($preload as $index => $preload_url)
                    <img id="{{ $page + 1 + $index }}" data-src="{{ $preload_url }}">
                @endforeach
            </div>
        @endif
    </div>

    <div class="container-fluid">
        <div class="row justify-content-between mt-2">
            <div class="col text-left mt-auto pr-0">
                @if ($readDirection === 'ltr')
                    <a @if (empty($previousArchiveUrl)) class="btn btn-secondary disabled" @else class="btn btn-primary" href="{{ ! empty($previousArchiveUrl) ? $previousArchiveUrl : '#' }}" @endif id="a-left">
                        <span class="fa fa-fast-backward"></span>
                    </a>
                @else
                    <a @if (empty($nextArchiveUrl)) class="btn btn-secondary disabled" @else class="btn btn-primary" href="{{ ! empty($nextArchiveUrl) ? $nextArchiveUrl : '#' }}" @endif id="a-left">
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
                    <a @if (empty($nextArchiveUrl)) class="btn btn-secondary disabled" @else class="btn btn-primary" href="{{ ! empty($nextArchiveUrl) ? $nextArchiveUrl : '#' }}" @endif id="a-right">
                        <span class="fa fa-fast-forward"></span>
                    </a>
                @else
                    <a @if (empty($previousArchiveUrl)) class="btn btn-secondary disabled" @else class="btn btn-primary" href="{{ ! empty($previousArchiveUrl) ? $previousArchiveUrl : '#' }}" @endif id="a-right">
                        <span class="fa fa-fast-forward"></span>
                    </a>
                @endif
            </div>
        </div>


        <div class="row mt-3 mb-3">
            <div class="col-12 text-center">
                @php
                    $volCh = App\Scanner::getVolumesAndChapters($archive->name);
                    // If there is no volume or chapter in the name, or if the parsing failed
                    // then just use the archive name :shrug:
                    if (empty($volCh) || empty($volCh[0]))
                        $nameVolCh = $archive->name;
                    else
                        $nameVolCh = $volCh[0][0];
                @endphp

                <h4><a href="{{ URL::action('MangaController@index', [$manga]) }}">{{ $manga->name }}</a> &mdash; {{ $nameVolCh }}</h4>
            </div>

            @admin
            <div class="col-12 text-center">
                <small>{{ $archive->name }}</small>
            </div>
            @endadmin
        </div>
    </div>
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
        const g_baseImageUrl = `{{ URL::to('/image') }}/${g_mangaId}/${g_archiveId}/`;
        const g_baseReaderUrl = `{{ URL::to('/reader') }}/${g_mangaId}/${g_archiveId}/`;

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
            // mangapie.history.pushReplace(g_readerKey, g_baseReaderUrl + page);
            mangapie.history.replace(g_readerKey, g_baseReaderUrl + page);

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
            // mangapie.history.pushReplace(g_readerKey, g_baseReaderUrl + page);
            mangapie.history.replace(g_readerKey, g_baseReaderUrl + page);

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
            axios.put("{{ URL::to('reader/history') }}", {
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

            // $(window).on("popstate", function (event) {
            //     if (event.originalEvent.state) {
            //         const data = mangapie.sessionStorage.find(g_readerKey);
            //
            //         navigatePrevious();
            //     }
            // });

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

            let container = $('.container');
            container.attr('original-max-width', container.css('max-width'));

            $('#fit-image-screen').click(function (eventData) {
                container.css('max-width', '100%');
            });

            $('#fit-image-container').click(function (eventData) {
                container.css('max-width', container.attr('original-max-width'));
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
