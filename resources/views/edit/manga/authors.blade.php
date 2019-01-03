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
            <a class="nav-link active" href="{{ action('MangaEditController@authors', [$manga]) }}">Authors</a>
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
            Author(s)
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-6">
                    {{ Form::open(['action' => 'MangaEditController@postAuthor']) }}
                    {{ Form::hidden('manga_id', $manga->id) }}
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Name</span>
                        </div>
                        <input class="form-control" name="name" type="text">
                        <div class="input-group-append">
                            <button class="btn btn-primary form-control" type="submit">
                                <span class="fa fa-plus"></span>

                                <span class="d-none d-lg-inline-flex">
                                    &nbsp;Add
                                </span>
                            </button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>

                <div class="col-12 col-md-6">
                    @if (! empty($manga->authorReferences))
                        <div class="row">
                            @foreach ($manga->authorReferences as $authorReference)
                                <div class="col-12">
                                    {{ Form::open(['action' => 'MangaEditController@deleteAuthor', 'method' => 'delete']) }}
                                    {{ Form::hidden('manga_id', $manga->id) }}
                                    {{ Form::hidden('author_reference_id', $authorReference->id) }}

                                    <div class="input-group form-group">
                                        <div class="input-group-text form-control">
                                            {{ $authorReference->author->name  }}
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
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection