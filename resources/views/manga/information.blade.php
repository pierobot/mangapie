@extends ('layout')

@section ('title')
    Information &middot; {{ $name }}
@endsection

@section ('stylesheets')
    <link href="{{ URL::to('/public/css/manga/information.css') }}" rel="stylesheet">
@endsection

@section ('custom_navbar_right')
    @if (\Auth::user()->isAdmin() || \Auth::user()->isMaintainer())
        <li class="clickable navbar-link"><a href="{{ URL::action('MangaEditController@index', [$id]) }}"><span class="glyphicon glyphicon-pencil"></span> Edit</a></li>
    @endif
@endsection

@section ('content')
        {{--
            Only make the title visible on screens that are not xs (phones)
            I don't think it looks very good on xs screens. Maybe there's a way to improve this?
        --}}
        <h2 class="visible-sm visible-md visible-lg visible-xl text-center">Information &middot; {{ $name }}</h2>

        @if ($errors->update->count() > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->update->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @elseif (\Session::has('thumbnail-update-success'))
            <div class="alert alert-success">
                <ul><li>{{ \Session::get('thumbnail-update-success') }}</li><ul>
            </div>
        @endif

        {{ Html::image(URL::action('ThumbnailController@mediumDefault', [$id]), '', ['class' => 'information-img center-block']) }}
        <hr>

        <ul class="nav nav-tabs">
            <li class="active"><a href="#information-content" data-toggle="tab"><span class="glyphicon glyphicon-info-sign"></span> Information</a></li>
            <li><a href="#files-content" data-toggle="tab"><span class="glyphicon glyphicon-folder-open"></span> Files</a></li>
        </ul>

        <div class="tab-content">

            <div class="tab-pane active" id="information-content">

                <ul class="list-group">

                    <li class="list-group-item">
                        <h4><span class="glyphicon glyphicon-info-sign"></span>&nbsp;<b>Description</b></h4>
                    @if ($description != null)
                        {!! Html::decode($description) !!}
                    @else
                        Unable to find description.
                    </li>
                    @endif

                    <li class="list-group-item">
                        <h4><span class="glyphicon glyphicon-book"></span>&nbsp;<b>Type</b></h4>
                    @if ($type != null)
                        <span class="label label-default">{{ $type }}</span>
                    @else
                        Unable to find type.
                    @endif
                    </li>

                    <li class="list-group-item">
                        <h4><span class="glyphicon glyphicon-list-alt"></span>&nbsp;<b>Associated Names</b></h4>
                    @if ($assoc_names != null)
                        @foreach ($assoc_names as $assoc_name)
                            <span class="label label-default">{{ $assoc_name->getName() }}</span>
                        @endforeach
                    @else
                        Unable to find associated names.
                    @endif
                    </li>

                    <li class="list-group-item">
                        <h4><span class="glyphicon glyphicon-tags"></span>&nbsp;<b>Genres</b></h4>
                    @if ($genres != null)
                        @foreach ($genres as $genre)
                            <span class="label label-default">{{ $genre }}</span>
                        @endforeach
                    @else
                        Unable to find genres.
                    @endif
                    </li>

                    <li class="list-group-item">
                        <h4><span class="glyphicon glyphicon-user"></span>&nbsp;<b>Authors</b></h4>
                    @if ($authors != null)
                        @foreach ($authors as $author)
                            <span class="label label-default">{{ $author->getName() }}</span>
                        @endforeach
                    @else
                        Unable to find authors.
                    @endif
                    </li>

                    <li class="list-group-item">
                        <h4><span class="glyphicon glyphicon-user"></span>&nbsp;<b>Artists</b></h4>
                    @if ($artists != null)
                        @foreach ($artists as $artist)
                            <span class="label label-default">{{ $artist->getName() }}</span>
                        @endforeach
                    @else
                        Unable to find artists.
                    @endif
                    </li>

                    <li class="list-group-item">
                        <h4><span class="glyphicon glyphicon-calendar"></span>&nbsp;<b>Year</b></h4>
                    @if ($year != null)
                        <span class="label label-default">{{ $year }}</span>
                    @else
                        Unable to find year.
                    @endif
                    </li>

                    @if (\Auth::user()->isAdmin() == true)
                    <li class="list-group-item">
                        <h4><span class="glyphicon glyphicon-hdd"></span>&nbsp;<b>Path</b></h4>
                        {{ $path }}
                    </li>
                    @endif

                </ul>
            </div>

            <div class="tab-pane" id="files-content">
                <div class="list-group">
                    <li class="list-group-item">
                        <div class="row">
                        <table class="table table-responsive table-hover">
                            <thead>
                                <tr>
                                    <th class="col-lg-8 col-md-8 col-sm-8">
                                        <a href="{{ \URL::action('MangaInformationController@index', [$id, $sort == 'ascending' ? 'descending' : 'ascending']) }}">Filename&nbsp;
                                            @if ($sort == 'ascending')
                                                <span class="glyphicon glyphicon-triangle-top"></span>
                                            @else
                                                <span class="glyphicon glyphicon-triangle-bottom"></span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="col-lg-2 col-md-2 col-sm-2">Size</th>
                                    <th class="col-lg-2 col-md-2 col-sm-2 visible-sm visible-md visible-lg">Modified</th>
                                </tr>
                            </thead>

                            <tbody>
                            @if (empty($archives) === false)
                                @foreach ($archives as $archive)
                                    <tr>
                                        <th class="col-lg-8 col-md-8 col-sm-8"><a href="{{ URL::action('ReaderController@index', [$id, rawurlencode($archive['name']), 1]) }}">{{ $archive['name'] }}</a></th>
                                        <th class="col-lg-2 col-md-2 col-sm-2">{{ $archive['size'] }}</th>
                                        <th class="col-lg-2 col-md-2 col-sm-2 visible-sm visible-md visible-lg">{{ $archive['modified'] }}</th>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        </div>
                    </li>
                </div>
            </div>
    </div>
</div>

@endsection
