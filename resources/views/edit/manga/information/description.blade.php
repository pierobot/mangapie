@extends ('edit.manga.layout')

@section ('tab-content')
    <div class="tab-content">
        <div class="tab-pane active" id="Information-description">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        Description
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
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
    </div>
@endsection