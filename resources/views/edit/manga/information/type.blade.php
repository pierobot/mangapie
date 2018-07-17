@extends ('edit.manga.layout')

@section ('tab-content')
    <div class="tab-content">
        <div class="tab-pane active" id="Information-type">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        Type
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h4>New</h4>
                            <hr>
                            {{ Form::open(['action' => 'MangaEditController@patchType', 'method' => 'patch']) }}
                            {{ Form::hidden('manga_id', $id) }}
                            <div class="row">
                                <div class="col-xs-12 col-md-6">
                                    <select name="type" class="form-control">
                                        <option value="Manga">Manga</option>
                                        <option value="Doujinshi">Doujinshi</option>
                                        <option value="Manwha">Manwha</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            {{ Form::submit('Save', ['class' => 'btn btn-success']) }}
                            {{ Form::close() }}
                        </div>
                        <div class="col-xs-6">
                            <h4>Current</h4>
                            <hr>
                            @if (isset($type))
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        {{ $type }}
                                    </div>
                                </div>
                                <br>
                                {{ Form::open(['action' => 'MangaEditController@deleteType', 'method' => 'delete']) }}
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