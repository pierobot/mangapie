@extends ('admin.dashboard.layout')

@section ('title')
    Admin &middot; Statistics
@endsection

@section ('card-content')
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <a class="nav-link" href="{{ URL::action('AdminController@config') }}">Config</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="#">Statistics</a>
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
                        <label>Number of admins:</label> {{ \App\User::admins('id')->count() }} <br>
                        <label>Number of maintainers:</label> {{ \App\User::maintainers('id')->count() }} <br>
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
