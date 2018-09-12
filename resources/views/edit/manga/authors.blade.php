@extends ('edit.manga.layout')

@section ('side-top-menu')
    @component ('edit.manga.components.side-top-menu', [
        'manga' => $manga,
        'active' => 'Author(s)',
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

                                <span class="d-none d-md-inline-block">
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

                                                <span class="d-none d-md-inline-block">
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