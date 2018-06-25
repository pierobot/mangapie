@extends ('user.layout')

@section ('tab-content')
    <div class="tab-pane">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">
                        <h4 class="text-center"><b>Recently Favorited</b></h4>
                    </div>

                    <div class="col-xs-12">
                        @if (! empty($recentFavorites))
                            <div class="row">
                                @foreach ($recentFavorites as $favorite)
                                    <div class="col-xs-6 col-sm-4 col-md-2 text-center thumbnail">
                                        <div>
                                            <a href="{{ URL::action('MangaController@index', [$favorite->manga->getId()]) }}">
                                                {{ Html::image(URL::action('ThumbnailController@smallDefault', [$favorite->manga->getId()])) }}
                                            </a>
                                        </div>
                                        <h4 title="{{ $favorite->manga->getName() }}">
                                            <a href="{{ URL::action('MangaController@index', [$favorite->manga->getId()]) }}">
                                                {{ $favorite->manga->getName() }}
                                            </a>
                                        </h4>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-xs-12">
                        <h4 class="text-center"><b>Recently Read</b></h4>
                    </div>

                    <div class="col-xs-12">
                        @if (! empty($recentReads))
                            <div class="row">
                                @foreach ($recentReads as $read)
                                    <div class="col-xs-6 col-sm-4 col-md-2 text-center thumbnail">
                                        <div>
                                            <a href="{{ URL::action('MangaController@index', [$read->manga->getId()]) }}">
                                                {{ Html::image(URL::action('ThumbnailController@smallDefault', [$read->manga->getId()])) }}
                                            </a>
                                        </div>
                                        <h4 title="{{ $read->manga->getName() }}">
                                            <a href="{{ URL::action('MangaController@index', [$read->manga->getId()]) }}">
                                                {{ $read->manga->getName() }}
                                            </a>
                                        </h4>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
