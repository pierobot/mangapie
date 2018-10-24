@extends ('edit.manga.layout')

@section ('side-top-menu')
    @component ('edit.manga.components.side-top-menu', [
        'manga' => $manga,
        'active' => 'Type',
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