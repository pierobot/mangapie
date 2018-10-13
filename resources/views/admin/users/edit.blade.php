@extends ('admin.users.layout')

@section ('title')
    Admin &middot; Edit User
@endsection

@section ('card-content')
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <a class="nav-link" href="{{ URL::action('AdminController@createUsers') }}">Create</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active">Edit</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ URL::action('AdminController@deleteUsers') }}">Delete</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <h5>Rename</h5>
            {{ Form::open(['action' => 'UserController@edit', 'method' => 'patch']) }}

            <div class="form-row">
                <div class="col-6 col-lg-6">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    Name
                                </span>
                            </div>

                            <input type="text" class="form-control" title="Current name of the user" id="name" name="name">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    New Name
                                </span>
                            </div>

                            <input type="text" class="form-control" title="New name for the user" id="new-name" name="new-name">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="col-12 col-lg-2">
                    <div class="form-group">
                        <button class="form-control btn btn-warning" type="submit">
                            <span class="fa fa-check"></span>

                            <span class="d-none d-md-inline-flex">
                                &nbsp;Edit
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            {{ Form::close() }}
        </div>
    </div>
@endsection