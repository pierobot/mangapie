@extends ('edit.manga.layout')

@section ('side-top-menu')
    @component ('edit.manga.components.side-top-menu', [
        'manga' => $manga,
        'active' => 'Mangaupdates',
        'items' => [
            ['title' => 'Mangaupdates', 'icon' => 'database', 'action' => 'MangaEditController@mangaupdates'],
            ['title' => 'Name(s)', 'icon' => 'globe-americas', 'action' => 'MangaEditController@names'],
            ['title' => 'Description', 'icon' => 'file-text', 'action' => 'MangaEditController@description'],
            ['title' => 'Author(s)', 'icon' => 'pencil', 'action' => 'MangaEditController@authors'],
            ['title' => 'Artist(s)', 'icon' => 'brush', 'action' => 'MangaEditController@artists'],
            ['title' => 'Genre(s)', 'icon' => 'tags', 'action' => 'MangaEditController@genres'],
            ['title' => 'Cover', 'icon' => 'file-image', 'action' => 'MangaEditController@covers'],
            ['title' => 'Type', 'icon' => 'list', 'action' => 'MangaEditController@type'],
            ['title' => 'Year', 'icon' => 'calendar', 'action' => 'MangaEditController@year']
        ]
    ])
    @endcomponent
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

                                        <span class="d-none d-md-inline-block">
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
