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
            <a class="nav-link" href="{{ action('MangaEditController@genres', [$manga]) }}">Genres</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ action('MangaEditController@covers', [$manga]) }}">Covers</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ action('MangaEditController@type', [$manga]) }}">Type</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ action('MangaEditController@year', [$manga]) }}">Year</a>
        </li>
    </ul>
@endsection

@section ('tab-content')
    <div class="card">
        <div class="card-header">
            Type
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-6">
                    {{ Form::open(['action' => 'MangaEditController@patchType', 'method' => 'patch']) }}
                    {{ Form::hidden('manga_id', $manga->id) }}

                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                Type
                            </span>
                        </div>
                        <select class="custom-select form-select" id="type" name="type">
                            <option @if (! empty($manga->type) && $manga->type === "Manga") selected="selected" @endif value="Manga">Manga</option>
                            <option @if (! empty($manga->type) && $manga->type === "Manwha") selected="selected" @endif value="Manwha">Manwha</option>
                            <option @if (! empty($manga->type) && $manga->type === "Doujinshi") selected="selected" @endif value="Doujinshi">Doujinshi</option>
                        </select>
                        <div class="input-group-append">
                            <button class="btn btn-primary form-control" type="submit">
                                <span class="fa fa-check"></span>

                                <span class="d-none d-lg-inline-flex">
                                    &nbsp;Set
                                </span>
                            </button>
                        </div>
                    </div>

                    {{ Form::close() }}
                </div>

                <div class="col-12 col-md-6">
                    @if (! empty($manga->type))
                        {{ Form::open(['action' => 'MangaEditController@deleteType', 'method' => 'delete']) }}
                        {{ Form::hidden('manga_id', $manga->id) }}

                        <div class="input-group form-group">
                            <div class="input-group-text form-control">
                                {{ $manga->type }}
                            </div>
                            <div class="input-group-append">
                                <button class="btn btn-danger form-control" type="submit">
                                    <span class="fa fa-times"></span>

                                    <span class="d-none d-lg-inline-flex">
                                        &nbsp;Remove
                                    </span>
                                </button>
                            </div>
                        </div>

                        {{ Form::close() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection