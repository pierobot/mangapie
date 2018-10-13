@extends ('admin.users.layout')

@section ('title')
    Admin &middot; Create User
@endsection

@section ('card-content')
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <a class="nav-link active">Create</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ URL::action('AdminController@editUsers') }}">Edit</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ URL::action('AdminController@deleteUsers') }}">Delete</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            {{ Form::open(['action' => 'UserController@create', 'method' => 'put']) }}

            <div class="form-row">
                <div class="col-12 col-lg-6">
                    <h5>Account</h5>

                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    Name
                                </span>
                            </div>

                            <input type="text" class="form-control" title="Username for the account" id="name" name="name">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    Password
                                </span>
                            </div>

                            <input type="password" class="form-control" title="Password for the account" id="password" name="password">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    E-mail
                                </span>
                            </div>

                            <input type="email" class="form-control" title="E-mail for the account" id="email" name="email">
                        </div>
                    </div>
                </div>
            </div>
            <hr>

            <div class="form-row">
                <div class="col-12">
                    <h5>Libraries</h5>
                </div>

                @php ($libraries = \App\Library::all('id', 'name'))

                @foreach ($libraries as $library)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="{{ $library->id }}" name="libraries[]" value="{{ $library->id }}">
                                <label class="custom-control-label" for="{{ $library->id }}">{{ $library->name }}</label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <hr>

            <div class="form-row">
                <div class="col-12">
                    <h5>Privileges</h5>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="admin" name="admin" value="1">
                            <label class="custom-control-label" for="admin">Admin</label>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="maintainer" name="maintainer" value="1">
                            <label class="custom-control-label" for="maintainer">Maintainer</label>
                        </div>
                    </div>
                </div>
            </div>
            <hr>

            <div class="form-row">
                <div class="col-12 col-lg-2">
                    <div class="form-group">
                        <button class="form-control btn btn-primary" type="submit">
                            <span class="fa fa-check"></span>

                            <span class="d-none d-md-inline-flex">
                                &nbsp;Create
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            {{ Form::close() }}
        </div>
    </div>
@endsection