<h5>Artist(s)</h5>

@if (! empty($manga->artistReferences))
    <div class="row">
        @foreach ($manga->artistReferences as $artistReference)
            @php
                $artist = $artistReference->artist;
            @endphp
            <div class="col-6 col-sm-4 col-md-3 col-lg-3">
                <a href="{{ \URL::action('PersonController@index', [$artist]) }}">
                    {{ $artist->name }}
                </a>
            </div>
        @endforeach
    </div>
@else
    Unable to find artists.
@endif
