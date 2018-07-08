@extends ('manga.layout')

@section ('navtabs-content')
    <div class="visible-xs">
        <ul class="nav nav-tabs">
            <li><a href="{{ URL::action('MangaController@index', [$manga]) }}"><span class="glyphicon glyphicon-info-sign"></span>&nbsp;Info</a></li>
            <li><a href="{{ URL::action('MangaController@files', [$manga]) }}"><span class="glyphicon glyphicon-folder-open"></span>&nbsp;Files</a></li>
            <li class="active"><a href="{{ URL::action('MangaController@comments', [$manga]) }}"><span class="glyphicon glyphicon-comment"></span>&nbsp;Comments</a></li>
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

    <div class="hidden-xs">
        <ul class="nav nav-tabs">
            <li><a href="{{ URL::action('MangaController@files', [$manga]) }}"><span class="glyphicon glyphicon-folder-open"></span>&nbsp;&nbsp;Files</a></li>
            <li class="active"><a><span class="glyphicon glyphicon-comment"></span>&nbsp;&nbsp;Comments</a></li>
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
