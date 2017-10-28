@extends ('layout')

@section ('title')
    Admin &middot; Libraries
@endsection

@section ('content')
    <h2 class="text-center"><b>Libraries</b></h2>

    @if (\Session::has('success'))
        <div class="alert alert-success">
            <span class="glyphicon glyphicon-ok"></span>&nbsp; {{ \Session::get('success') }}
        </div>
    @endif

    @if ($errors->count() > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                </li>

                {{ Form::close() }}
            </ul>
        </div>

        <div class="tab-pane" id="edit-content">
            <ul class="list-group">
                <li class="list-group-item">
                    <table class="table table-responsive table-hover ">
                        <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>Name</th>
                            <th class="visible-sm visible-md visible-lg">Id</th>
                            <th class="visible-sm visible-md visible-lg">Path</th>
                            <th>Count</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($libraries as $library)
                            <tr>
                                <td>
                                    {{ Form::open(['action' => 'LibraryController@update']) }}
                                    {{ Form::hidden('id', $library->getId()) }}
                                    <button class="btn btn-success" type="submit">
                                        <span class="glyphicon glyphicon-refresh"></span>
                                    </button>
                                    {{ Form::close() }}
                                </td>
                                <td>
                                    {{ Form::open(['action' => 'LibraryController@delete']) }}
                                    {{ Form::hidden('id', $library->getId()) }}
                                    <button class="btn btn-danger" type="submit">
                                        <span class="glyphicon glyphicon-remove"></span>
                                    </button>
                                    {{ Form::close() }}
                                </td>
                                <td>{{ $library->name }}</td>
                                <td class="visible-sm visible-md visible-lg">{{ $library->id }}</td>
                                <td class="visible-sm visible-md visible-lg">{{ $library->path }}</td>
                                <td>{{ \App\Manga::where('library_id', '=', $library->id)->count() }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </li>
            </ul>
        </div>
    </div>
@endsection
