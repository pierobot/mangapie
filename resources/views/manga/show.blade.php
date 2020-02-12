@extends ('manga.layout')

@section ('lower-card')

    <div class="d-flex flex-column d-sm-none">
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-pills justify-content-center">
                    <li class="nav-item"><a class="nav-link active" href="#">Information</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ URL::action('MangaController@files', [$manga]) }}">Files</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ URL::action('MangaController@comments', [$manga]) }}">Comments</a></li>
                </ul>
            </div>

            <div class="col-12 mt-3">
                @include ('manga.shared.information')
            </div>
        </div>
    </div>

    <div class="d-none d-sm-flex flex-sm-column">
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="{{ URL::action('MangaController@files', [$manga]) }}">Files</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ URL::action('MangaController@comments', [$manga]) }}">Comments</a></li>
                </ul>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                @include ('manga.shared.files')
            </div>
        </div>

    </div>
@endsection
