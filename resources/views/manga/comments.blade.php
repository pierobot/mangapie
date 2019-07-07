@extends ('manga.layout')

@section ('lower-card')
    <div class="d-flex d-sm-none flex-column">
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-pills justify-content-center">
                    <li class="nav-item"><a class="nav-link" href="{{ URL::action('MangaController@index', [$manga]) }}">Information</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ URL::action('MangaController@files', [$manga]) }}">Files</a></li>
                    <li class="nav-item"><a class="nav-link active" href="#">Comments</a></li>
                </ul>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                @include ('manga.shared.comments')
            </div>
        </div>
    </div>

    <div class="d-none d-sm-flex flex-column">
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link" href="{{ URL::action('MangaController@files', [$manga]) }}">Files</a></li>
                    <li class="nav-item"><a class="nav-link active" href="#">Comments</a></li>
                </ul>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                @include ('manga.shared.comments')
            </div>
        </div>
    </div>
@endsection
