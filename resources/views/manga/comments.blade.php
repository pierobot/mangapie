@extends ('manga.layout')

@section ('navtabs-content')
    <div class="d-block d-sm-none">
        <ul class="nav nav-tabs">
            <li class="nav-item"><a class="nav-link" href="{{ URL::action('MangaController@index', [$manga]) }}"><span class="fa fa-info"></span>&nbsp;Info</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ URL::action('MangaController@files', [$manga]) }}"><span class="fa fa-folder-open"></span>&nbsp;Files</a></li>
            <li class="nav-item active"><a class="nav-link"><span class="fa fa-comment"></span>&nbsp;Comments</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="comments-content-xs">
                @component ('manga.components.comments', [
                    'manga' => $manga,
                ])
                @endcomponent
            </div>
        </div>
    </div>

    <div class="d-none d-sm-block">
        <ul class="nav nav-tabs">
            <li class="nav-item"><a class="nav-link" href="{{ URL::action('MangaController@files', [$manga]) }}"><span class="fa fa-folder-open"></span>&nbsp;&nbsp;Files</a></li>
            <li class="nav-item active"><a class="nav-link"><span class="fa fa-comment"></span>&nbsp;&nbsp;Comments</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="comments-content">
                @component ('manga.components.comments', [
                    'manga' => $manga,
                ])
                @endcomponent
            </div>
        </div>
    </div>
@endsection
