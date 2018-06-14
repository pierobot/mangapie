@extends ('layout')

@section ('title')
    Admin &middot; Users
@endsection

@section ('custom_navbar_right')
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
                        <label for="name">Name:</label>
                        <input id="name" name="name" type="text" class="form-control" placeholder="Enter name here">

                        <label for="email">Email:</label>
                        <input id="email" name="email" type="text" class="form-control" placeholder="Enter e-mail here">

                        <label for="password">Password:</label>
                        <input id="password" name="password" type="password" class="form-control" placeholder="Enter password here...">
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
                                <div class="checkbox checkbox-success">
                                    <input id="libraries[{{ $library->getId() }}]" name="libraries[{{ $library->getId() }}]" type="checkbox" value="{{ $library->getId() }}">
                                    <label for="libraries[{{ $library->getId() }}]" title="{{ $library->getPath() }}">{{ $library->getName() }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-4">
                    <h4>Roles</h4>
                    <hr>
                    <div class="row">
                        <div class="form-group col-xs-12 col-md-6">
                            <div class="checkbox checkbox-success">
                                <input id="admin" name="admin" type="checkbox" value="1">
                                <label for="admin">Admin</label>
                            </div>
                        </div>

                        <div class="form-group col-xs-12 col-md-6">
                            <div class="checkbox checkbox-success">
                                <input id="maintainer" name="maintainer" type="checkbox" value="1">
                                <label for="maintainer">Maintainer</label>
                            </div>
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
