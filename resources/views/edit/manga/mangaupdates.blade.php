@extends ('edit.manga.layout')

@section ('side-top-menu')
    <ul class="nav nav-pills d-md-flex flex-md-column">
        <li class="nav-item">
            <a class="nav-link active" href="{{ action('MangaEditController@mangaupdates', [$manga]) }}">Mangaupdates</a>
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
            <a class="nav-link" href="{{ action('MangaEditController@type', [$manga]) }}">Type</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ action('MangaEditController@year', [$manga]) }}">Year</a>
        </li>
    </ul>
@endsection

@section ('tab-content')
    <div class="tab-content">
        <div class="tab-pane active" id="MangaUpdates">
            <div class="card">
                <div class="card-header">
                    Mangaupdates
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            {{ Form::open(['action' => 'MangaEditController@putAutofill', 'method' => 'put']) }}
                            {{ Form::hidden('manga_id', $manga->id) }}

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        Id
                                    </div>
                                </div>

                                <input class="form-control" id="mu_id" name="mu_id" value="@if (! empty($manga->mu_id)) {{ $manga->mu_id }} @endif">

                                <div class="input-group-append">
                                    <button class="btn btn-primary form-control" type="submit">
                                        <span class="fa fa-check"></span>

                                        <span class="d-none d-lg-inline-flex">
                                            &nbsp;Update
                                        </span>
                                    </button>
                                </div>
                            </div>

                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
