@extends ('admin.layout')

@section ('title')
    Create Library :: Mangapie
@endsection

@section ('side-top-menu')
    <div class="list-group side-top-menu d-inline-flex d-md-flex flex-wrap flex-md-nowrap flex-row flex-md-column mb-3 mb-md-auto">
        <a class="list-group-item" href="{{ URL::action('AdminController@index') }}">
            <span class="fa fa-dashboard"></span>

            <div class="d-none d-md-inline-block">
                &nbsp;Dashboard
            </div>
        </a>

        <a class="list-group-item active" href="{{ URL::action('AdminController@libraries') }}">
            <span class="fa fa-book"></span>

            <div class="d-none d-md-inline-block">
                &nbsp;Libraries
            </div>
        </a>

        <a class="list-group-item" href="{{ URL::action('AdminController@users') }}">
            <span class="fa fa-users"></span>

            <div class="d-none d-md-inline-block">
                &nbsp;Users
            </div>
        </a>

        <a class="list-group-item" href="{{ URL::action('AdminController@logs') }}">
            <span class="fa fa-clipboard-list"></span>

            <div class="d-none d-md-inline-block">
                &nbsp;Logs
            </div>
        </a>
    </div>
@endsection

@section ('card-content')
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <a class="nav-link active">Create</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ URL::action('AdminController@modifyLibraries') }}">Modify</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ URL::action('AdminController@deleteLibraries') }}">Delete</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            {{ Form::open(['action' => 'LibraryController@create', 'method' => 'put']) }}

            <div class="form-row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Name</span>
                            </div>

                            <input type="text" class="form-control" title="Name" id="create-name" name="name">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Path</span>
                            </div>

                            <input type="text" class="form-control" title="Filesystem path" id="create-path" name="path">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="col-12 col-md-2">
                    <div class="form-group">
                        <button type="submit" class="form-control btn btn-primary">
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
