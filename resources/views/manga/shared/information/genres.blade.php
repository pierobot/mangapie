<h5>Genres</h5>

@if (! empty($manga->genreReferences))
    <div class="row">
        @foreach ($manga->genreReferences as $genreReference)
            @php
                $genre = $genreReference->genre;
            @endphp
            <div class="col-6 col-sm-4 col-md-3 col-lg-3">
                <a href="{{ \URL::action('GenreController@index', [$genre->name]) }}">
                    {{ $genre->name }}
                </a>
            </div>
        @endforeach
    </div>
@else
    Unable to find genres.
@endif
