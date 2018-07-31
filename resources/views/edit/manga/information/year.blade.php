@extends ('edit.manga.layout')

@section ('tab-content')
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        Year
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h4>New</h4>
                            <hr>
                            {{ Form::open(['action' => 'MangaEditController@patchYear', 'method' => 'patch']) }}
                            {{ Form::hidden('manga_id', $id) }}
                            <div class="row">
                                <div class="col-xs-12 col-md-6">
                                    {{ Form::input('year', null, null, ['class' => 'form-control',
                                    'placeholder' => 'Enter year...',
                                    'name' => 'year']) }}
                                </div>
                            </div>
                            <br>
                            {{ Form::submit('Save', ['class' => 'btn btn-success']) }}
                            {{ Form::close() }}
                        </div>
                        <div class="col-xs-6">
                            <h4>Current</h4>
                            <hr>
                            @if (isset($year))
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        {{ $year }}
                                    </div>
                                </div>
                                <br>

                                {{ Form::open(['action' => 'MangaEditController@deleteYear', 'method' => 'delete']) }}
                                {{ Form::hidden('manga_id', $id) }}
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