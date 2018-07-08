@extends ('manga.layout')

@section ('navtabs-content')
    <div class="visible-xs">
        <ul class="nav nav-tabs">
            <li><a href="{{ URL::action('MangaController@index', [$manga]) }}"><span class="glyphicon glyphicon-info-sign"></span>&nbsp;Info</a></li>
            <li class="active"><a><span class="glyphicon glyphicon-folder-open"></span>&nbsp;Files</a></li>
            <li><a href="{{ URL::action('MangaController@comments', [$manga]) }}"><span class="glyphicon glyphicon-comment"></span>&nbsp;Comments</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="files-content-xs">
                <div class="row">
                    <div class="col-xs-12">
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

    <div class="hidden-xs">
        <ul class="nav nav-tabs">
            <li class="active"><a><span class="glyphicon glyphicon-folder-open"></span>&nbsp;&nbsp;Files</a></li>
            <li><a href="{{ URL::action('MangaController@comments', [$manga]) }}"><span class="glyphicon glyphicon-comment"></span>&nbsp;&nbsp;Comments</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="files-content">
                <div class="row">
                    <div class="col-xs-12">
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
