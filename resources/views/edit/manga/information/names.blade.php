@extends ('edit.manga.layout')

@section ('tab-content')
    <div class="tab-content">
        <div class="tab-pane active" id="Information-description">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        Names
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h4>New</h4>
                            <hr>
                            {{ Form::open(['action' => 'MangaEditController@update']) }}
                            {{ Form::hidden('id', $id) }}
                            {{ Form::hidden('action', 'assoc_name.add') }}
                            <div class="row">
                                <div class="col-xs-12 col-md-6">
                                    {{ Form::input('assoc_name', null, null, ['class' => 'form-control',
                                    'placeholder' => 'Enter name...',
                                    'name' => 'assoc_name']) }}
                                </div>
                            </div>
                            <br>
                            {{ Form::submit('Add', ['class' => 'btn btn-success']) }}
                            {{ Form::close() }}
                        </div>
                        <div class="col-xs-6"
                             style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis">
                            <h4>Current</h4>
                            <hr>
                            @if (isset($assoc_names))
                                {{ Form::open(['action' => 'MangaEditController@update']) }}
                                {{ Form::hidden('id', $id) }}
                                {{ Form::hidden('action', 'assoc_name.delete') }}
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <select name="assoc_name" class="form-control">
                                            @foreach ($assoc_names as $name_)
                                                <option value="{{ $name_->getName() }}">{{ $name_->getName() }}</option>
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