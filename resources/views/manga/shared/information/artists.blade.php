<h5>
    Artist(s)
    @if ($user->admin || $user->maintainer)
        <a href="{{ action('MangaEditController@artists', [$manga]) }}">
            <span class="fa fa-edit"></span>
        </a>
    @endif
</h5>

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
