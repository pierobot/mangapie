@extends ('admin.layout')

@section ('title')
    Admin &middot; Dashboard
@endsection

@section ('side-top-menu')
    <div class="list-group side-top-menu d-inline-flex d-md-flex flex-wrap flex-md-nowrap flex-row flex-md-column mb-3 mb-md-auto">
        <a class="list-group-item active" href="{{ URL::action('AdminController@index') }}">
            <span class="fa fa-dashboard"></span>

            <div class="d-none d-md-inline-block">
                &nbsp;Dashboard
            </div>
        </a>

        <a class="list-group-item" href="{{ URL::action('AdminController@libraries') }}">
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
                    <a class="nav-link active">Statistics</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="col-12">
                        <h4><b>Users</b></h4>
                    </div>
                    <div class="col-12">
                        <label>Number of admins:</label> {{ \App\User::admin('id')->count() }} <br>
                        <label>Number of maintainers:</label> {{ \App\User::maintainer('id')->count() }} <br>
                        <label>Number of users:</label> {{ \App\User::all('id')->count() }}
                    </div>
                    <div class="col-12">
                        <hr>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="col-12">
                        <h4><b>Avatars</b></h4>
                    </div>
                    <div class="col-12">
                        <label>Size:</label> {{ App\Archive::convertSizeToReadable(App\Avatar::size()) }}<br>
                        <label>Path:</label> {{ App\Avatar::disk()->path('') }}
                    </div>
                    <div class="col-12">
                        <hr>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="col-12">
                        <h4><b>Covers</b></h4>
                    </div>
                    <div class="col-12">
                        <label>Size:</label> {{ App\Archive::convertSizeToReadable(App\Cover::size()) }}<br>
                        <label>Path:</label> {{ App\Cover::disk()->path('') }}
                    </div>
                    <div class="col-12">
                        <hr>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="col-12">
                        <h4><b>Images</b></h4>
                    </div>
                    <div class="col-12">
                        <label>Size:</label> {{ App\Archive::convertSizeToReadable(App\Image::size()) }}<br>
                        <label>Path:</label> {{ App\Image::disk()->path('') }}
                    </div>
                    <div class="col-6">
                        {{ Form::open(['action' => 'AdminController@patchImages', 'method' => 'patch']) }}
                        {{ Form::submit('Clean', ['class' => 'btn btn-warning form-control']) }}
                        {{ Form::close() }}
                    </div>
                    <div class="col-6">
                        {{ Form::open(['action' => 'AdminController@deleteImages', 'method' => 'delete']) }}
                        {{ Form::submit('Wipe', ['class' => 'btn btn-danger form-control']) }}
                        {{ Form::close() }}
                    </div>
                    <div class="col-12">
                        <hr>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
