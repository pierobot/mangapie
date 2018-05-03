@extends ('layout')

@section ('title')
    Admin &middot; Users
@endsection

@section ('custom_navbar_right')
    @include ('shared.searchbar')
    @include ('shared.libraries')
@endsection

@section ('content')
    <h2 class="text-center"><b>Users</b></h2>

    @include ('shared.success')
    @include ('shared.errors')

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="glyphicon glyphicon-plus"></span>&nbsp;Create
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                {{ Form::open(['action' => 'UserController@create']) }}

                <div class="col-md-4">
                    <h4>Information</h4>
                    <hr>
                    <div class="form-group">
                        {{ Form::label('name:', null, ['for' => 'name']) }}
                        {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter name here...']) }}

                        {{ Form::label('email:', null, ['for' => 'email']) }}
                        {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Enter e-mail here...']) }}

                        {{ Form::label('password:', null, ['for' => 'password']) }}
                        {{-- Form::password doesn't seem to display properly? hardcoded html for now. --}}
                        <input name="password" id="password" type="password" class="form-control" placeholder="Enter password here...">
                    </div>
                </div>

                <div class="col-md-4">
                    <h4>Libraries</h4>
                    <hr>
                    <div class="row">
                        @admin
                            @php
                                $libraries = App\Library::all();
                            @endphp
                        @else
                            @php
                                $libraryIds = App\LibraryPrivilege::getIds();
                                $libraries = App\Library::whereIn('id', $libraryIds)->get();
                            @endphp
                        @endadmin

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
                <span class="glyphicon glyphicon-th-list"></span>&nbsp;Browse
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12">
                    <table class="table table-responsive table-hover">
                        <thead>
                            <tr>
                                <th class="col-xs-1"></th>
                                <th>Name</th>
                                <th>E-mail</th>
                            </tr>
                        </thead>
                        <tbody>
                             @foreach ($users as $user)
                                <tr>
                                    <td>
                                        {{ Form::open(['action' => 'UserController@delete']) }}
                                        <button class="btn btn-danger" type="submit" title="Delete"
                                                name="name" value="{{ $user->getName() }}">
                                            <span class="glyphicon glyphicon-remove"></span>
                                        </button>
                                        {{ Form::close() }}
                                    </td>
                                    <td>
                                        {{ $user->getName() }}
                                    </td>
                                    <td>
                                        {{ $user->getEmail() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
