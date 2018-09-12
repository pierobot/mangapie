@extends ('edit.manga.layout')

@section ('side-top-menu')
    @component ('edit.manga.components.side-top-menu', [
        'manga' => $manga,
        'active' => 'Genre(s)',
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
            Genre(s)
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    @php
                        $genres = \App\Genre::all();
                    @endphp

                    {{ Form::open(['action' => 'MangaEditController@putGenres', 'method' => 'put']) }}
                    {{ Form::hidden('manga_id', $manga->id) }}
                    <div class="form-group">
                        <div class="form-row">
                            @foreach ($genres as $genre)
                                @php
                                    $active = ! empty($manga->genreReferences->where('genre_id', $genre->id)->count());
                                @endphp

                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="{{ $genre->id }}" name="genres[]" value="{{ $genre->id }}" @if ($active) checked="checked" data-active="yes" @else data-active="no" @endif>
                                        <label class="custom-control-label" for="{{ $genre->id }}">{{ $genre->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12">
                        {{ Form::submit('Update', ['class' => 'btn btn-primary form-control']) }}
                    </div>

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section ('scripts')
@endsection