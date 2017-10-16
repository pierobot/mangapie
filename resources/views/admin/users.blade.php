@extends ('layout')
@section ('title')
    Admin &middot; Users
@endsection

@section ('content')
    <h2 class="text-center">Users</h2>
    <ul class="nav nav-tabs">
        <li class="active"><a href="#create-user-content" data-toggle="tab"><span class="glyphicon glyphicon-plus"></span> Create</a></li>
        <li><a href="#delete-user-content" data-toggle="tab"><span class="glyphicon glyphicon-trash"></span> Delete</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="create-user-content">
            <ul class="list-group">
                {{ Form::open(['action' => 'AdminController@createUser']) }}

                <li class="list-group-item">
                    <h4>Information</h4>

                    <div class="row">
                        <div class="form-group col-xs-12 col-lg-3">
                        {{ Form::label('name:', null, ['for' => 'name']) }}
                        {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter name here...']) }}

                        {{ Form::label('email:', null, ['for' => 'email']) }}
                        {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Enter e-mail here...']) }}

                        {{ Form::label('password:', null, ['for' => 'password']) }}
                        {{-- Form::password doesn't seem to display properly? hardcoded html for now. --}}
                            <input name="password" id="password" type="password" class="form-control" placeholder="Enter password here...">
                        </div>
                    </div>
                </li>

                <li class="list-group-item">
                    <h4>Libraries</h4>

                    <div class="row">
                        <div class="form-group col-xs-12 col-lg-6">
                            @foreach ($libraries as $library)

                                {{ Form::checkbox('libraries[]', $library->getId(), ['class' => 'form-control']) }}
                                {{ Form::label($library->getName(), null, ['for' => 'libraries[]', 'title' => $library->getPath()]) }}

                                &nbsp;
                            @endforeach
                        </div>
                    </div>
                </li>

                <li class="list-group-item">
                    <h4>Roles</h4>

                    <div class="row">
                        <div class="form-group col-xs-12 col-lg-6">
                            {{ Form::checkbox('admin', 1, ['class' => 'form-control']) }}
                            {{ Form::label('admin-label', 'Admin', ['for' => 'admin']) }}

                            {{ Form::checkbox('maintainer', 1, ['class' => 'form-control']) }}
                            {{ Form::label('maintainer-label', 'Maintainer', ['for' => 'maintainer']) }}
                        </div>
                    </div>
                </li>

                <li class="list-group-item">
                    <div class="row">
                        <div class="col-xs-4 col-lg-3">
                            {{ Form::submit('Create', ['class' => 'btn btn-success']) }}
                        </div>
                    </div>
                </li>

                {{ Form::close() }}
            </ul>
        </div>

        <div class="tab-pane" id="delete-user-content">
            <ul class="list-group">
                {{ Form::open(['action' => 'AdminController@deleteUser']) }}

                <li class="list-group-item">
                    <div class="row">
                        <div class="form-group col-xs-12 col-lg-3">
                            {{ Form::label('name:', null, ['for' => 'name']) }}
                            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter name here...']) }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-4 col-lg-3">
                            {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
                        </div>
                    </div>
                </li>

                {{ Form::close() }}
            </ul>
        </div>
    </div>

    @if (\Session::has('create-alert-success'))
        <div class="alert alert-success">
            <span class="glyphicon glyphicon-ok"></span>&nbsp; {{ \Session::get('create-alert-success') }}
        </div>
    @endif

    @if ($errors->create->count() > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->create->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (\Session::has('delete-alert-success'))
        <div class="alert alert-success">
            <span class="glyphicon glyphicon-ok"></span>&nbsp; {{ \Session::get('delete-alert-success') }}
        </div>
    @endif

    @if ($errors->delete->count() > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->delete->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
