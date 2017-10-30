@extends ('layout')

@section ('title')
    Favorites
@endsection

@section ('stylesheets')
    <link href="{{ URL::to('/public/css/manga/index.css') }}" rel="stylesheet">
@endsection

@section ('custom_navbar_right')

    <li>
        {{ Form::open(['action' => 'SearchController@search', 'class' => 'navbar-form form-inline']) }}

        <div class="form-group">
            {{ Form::text('query', null, ['class' => 'form-control',
                                          'placeholder' => '...',
                                          'id' => 'autocomplete']) }}
        </div>

        {{ Form::submit('Search', ['class' => 'btn btn-primary btn-navbar']) }}

        {{ Form::close() }}
    </li>

    <li class="dropdown">
        <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="glyphicon glyphicon-book"></span>&nbsp;Libraries&nbsp;<span class="glyphicon glyphicon-chevron-down white"></span>
        </a>
        <ul class="dropdown-menu" style="color: black;">
            @foreach ($libraries as $library)
                <li>
                   <a href="{{ URL::action('MangaController@library', ['id' => $library->getId()]) }}">{{ $library->getName() }}</a>
                <li>
            @endforeach
        </ul>
    </li>

@endsection

@section ('content')
    <h3 class="text-center">
        <b>Favorites&nbsp;({{ $total }})</b>
    </h3>

    @if ($errors->count() > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">

    @foreach ($favorite_list as $favorite)

    <div class="col-lg-2 col-sm-4 col-xs-6 text-center thumbnail center">

        <div>
            <a href="{{ URL::action('MangaInformationController@index', [$favorite->getId()]) }}">
                {{ Html::image(URL::action('ThumbnailController@smallDefault', [$favorite->getId()])) }}
            </a>
        </div>

        <h4 title="{{ $favorite->getName() }}"><a href="{{ URL::action('MangaInformationController@index', [$favorite->getId()]) }}">{{ $favorite->getName() }}</a></h4>

    </div>

    @endforeach

    </div>

    <div class="text-center">
        {{ $favorite_list->render() }}
    </div>

@endsection

@section ('scripts')
    <script src="{{ \URL::to('public/bootstrap-3-typeahead/bootstrap3-typeahead.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        $(function () {
            $('#autocomplete').typeahead({
                minLength: 3,
                delay: 250,
                source: function (query, process) {
                    return $.getJSON('{{ \URL::to('/search/autocomplete') }}', { query : query}, function (data) {
                        return process(data);
                    });
                }
            });
        });
    </script>
@endsection
