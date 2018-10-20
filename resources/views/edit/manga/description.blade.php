@extends ('edit.manga.layout')

@section ('side-top-menu')
    @component ('edit.manga.components.side-top-menu', [
        'manga' => $manga,
        'active' => 'Description',
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