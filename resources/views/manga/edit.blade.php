@extends ('layout')

@section ('title')
    Edit &middot; {{ $name }}
@endsection

@section ('stylesheets')
    <link href="{{ URL::to('/public/css/manga/information.css') }}" rel="stylesheet">
@endsection

@section ('custom_navbar_right')

    <li>
        {{ Form::open(['action' => 'SearchController@search', 'class' => 'navbar-form form-inline']) }}

        <div class="form-group">
            {{ Form::text('query', null, ['class' => 'form-control', 'placeholder' => '...']) }}
        </div>

        {{ Form::submit('Search', ['class' => 'btn btn-primary btn-navbar']) }}

        {{ Form::close() }}
    </li>

    <li class="clickable navbar-link">
        <a href="{{ URL::action('MangaInformationController@index', [$id]) }}"><span class="glyphicon glyphicon-book white"></span> Information</a>
    </li>

@endsection

@section ('content')
    <h2 class="text-center"><b>Edit &middot; <a href="{{ URL::action('MangaInformationController@index', [$id]) }}">{{ $name }}</a></b></h2>

    @if ($errors->count() > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (\Session::has('update-success'))
        <div class="alert alert-success">
            <ul>
                <li>{{ \Session::get('update-success') }}</li>
            </ul>
        </div>
    @endif

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <a data-toggle="collapse" href="#mangaupdates">Mangaupdates</a>
            </div>
        </div>
        <div id="mangaupdates" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="panel-content">
                    <h4>Autofill</h4>
                    <hr>
                    {{ Form::open(['action' => 'MangaInformationController@update']) }}
                    {{ Form::hidden('id', $id) }}
                    <div class="input-group">
                        {{ Form::label('id:', '', ['for' => 'mu_id']) }}
                        @if (isset($mu_id))
                            {{ Form::text('mu_id', '', ['class' => 'form-control', 'placeholder' => $mu_id]) }}
                        @else
                            {{ Form::text('mu_id', '', ['class' => 'form-control']) }}
                        @endif
                    </div>
                    <br>
                    {{ Form::submit('Update', ['class' => 'btn btn-success',
                                               'id' => 'action',
                                               'name' => 'action',
                                               'value' => 'update']) }}
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="glyphicon glyphicon-info-sign"></span>&nbsp;&nbsp;<a data-toggle="collapse" href="#information">Information</a>
            </div>
        </div>
        <div id="information" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="panel-content">

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <span class="glyphicon glyphicon-info-sign"></span>&nbsp;&nbsp;<a data-toggle="collapse" href="#description">Description</a>
                            </div>
                        </div>
                        <div id="description" class="panel-collapse collapse">
                            <div class="panel-body">
                                <div class="panel-content">
                                    <div class="col-xs-12 col-md-6">
                                        <h4>New</h4>
                                        <hr>
                                        {{ Form::open(['action' => 'MangaEditController@update']) }}
                                        {{ Form::hidden('id', $id) }}
                                        {{ Form::hidden('action', 'description.update') }}
                                        {{ Form::textarea('description', null, ['class' => 'form-control',
                                                                                'placeholder' => 'Enter description...']) }}
                                        <br>
                                        {{ Form::submit('Save', ['class' => 'btn btn-success']) }}
                                        {{ Form::close() }}
                                    </div>
                                    <div class="col-xs-12 col-md-6">
                                        <h4>Current</h4>
                                        <hr>
                                        @if (isset($description))
                                            {{ $description }}
                                            {{ Form::open(['action' => 'MangaEditController@update']) }}
                                            {{ Form::hidden('id', $id) }}
                                            {{ Form::hidden('action', 'description.delete') }}
                                            <br>
                                            {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
                                            {{ Form::close() }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <span class="glyphicon glyphicon-book"></span>&nbsp;&nbsp;<a data-toggle="collapse" href="#type">Type</a>
                            </div>
                        </div>
                        <div id="type" class="panel-collapse collapse">
                            <div class="panel-body">
                                <div class="panel-content">
                                    <div class="col-xs-6">
                                        <h4>New</h4>
                                        <hr>
                                        {{ Form::open(['action' => 'MangaEditController@update']) }}
                                        {{ Form::hidden('id', $id) }}
                                        {{ Form::hidden('action', 'type.update') }}
                                        <select name="type" class="form-control">
                                            <option value="Manga">Manga</option>
                                            <option value="Doujinshi">Doujinshi</option>
                                            <option value="Manwha">Manwha</option>
                                        </select>
                                        <br>
                                        {{ Form::submit('Save', ['class' => 'btn btn-success']) }}
                                        {{ Form::close() }}
                                    </div>
                                    <div class="col-xs-6">
                                        <h4>Current</h4>
                                        <hr>
                                        @if (isset($type))
                                            {{ $type }}
                                            {{ Form::open(['action' => 'MangaEditController@update']) }}
                                            {{ Form::hidden('id', $id) }}
                                            {{ Form::hidden('action', 'type.delete') }}
                                            <br>
                                            {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
                                            {{ Form::close() }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp;<a data-toggle="collapse" href="#names">Associated Names</a>
                            </div>
                        </div>
                        <div id="names" class="panel-collapse collapse">
                            <div class="panel-body">
                                <div class="panel-content">
                                    <div class="col-xs-6">
                                        <h4>New</h4>
                                        <hr>
                                        {{ Form::open(['action' => 'MangaEditController@update']) }}
                                        {{ Form::hidden('id', $id) }}
                                        {{ Form::hidden('action', 'assoc_name.add') }}
                                        {{ Form::input('assoc_name', null, null, ['class' => 'form-control',
                                                                                  'placeholder' => 'Enter name...',
                                                                                  'name' => 'assoc_name']) }}
                                        <br>
                                        {{ Form::submit('Add', ['class' => 'btn btn-success']) }}
                                        {{ Form::close() }}
                                    </div>
                                    <div class="col-xs-6" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis">
                                        <h4>Current</h4>
                                        <hr>
                                        @if (isset($assoc_names))
                                            {{ Form::open(['action' => 'MangaEditController@update']) }}
                                            {{ Form::hidden('id', $id) }}
                                            {{ Form::hidden('action', 'assoc_name.delete') }}
                                            <select name="assoc_name" class="form-control">
                                                @foreach ($assoc_names as $name)
                                                    <option value="{{ $name->getName() }}">{{ $name->getName() }}</option>
                                                @endforeach
                                            </select>
                                            <br>
                                            {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
                                            {{ Form::close() }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;<a data-toggle="collapse" href="#genres">Genres</a>
                            </div>
                        </div>
                        <div id="genres" class="panel-collapse collapse">
                            <div class="panel-body">
                                <div class="panel-content">
                                    <div class="col-xs-6">
                                        <h4>New</h4>
                                        <hr>
                                        {{ Form::open(['action' => 'MangaEditController@update']) }}
                                        {{ Form::hidden('id', $id) }}
                                        {{ Form::hidden('action', 'genre.add') }}
                                        @if (isset($availableGenres) && empty($availableGenres) == false)
                                            <select name="genre" class="form-control">
                                            @foreach ($availableGenres as $genre)
                                                <option value="{{ $genre }}">{{ $genre }}</option>
                                            @endforeach
                                            </select>
                                            <br>
                                            {{ Form::submit('Add', ['class' => 'btn btn-success']) }}
                                        @else
                                            No available genres to select from were found.
                                        @endif
                                        {{ Form::close() }}
                                    </div>
                                    <div class="col-xs-6">
                                        <h4>Current</h4>
                                        <hr>
                                        @if (isset($genres))
                                            {{ Form::open(['action' => 'MangaEditController@update']) }}
                                            {{ Form::hidden('id', $id) }}
                                            {{ Form::hidden('action', 'genre.delete') }}
                                            <select name="genre" class="form-control">
                                                @foreach ($genres as $genre)
                                                    <option value="{{ $genre }}">{{ $genre }}</option>
                                                @endforeach
                                            </select>
                                            <br>
                                            {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
                                            {{ Form::close() }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;<a data-toggle="collapse" href="#authors">Authors</a>
                            </div>
                        </div>
                        <div id="authors" class="panel-collapse collapse">
                            <div class="panel-body">
                                <div class="panel-content">
                                    <div class="col-xs-6">
                                        <h4>New</h4>
                                        <hr>
                                        {{ Form::open(['action' => 'MangaEditController@update']) }}
                                        {{ Form::hidden('id', $id) }}
                                        {{ Form::hidden('action', 'author.add') }}
                                        {{ Form::input('author', null, null, ['class' => 'form-control',
                                                                              'placeholder' => 'Enter name...',
                                                                              'name' => 'author']) }}
                                        <br>
                                        {{ Form::submit('Add', ['class' => 'btn btn-success']) }}
                                        {{ Form::close() }}
                                    </div>
                                    <div class="col-xs-6">
                                        <h4>Current</h4>
                                        <hr>
                                        @if (isset($authors))
                                            {{ Form::open(['action' => 'MangaEditController@update']) }}
                                            {{ Form::hidden('id', $id) }}
                                            {{ Form::hidden('action', 'author.delete') }}
                                            <select name="author" class="form-control">
                                                @foreach ($authors as $author)
                                                    <option value="{{ $author->getName() }}">{{ $author->getName() }}</option>
                                                @endforeach
                                            </select>
                                            <br>
                                            {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
                                            {{ Form::close() }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;<a data-toggle="collapse" href="#artists">Artists</a>
                            </div>
                        </div>
                        <div id="artists" class="panel-collapse collapse">
                            <div class="panel-body">
                                <div class="panel-content">
                                    <div class="col-xs-6">
                                        <h4>New</h4>
                                        <hr>
                                        {{ Form::open(['action' => 'MangaEditController@update']) }}
                                        {{ Form::hidden('id', $id) }}
                                        {{ Form::hidden('action', 'artist.add') }}
                                        {{ Form::input('artist', null, null, ['class' => 'form-control',
                                                                              'placeholder' => 'Enter name...',
                                                                              'name' => 'artist']) }}
                                        <br>
                                        {{ Form::submit('Add', ['class' => 'btn btn-success']) }}
                                        {{ Form::close() }}
                                    </div>
                                    <div class="col-xs-6">
                                        <h4>Current</h4>
                                        <hr>
                                        @if (isset($artists))
                                            {{ Form::open(['action' => 'MangaEditController@update']) }}
                                            {{ Form::hidden('id', $id) }}
                                            {{ Form::hidden('action', 'artist.delete') }}
                                            <select name="artist" class="form-control">
                                                @foreach ($artists as $artist)
                                                    <option value="{{ $artist->getName() }}">{{ $artist->getName() }}</option>
                                                @endforeach
                                            </select>
                                            <br>
                                            {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
                                            {{ Form::close() }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <span class="glyphicon glyphicon-calendar"></span>&nbsp;&nbsp;<a data-toggle="collapse" href="#year">Year</a>
                            </div>
                        </div>
                        <div id="year" class="panel-collapse collapse">
                            <div class="panel-body">
                                <div class="panel-content">
                                    <div class="col-xs-6">
                                        <h4>New</h4>
                                        <hr>
                                        {{ Form::open(['action' => 'MangaEditController@update']) }}
                                        {{ Form::hidden('id', $id) }}
                                        {{ Form::hidden('action', 'year.update') }}
                                        {{ Form::input('year', null, null, ['class' => 'form-control',
                                                                            'placeholder' => 'Enter year...',
                                                                            'name' => 'year']) }}
                                        <br>
                                        {{ Form::submit('Save', ['class' => 'btn btn-success']) }}
                                        {{ Form::close() }}
                                    </div>
                                    <div class="col-xs-6">
                                        <h4>Current</h4>
                                        <hr>
                                        @if (isset($year))
                                            {{ $year }}
                                            {{ Form::open(['action' => 'MangaEditController@update']) }}
                                            {{ Form::hidden('id', $id) }}
                                            {{ Form::hidden('action', 'year.delete') }}
                                            <br>
                                            {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
                                            {{ Form::close() }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="glyphicon glyphicon-picture"></span>&nbsp;&nbsp;<a data-toggle="collapse" href="#covers">Cover</a>
            </div>
        </div>
        <div id="covers" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="panel-content">
                    @if (empty($archives) === false)

                        @foreach ($archives as $archive_index => $archive)
                            <div class="panel-group">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <a data-toggle="collapse" href="#{{ $archive_index }}">{{ $archive['name'] }}</a>
                                        </h3>
                                    </div>
                                    <div id="{{ $archive_index }}" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="row">
                                                @for ($i = 1; $i <= 4; $i++)
                                                    <div class="col-lg-2 col-sm-4 col-xs-6 set-cover thumbnail">
                                                        {{ Form::open(['action' => 'ThumbnailController@update'], [$id]) }}

                                                        {{ Form::hidden('id', $id) }}
                                                        {{ Form::hidden('archive_name', $archive['name']) }}
                                                        {{ Form::hidden('page', $i) }}

                                                        <div>
                                                            {{ Html::image(URL::action('ThumbnailController@small', [
                                                                               $id,
                                                                               rawurlencode($archive['name']),
                                                                               $i]), null, ['class' => 'center-block'])
                                                            }}
                                                        </div>

                                                        <h4>
                                                            {{ Form::submit('Set', ['class' => 'btn btn-success center-block']) }}
                                                        </h4>

                                                        {{ Form::close() }}
                                                    </div>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @endforeach
                    @else

                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
