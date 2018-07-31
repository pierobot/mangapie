@extends ('edit.manga.layout')

@section ('tab-content')
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        Authors
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h4>New</h4>
                            <hr>
                            {{ Form::open(['action' => 'MangaEditController@patchAuthor', 'method' => 'patch']) }}
                            {{ Form::hidden('manga_id', $id) }}
                            <div class="row">
                                <div class="col-xs-12 col-md-6">
                                    {{ Form::input('author', null, null, ['class' => 'form-control',
                                    'placeholder' => 'Enter name...',
                                    'name' => 'name']) }}
                                </div>
                            </div>
                            <br>
                            {{ Form::submit('Add', ['class' => 'btn btn-success']) }}
                            {{ Form::close() }}
                        </div>
                        <div class="col-xs-6">
                            <h4>Current</h4>
                            <hr>
                            @if (isset($authorReferences))
                                {{ Form::open(['action' => 'MangaEditController@deleteAuthor', 'method' => 'delete']) }}
                                {{ Form::hidden('manga_id', $id) }}
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <select name="author_reference_id" class="form-control">
                                            @foreach ($authorReferences as $authorReference)
                                                <option value="{{ $authorReference->id }}">{{ $authorReference->author->name }}</option>
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