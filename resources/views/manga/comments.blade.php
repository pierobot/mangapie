@extends ('manga.layout')

@section ('lower-card')
    <div class="d-flex d-sm-none">
        <div class="card w-100">
            <div class="card-header">
                <ul class="nav nav-pills card-header-pills">
                    <li class="nav-item"><a class="nav-link" href="{{ URL::action('MangaController@index', [$manga]) }}">Information</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ URL::action('MangaController@files', [$manga]) }}">Files</a></li>
                    <li class="nav-item"><a class="nav-link active" href="#">Comments</a></li>
                </ul>
            </div>

            <div class="card-body">
                @component ('manga.components.comments', ['manga' => $manga])
                @endcomponent
            </div>
        </div>
    </div>

    <div class="d-none d-sm-flex">
        <div class="card w-100">
            <div class="card-header">
                <ul class="nav nav-pills card-header-pills">
                    <li class="nav-item"><a class="nav-link" href="{{ URL::action('MangaController@files', [$manga]) }}">Files</a></li>
                    <li class="nav-item"><a class="nav-link active" href="#">Comments</a></li>
                </ul>
            </div>

            <div class="card-body">
                @component ('manga.components.comments', ['manga' => $manga])
                @endcomponent
            </div>
        </div>
    </div>
@endsection
