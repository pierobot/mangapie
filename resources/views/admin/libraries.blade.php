@extends ('layout')

@section ('title')
    Admin &middot; Libraries
@endsection

@section ('content')

    <div class="panel panel-default">

        <div class="panel-heading">
            <h2 class="panel-title">Libraries</h2>
        </div>

        <div class="panel-body">

            <ul class="nav nav-tabs">

                <li class="active"><a href="#create-content" data-toggle="tab"><span class="glyphicon glyphicon-plus"></span> Create</a></li>
                <li><a href="#edit-content" data-toggle="tab"><span class="glyphicon glyphicon-pencil"></span> Edit</a></li>

            </ul>

            <div class="tab-content">

                <div class="tab-pane active" id="create-content">
                    <ul class="list-group">

                        {{ Form::open(['action' => 'LibraryController@create']) }}

                        <li class="list-group-item">
                            <div class="row">
                                <div class="form-group col-xs-12 col-lg-3">

                                    {{ Form::label('name:', null, ['for' => 'name']) }}
                                    {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter name here...']) }}

                                    {{ Form::label('path:', null, ['for' => 'path']) }}
                                    {{ Form::text('path', null, ['class' => 'form-control', 'placeholder' => 'Enter path here'])}}

                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-xs-12 col-lg-3">
                                {{ Form::submit('Create', ['class' => 'btn btn-success']) }}
                                </div>
                            </div>

                        @if ($errors->create->count() > 0)

                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->create->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>

                        @endif
                        </li>

                        {{ Form::close() }}

                        @if (\Session::has('library-create-success'))

                            <div class="alert alert-success">
                                <span class="glyphicon glyphicon-ok"></span>&nbsp; {{ \Session::get('library-create-success') }}
                            </div>

                        @elseif (\Session::has('library-create-failure'))

                            <div class="alert alert-danger">
                                <span class="glyphicon glyphicon-remove"></span>&nbsp; {{ \Session::get('library-create-failure') }}
                            </div>

                        @endif

                    </ul>
                </div>

                <div class="tab-pane" id="edit-content">
                    <ul class="list-group">

                        {{ Form::open(['action' => 'LibraryController@update']) }}

                        <li class="list-group-item">

                            <div class="row">
                                <div class="form-group col-xs-12">
                                    <table class="table table-responsive table-hover ">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>Name</th>
                                            <th class="visible-sm visible-md visible-lg">Id</th>
                                            <th class="visible-sm visible-md visible-lg">Path</th>
                                            <th class="visible-sm visible-md visible-lg">Count</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        @foreach ($libraries as $library)
                                            <tr>
                                                <th>{{ Form::checkbox('ids[]', $library->id) }}</th>
                                                <th>{{ $library->name }}</th>
                                                <th class="visible-sm visible-md visible-lg">{{ $library->id }}</th>
                                                <th class="visible-sm visible-md visible-lg">{{ $library->path }}</th>
                                                <th class="visible-sm visible-md visible-lg">{{ \App\Manga::where('library_id', '=', $library->id)->count() }}</th>
                                            </tr>
                                        @endforeach
                                        </tbody>

                                    </table>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-xs-12 col-lg-3">
                                    {{ Form::submit('Update', ['class' => 'btn btn-success', 'id' => 'action', 'name' => 'action', 'value' => 'update']) }}
                                    {{ Form::submit('Delete', ['class' => 'btn btn-danger', 'id' => 'action', 'name' => 'action', 'value' => 'delete']) }}
                                </div>
                            </div>

                            @if ($errors->update->count() > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->update->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (\Session::has('library-update-success'))

                                <div class="alert alert-success">
                                    <span class="glyphicon glyphicon-ok"></span>&nbsp; {{ \Session::get('library-update-success') }}
                                </div>

                            @elseif (\Session::has('library-update-failure'))

                                <div class="alert alert-danger">
                                    <span class="glyphicon glyphicon-remove"></span>&nbsp; {{ \Session::get('library-update-failure') }}
                                </div>

                            @endif

                        </li>

                        {{ Form::close() }}

                    </ul>
                </div>

            </div>
        </div>
    </div>

@endsection
