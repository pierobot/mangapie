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
            <a class="nav-link active" href="{{ action('MangaEditController@covers', [$manga]) }}">Covers</a>
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
                                        @php($pageLimit = $archive->getPageCount() < 8 ? $archive->getPageCount() : 8)
                                        @for ($i = 1; $i <= $pageLimit; $i++)
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