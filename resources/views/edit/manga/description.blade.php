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
            <a class="nav-link active" href="{{ action('MangaEditController@description', [$manga]) }}">Description</a>
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
            Description
        </div>
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-12">
                    {{ Form::open(['action' => 'MangaEditController@patchDescription', 'method' => 'patch']) }}
                    {{ Form::hidden('manga_id', $manga->id) }}

                    {{ Form::textarea('description', ! empty($manga->description) ? $manga->description : '', ['class' => 'form-control', 'placeholder' => 'Enter description...']) }}
                    <div class="form-row justify-content-center mt-3">
                        <div class="col-12 col-md-4">
                            <button class="btn btn-primary form-control">
                                <span class="fa fa-check"></span>

                                &nbsp;Set
                            </button>
                        </div>
                    </div>

                    {{ Form::close() }}
                </div>

                <div class="col-12 col-md-4 mt-0">
                    @if (! empty($manga->description))
                        {{ Form::open(['action' => 'MangaEditController@deleteDescription', 'method' => 'delete']) }}
                        {{ Form::hidden('manga_id', $manga->id) }}
                        <button class="btn btn-danger form-control">
                            <span class="fa fa-times"></span>

                            &nbsp;Delete
                        </button>
                        {{ Form::close() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection