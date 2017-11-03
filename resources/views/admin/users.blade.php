@extends ('layout')
@section ('title')
    Admin &middot; Users
@endsection

@section ('content')
    <h2 class="text-center"><b>Users</b></h2>

    @include ('shared.success')
    @include ('shared.errors')

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="glyphicon glyphicon-plus"></span> Create
            </div>
        </div>
        <div class="panel-content">
            <div class="panel-body">
                {{ Form::open(['action' => 'UserController@create']) }}

                <div class="col-md-4">
                    <h4>Information</h4>
                    <hr>
                    <div class="row">
                        <div class="form-group col-xs-12">
                            {{ Form::label('name:', null, ['for' => 'name']) }}
                            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter name here...']) }}

                            {{ Form::label('email:', null, ['for' => 'email']) }}
                            {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Enter e-mail here...']) }}

                            {{ Form::label('password:', null, ['for' => 'password']) }}
                            {{-- Form::password doesn't seem to display properly? hardcoded html for now. --}}
                            <input name="password" id="password" type="password" class="form-control" placeholder="Enter password here...">
                        </div>

                    </div>
                </div>

                <div class="col-md-4">
                    <h4>Libraries</h4>
                    <hr>
                    <div class="row">
                        @foreach ($libraries as $library)
                            <div class="form-group col-xs-12 col-md-6">
                                {{ Form::checkbox('libraries[]', $library->getId(), ['class' => 'form-control']) }}
                                {{ Form::label($library->getName(), null, ['for' => 'libraries[]', 'title' => $library->getPath()]) }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-4">
                    <h4>Roles</h4>
                    <hr>
                    <div class="row">
                        <div class="form-group col-xs-12 col-md-6">
                            {{ Form::checkbox('admin', 1, ['class' => 'form-control']) }}
                            {{ Form::label('admin-label', 'Admin', ['for' => 'admin']) }}
                        </div>

                        <div class="form-group col-xs-12 col-md-6">
                            {{ Form::checkbox('maintainer', 1, ['class' => 'form-control']) }}
                            {{ Form::label('maintainer-label', 'Maintainer', ['for' => 'maintainer']) }}
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
                <span class="glyphicon glyphicon-trash"></span> Delete
            </div>
        </div>
        <div class="panel-content">
            <div class="panel-body">
                {{ Form::open(['action' => 'UserController@delete']) }}

                <div class="col-xs-12">
                    <div class="row">
                        <div class="form-group col-xs-12 col-md-4">
                            {{ Form::label('name:', null, ['for' => 'name']) }}
                            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter name here...']) }}
                        </div>
                    </div>
                </div>

                <div class="col-xs-12">
                    <div class="form-group">
                        {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
                    </div>
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
