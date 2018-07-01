<div class="row">
    @if (isset($header))
        <h3 class="text-center">
            <b>{{ $header }}</b>
        </h3>
    @endif
</div>


<div class="row">
    @if (isset($manga_list))
        @foreach ($manga_list as $manga)
            <div class="col-lg-2 col-sm-4 col-xs-6 text-center thumbnail">
                <div>
                    <a href="{{ URL::action('MangaController@index', [$manga->getId()]) }}">
                       {{ Html::image(URL::action('CoverController@smallDefault', [$manga->getId()])) }}
                    </a>
                </div>
                <h4 title="{{ $manga->getName() }}"><a href="{{ URL::action('MangaController@index', [$manga->getId()]) }}">{{ $manga->getName() }}</a></h4>
            </div>
        @endforeach
    @else
    @endif
</div>

<div class="text-center">
    @if (isset($manga_list))
        {{ $manga_list->render() }}
    @endif
</div>