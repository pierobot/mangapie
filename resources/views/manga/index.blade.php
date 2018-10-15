@extends ('manga.layout')

@section ('lower-card')
    <div class="d-flex d-sm-none">
        <div class="card w-100">
            <div class="card-header">
                <ul class="nav nav-pills card-header-pills">
                    <li class="nav-item"><a class="nav-link active" href="#">Information</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ URL::action('MangaController@files', [$manga]) }}">Files</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ URL::action('MangaController@comments', [$manga]) }}">Comments</a></li>
                </ul>
            </div>
            <div class="card-body">
                @component ('manga.components.information', ['manga' => $manga, 'user' => $user])
                @endcomponent
            </div>
        </div>
    </div>

    <div class="d-none d-sm-flex">
        <div class="card w-100">
            <div class="card-header">
                <ul class="nav nav-pills card-header-pills">
                    <li class="nav-item"><a class="nav-link active" href="{{ URL::action('MangaController@files', [$manga]) }}">Files</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ URL::action('MangaController@comments', [$manga]) }}">Comments</a></li>
                </ul>
            </div>
            <div class="card-body">
                @component ('manga.components.files', ['manga' => $manga, 'user' => $user])
                @endcomponent
            </div>
        </div>
    </div>
@endsection
