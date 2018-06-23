@extends ('edit.manga.layout')

@section ('tab-content')
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        Genres
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h4>New</h4>
                            <hr>
                            {{ Form::open(['action' => 'MangaEditController@update']) }}
                            {{ Form::hidden('id', $id) }}
                            {{ Form::hidden('action', 'genre.add') }}
                            @php ($availableGenres = \App\Genre::all())
                            @if ($availableGenres->count())
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <select name="genre" class="form-control">
                                            @foreach ($availableGenres as $genre)
                                                <option value="{{ $genre }}">{{ $genre->getName() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
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
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <select name="genre" class="form-control">
                                            @foreach ($genres as $genre)
                                                <option value="{{ $genre->getName() }}">{{ $genre->getName() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
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
@endsection