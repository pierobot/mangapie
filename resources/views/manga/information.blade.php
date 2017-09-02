@extends ('layout')

@section ('content')

<div class="panel panel-default">

    <div class="panel-heading">
        <h4 class="panel-title">{{ $name }}</h4>
    </div>

    <div class="panel-body">

        {{ Html::image(URL::action('MangaController@thumbnail', [$id]), '', ['class' => 'information-img center-block']) }}
        <hr>
        <!--
            other preview thumbnails here ?
        -->

        <ul class="nav nav-tabs">

            <li class="active"><a href="#information-content" data-toggle="tab"><span class="glyphicon glyphicon-info-sign"></span> Information</a></li>
            <li><a href="#files-content" data-toggle="tab"><span class="glyphicon glyphicon-folder-open"></span> Files</a></li>
            <li><a href="#edit-content" data-toggle="tab"><span class="glyphicon glyphicon-pencil"></span> Edit</a></li>

        </ul>

        <div class="tab-content">

            <div class="tab-pane active" id="information-content">

                <ul class="list-group">

                    <li class="list-group-item">
                        <h4><span class="glyphicon glyphicon-info-sign"></span> Description</h4>
                    @if ($description != null)
                        {{ $description }}
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
                @if ($archives != null)
                    @foreach ($archives as $archive)
                    <li class="list-group-item">
                        <a href="{{ \Config::get('mangapie.app_url') }}/reader/{{ $id }}/{{ rawurlencode($archive['name']) }}/1">
                        {{ $archive['name'] }}
                        </a>
                    </li>
                    @endforeach
                @endif
                </ul>

            </div>

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
                            <hr>

                            {{ Form::submit('Update', ['class' => 'btn btn-success', 'id' => 'action', 'name' => 'action', 'value' => 'update']) }}
                            {{-- Form::submit('Delete', ['class' => 'btn btn-danger', 'id' => 'action', 'name' => 'action', 'value' => 'delete']) --}}

                        {{ Form::close() }}
                    </li>
                </ul>

            </div>

        </div>

    </div>
</div>

@endsection
