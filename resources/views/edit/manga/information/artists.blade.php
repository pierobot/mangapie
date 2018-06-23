@extends ('edit.manga.layout')

@section ('tab-content')
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        Artists
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h4>New</h4>
                            <hr>
                            {{ Form::open(['action' => 'MangaEditController@update']) }}
                            {{ Form::hidden('id', $id) }}
                            {{ Form::hidden('action', 'artist.add') }}
                            <div class="row">
                                <div class="col-xs-12 col-md-6">
                                    {{ Form::input('artist', null, null, ['class' => 'form-control',
                                    'placeholder' => 'Enter name...',
                                    'name' => 'artist']) }}
                                </div>
                            </div>
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
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <select name="artist" class="form-control">
                                            @foreach ($artists as $artist)
                                                <option value="{{ $artist->getName() }}">{{ $artist->getName() }}</option>
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