@extends ('edit.manga.layout')

@section ('side-top-menu')
    @component ('edit.manga.components.side-top-menu', [
        'manga' => $manga,
        'active' => 'Cover',
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
            Cover
        </div>
        <div class="card-body">
            <div class="row">
                @if (! empty($archives))
                    @foreach ($archives as $archive)
                        <div class="col-12 mb-2">
                            <div class="card">
                                <div class="card-header">
                                    <a href="#dropdown-{{ $archive->id }}" data-toggle="collapse" data-target="#dropdown-{{ $archive->id }}">
                                        <h5 class="m-0">{{ $archive->name }}</h5>
                                    </a>
                                </div>
                                <div class="card-body collapse cover-collapse" id="dropdown-{{ $archive->id }}">
                                    <div class="row">
                                        {{-- 8 pages should be enough, right? :| --}}
                                        @for ($i = 1; $i <= 8; $i++)
                                            <div class="col-6 col-md-3 mb-2">
                                                <div class="card text-center">
                                                    <img class="card-img-top" src="{{ URL::action('CoverController@small', [$manga, $archive, $i]) }}">

                                                    <div class="card-footer">
                                                        {{ Form::open(['action' => 'CoverController@put', 'method' => 'put', 'class' => 'm-0']) }}
                                                        {{ Form::hidden('manga_id', $manga->id) }}
                                                        {{ Form::hidden('archive_id', $archive->id) }}
                                                        {{ Form::hidden('page', $i) }}

                                                        <button class="btn btn-primary form-control" type="submit">
                                                            <span class="fa fa-check"></span>

                                                            <span class="d-none d-lg-inline-flex">
                                                                &nbsp;Set
                                                            </span>
                                                        </button>

                                                        {{ Form::close() }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection