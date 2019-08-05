@extends ('admin.layout')

@section ('title')
    Admin &middot; Statistics
@endsection

@section ('top-menu')
    <ul class="nav nav-pills mb-3 justify-content-center">
        <li class="nav-item"><a href="#" class="nav-link active">Statistics</a></li>
        <li class="nav-item"><a href="{{ URL::action('AdminController@config') }}" class="nav-link">Config</a></li>
        <li class="nav-item"><a href="{{ URL::action('AdminController@libraries') }}" class="nav-link">Libraries</a></li>
        <li class="nav-item"><a href="{{ URL::action('AdminController@users') }}" class="nav-link">Users</a></li>
    </ul>
@endsection

@section ('card-content')
    <hr>
    <div class="row">
        <div class="col-12 col-md-6 col-lg-4">
            <h4><b>Users</b></h4>

            <label>Number of admins:</label> {{ \App\User::admins('id')->count() }} <br>
            <label>Number of maintainers:</label> {{ \App\User::maintainers('id')->count() }} <br>
            <label>Number of users:</label> {{ \App\User::all('id')->count() }}

            <hr>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <h4><b>Avatars</b></h4>

            <label>Size:</label> {{ App\Archive::convertSizeToReadable(App\Avatar::size()) }}<br>
            <label>Path:</label> {{ App\Avatar::disk()->path('') }}

            <hr>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <h4><b>Covers</b></h4>

            <label>Size:</label> {{ App\Archive::convertSizeToReadable(App\Cover::size()) }}<br>
            <label>Path:</label> {{ App\Cover::disk()->path('') }}

            <hr>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <h4><b>Images</b></h4>

            <label>Size:</label> {{ App\Archive::convertSizeToReadable(App\Image::size()) }}<br>
            <label>Path:</label> {{ App\Image::disk()->path('') }}

            <div class="row mt-3">
                <div class="col-6">
                    {{ Form::open(['action' => 'AdminController@patchImages', 'method' => 'patch']) }}

                    <button class="btn btn-warning form-control" type="submit">
                        <span class="fa fa-broom"></span>&nbsp;Clean
                    </button>
                    {{ Form::close() }}
                </div>
                <div class="col-6">
                    {{ Form::open(['action' => 'AdminController@deleteImages', 'method' => 'delete']) }}
                    <button class="btn btn-danger form-control">
                        <span class="fa fa-trash"></span>&nbsp;Wipe
                    </button>
                    {{ Form::close() }}
                </div>

                <div class="col-12">
                    <p class="text-warning">Clean will remove images based on heat &mdash; if enabled.</p>
                    <p class="text-danger">Wipe will literally remove <strong>all images</strong>.</p>
                </div>
            </div>

            <hr>
        </div>
    </div>
@endsection