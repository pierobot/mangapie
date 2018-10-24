@extends ('admin.users.layout')

@section ('title')
    Admin &middot; Delete User
@endsection

@section ('card-content')
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <a class="nav-link" href="{{ URL::action('AdminController@createUsers') }}">Create</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ URL::action('AdminController@editUsers') }}">Edit</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active">Delete</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            {{ Form::open(['action' => 'UserController@delete', 'method' => 'delete']) }}

            <div class="form-row">
                <div class="col-12 col-lg-6">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    Name
                                </span>
                            </div>

                            <input type="text" class="form-control" title="Name of the user to delete" id="name" name="name">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="col-12 col-lg-2">
                    <div class="form-group">
                        <button class="form-control btn btn-danger" type="submit" title="Are you sure? This will be permanent.">
                            <span class="fa fa-check"></span>

                            <span class="d-none d-md-inline-flex">
                                &nbsp;Delete
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            {{ Form::close() }}
        </div>
    </div>
@endsection