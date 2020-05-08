@if (! empty($manga->mu_id))
    <h5>
        External Links
    </h5>

    <div class="row">
        <div class="col-6 col-sm-4 col-md-3">
            <img src="{{ asset('assets/mu.png') }}">
            <a href="https://mangaupdates.com/series.html?id={{ $manga->mu_id }}">MangaUpdates</a>
        </div>
    </div>
@endif