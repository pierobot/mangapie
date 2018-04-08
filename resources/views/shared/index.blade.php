<div class="row">
    @foreach ($manga_list as $manga)
        <div class="col-lg-2 col-sm-3 col-xs-6 text-center thumbnail">
            <div>
                <a href="{{ URL::action('MangaController@index', [$manga->getId()]) }}">
                    {{ Html::image(URL::action('ThumbnailController@smallDefault', [$manga->getId()])) }}
                </a>
            </div>
            <h4 title="{{ $manga->getName() }}"><a href="{{ URL::action('MangaController@index', [$manga->getId()]) }}">{{ $manga->getName() }}</a></h4>
        </div>
    @endforeach
</div>

<div class="text-center">
    {{ $manga_list->render() }}
</div>