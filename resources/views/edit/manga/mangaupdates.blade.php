@extends ('edit.manga.layout')

@section ('tab-content')
    <div class="tab-content">
        <div class="tab-pane active" id="MangaUpdates">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        Mangaupdates
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12">
                            {{ Form::open(['action' => 'MangaEditController@putAutofill', 'method' => 'patch']) }}
                            {{ Form::hidden('manga_id', $id) }}
                            <div class="input-group">
                                {{ Form::label('id:', '', ['for' => 'mu_id']) }}
                                @if (isset($mu_id))
                                    {{ Form::text('mu_id', '', ['class' => 'form-control', 'placeholder' => $mu_id]) }}
                                @else
                                    {{ Form::text('mu_id', '', ['class' => 'form-control']) }}
                                @endif
                            </div>
                            <br>
                            {{ Form::submit('Update', ['class' => 'btn btn-success']) }}
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
