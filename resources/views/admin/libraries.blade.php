@extends ('layout')

@section ('title')
    Admin &middot; Libraries
@endsection

@section ('custom_navbar_right')
    @include ('shared.libraries')
@endsection

@section ('content')
    <h2 class="text-center"><b>Libraries</b></h2>

    @include ('shared.success')
    @include ('shared.warnings')
    @include ('shared.errors')

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="glyphicon glyphicon-plus"></span>&nbsp;Create
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                {{ Form::open(['action' => 'LibraryController@create']) }}

                    <div class="col-xs-12">
                        <div class="row">
                            <div class="form-group col-xs-12 col-md-4">

                                {{ Form::label('name:', null, ['for' => 'name']) }}
                                {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter name here...']) }}

                                {{ Form::label('path:', null, ['for' => 'path']) }}
                                {{ Form::text('path', null, ['class' => 'form-control', 'placeholder' => 'Enter path here'])}}

                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            {{ Form::submit('Create', ['class' => 'btn btn-success']) }}
                        </div>
                    </div>

                {{ Form::close() }}

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="glyphicon glyphicon-pencil"></span>&nbsp;Edit
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12">
                    <table class="table table-responsive table-hover">
                        <thead>
                        <tr>
                            <th class="col-xs-1"></th>
                            <th class="col-xs-1"></th>
                            <th>Name</th>
                            <th class="visible-sm visible-md visible-lg">Path</th>
                            <th>Count</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($libraries as $library)
                            <tr>
                                <td>
                                    {{ Form::open(['action' => 'LibraryController@delete']) }}
                                    {{ Form::hidden('id', $library->getId()) }}
                                    <button class="btn btn-danger" type="submit" title="Delete">
                                        <span class="glyphicon glyphicon-remove"></span>
                                    </button>
                                    {{ Form::close() }}
                                </td>
                                <td>
                                    {{ Form::open(['action' => 'LibraryController@update']) }}
                                    {{ Form::hidden('id', $library->getId()) }}
                                    <button class="btn btn-success" type="submit" title="Scan">
                                        <span class="glyphicon glyphicon-refresh"></span>
                                    </button>
                                    {{ Form::close() }}
                                </td>
                                <td>{{ $library->name }}</td>
                                <td class="visible-sm visible-md visible-lg">{{ $library->path }}</td>
                                <td>{{ \App\Manga::where('library_id', '=', $library->id)->count() }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="alert alert-warning">
                        <span class="glyphicon glyphicon-warning-sign"></span>&nbsp; Deleted libraries will <b>NOT</b> be deleted from the filesystem.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
