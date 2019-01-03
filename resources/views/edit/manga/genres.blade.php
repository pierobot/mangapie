@extends ('edit.manga.layout')

@section ('side-top-menu')
    <ul class="nav nav-pills d-md-flex flex-md-column">
        <li class="nav-item">
            <a class="nav-link" href="{{ action('MangaEditController@mangaupdates', [$manga]) }}">Mangaupdates</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ action('MangaEditController@names', [$manga]) }}">Names</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ action('MangaEditController@description', [$manga]) }}">Description</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ action('MangaEditController@authors', [$manga]) }}">Authors</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ action('MangaEditController@artists', [$manga]) }}">Artists</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ action('MangaEditController@genres', [$manga]) }}">Genres</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ action('MangaEditController@covers', [$manga]) }}">Covers</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ action('MangaEditController@type', [$manga]) }}">Type</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ action('MangaEditController@year', [$manga]) }}">Year</a>
        </li>
    </ul>
@endsection

@section ('tab-content')
    <div class="card">
        <div class="card-header">
            Genre(s)
        </div>
        <div class="card-body">
            @php ($genres = \App\Genre::all())

            {{ Form::open(['action' => 'MangaEditController@putGenres', 'method' => 'put']) }}
            {{ Form::hidden('manga_id', $manga->id) }}

            <div class="row">
                @foreach ($genres as $genre)
                    @php ($active = ! empty($manga->genreReferences->where('genre_id', $genre->id)->count()))

                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="{{ $genre->id }}" name="genres[]" value="{{ $genre->id }}" @if ($active) checked="checked" data-active="yes" @else data-active="no" @endif>
                            <label class="custom-control-label" for="{{ $genre->id }}">{{ $genre->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row mt-3 d-flex justify-content-center">
                <div class="col-12 col-md-4">
                    <button class="btn btn-primary form-control">
                        <span class="fa fa-check"></span>

                        &nbsp;Update
                    </button>
                </div>
            </div>

            {{ Form::close() }}
        </div>
    </div>
@endsection

@section ('scripts')
@endsection