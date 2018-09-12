@extends ('manga.layout')

@section ('navtabs-content')
    <div class="d-block d-sm-none">
        <ul class="nav nav-tabs">
            <li class="nav-item active"><a class="nav-link"><span class="fa fa-info"></span>&nbsp;Info</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ URL::action('MangaController@files', [$manga->id]) }}"><span class="fa fa-folder-open"></span>&nbsp;Files</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ URL::action('MangaController@comments', [$manga->id]) }}"><span class="fa fa-comment"></span>&nbsp;Comments</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="information-content-xs">
                <div class="row">
                    <div class="col-12">
                        @component ('manga.components.information',[
                            'user' => $user,
                            'manga' => $manga,
                        ])
                        @endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-none d-md-block">
        <ul class="nav nav-tabs">
            <li class="active nav-item"><a class="nav-link"><span class="fa fa-folder-open"></span>&nbsp;&nbsp;Files</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ URL::action('MangaController@comments', [$manga->id]) }}"><span class="fa fa-comment"></span>&nbsp;&nbsp;Comments</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="files-content">
                <div class="row">
                    <div class="col-12">
                        @component ('manga.components.files', [
                            'user' => $user,
                            'manga' => $manga,
                            'sort' => $sort,
                        ])
                        @endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
