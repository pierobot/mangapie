@extends ('layout')

@section ('stylesheets')
    <link href="{{ URL::to('/public/css/manga/information.css') }}" rel="stylesheet">
@endsection

@section ('content')

<div class="panel panel-default">

    <div class="panel-heading">
        <h4 class="panel-title">{{ $name }}</h4>
    </div>

    <div class="panel-body">

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
            @if (\Auth::user()->isAdmin() == true)
            <li><a href="#edit-content" data-toggle="tab"><span class="glyphicon glyphicon-pencil"></span> Edit</a></li>
            @endif

        </ul>

        <div class="tab-content">

            <div class="tab-pane active" id="information-content">

                <ul class="list-group">

                    <li class="list-group-item">
                        <h4><span class="glyphicon glyphicon-info-sign"></span> Description</h4>
                    @if ($description != null)
                        {!! Html::decode($description) !!}
                    @else
                        Unable to find description.
                    </li>
                    @endif

                    <li class="list-group-item">
                        <h4><span class="glyphicon glyphicon-book"></span> Type</h4>
                    @if ($type != null)
                        <span class="label label-default">{{ $type }}</span>
                    @else
                        Unable to find type.
                    @endif
                    </li>

                    <li class="list-group-item">
                        <h4><span class="glyphicon glyphicon-list-alt"></span> Associated Names</h4>
                    @if ($assoc_names != null)
                        @foreach ($assoc_names as $assoc_name)
                            <span class="label label-default">{{ $assoc_name->getName() }}</span>
                        @endforeach
                    @else
                        Unable to find associated names.
                    @endif
                    </li>

                    <li class="list-group-item">
                        <h4><span class="glyphicon glyphicon-tags"></span> Genres</h4>
                    @if ($genres != null)
                        @foreach ($genres as $genre)
                            <span class="label label-default">{{ $genre }}</span>
                        @endforeach
                    @else
                        Unable to find genres.
                    @endif
                    </li>

                    <li class="list-group-item">
                        <h4><span class="glyphicon glyphicon-user"></span> Authors</h4>
                    @if ($authors != null)
                        @foreach ($authors as $author)
                            <span class="label label-default">{{ $author->getName() }}</span>
                        @endforeach
                    @else
                        Unable to find authors.
                    @endif
                    </li>

                    <li class="list-group-item">
                        <h4><span class="glyphicon glyphicon-user"></span> Artists</h4>
                    @if ($artists != null)
                        @foreach ($artists as $artist)
                            <span class="label label-default">{{ $artist->getName() }}</span>
                        @endforeach
                    @else
                        Unable to find artists.
                    @endif
                    </li>

                    <li class="list-group-item">
                        <h4><span class="glyphicon glyphicon-calendar"></span> Year</h4>
                    @if ($year != null)
                        <span class="label label-default">{{ $year }}</span>
                    @else
                        Unable to find year.
                    @endif
                    </li>

                </ul>
            </div>

            <div class="tab-pane" id="files-content">

                <ul id="list-files" class="list-group scrollable-list">
                @if (empty($archives) === false)
                    @foreach ($archives as $archive)
                    <li class="list-group-item">
                        <a href="{{ URL::action('ReaderController@index', [$id, rawurlencode($archive['name']), 1]) }}">
                        {{ $archive['name'] }}
                        </a>
                    </li>
                    @endforeach
                @endif
                </ul>

            </div>

            @if (\Auth::user()->isAdmin() == true)
            <div class="tab-pane" id="edit-content">

                <ul class="list-group">
                    <li class="list-group-item">

                        <h4>Mangaupdates</h4>

                        {{ Form::open(['action' => 'MangaInformationController@update']) }}

                            {{ Form::hidden('id', $id) }}

                            <div class="input-group">

                            {{ Form::label('id:', '', ['for' => 'mu_id']) }}
                            @if ($mu_id != null)
                                {{ Form::text('mu_id', '', ['class' => 'form-control', 'placeholder' => $mu_id]) }}
                            @else
                                {{ Form::text('mu_id', '', ['class' => 'form-control']) }}
                            @endif

                            </div>
                            <br>
                            {{ Form::submit('Update', ['class' => 'btn btn-success', 'id' => 'action', 'name' => 'action', 'value' => 'update']) }}

                        {{ Form::close() }}
                    </li>

                </ul>

                <li class="list-group-item">
                    <h4><span class="glyphicon glyphicon-picture"></span>&nbsp; Cover</h4>

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
                                                                           $archive['name'],
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

                    @endif
                </li>

            </div>
            @endif

        </div>

    </div>
</div>

@endsection
