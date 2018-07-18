@extends ('layout')

@section ('title')
    Admin &middot; Dashboard
@endsection

@section ('custom_navbar_right')
@endsection

@section ('content')
    <h2 class="text-center"><b>Dashboard</b></h2>

    @include ('shared.errors')
    @include ('shared.success')

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="glyphicon glyphicon-info-sign"></span>&nbsp;Statistics
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 col-md-4">
                    <div class="col-xs-12">
                        <h4><b>Users</b></h4>
                    </div>
                    <div class="col-xs-12">
                        <label>Number of admins:</label> {{ $admin_count }} <br>
                        <label>Number of users:</label> {{ $user_count }} <br>
                    </div>
                    <div class="col-xs-12">
                        <hr>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="col-xs-12">
                        <h4><b>Avatars</b></h4>
                    </div>
                    <div class="col-xs-12">
                        <label>Size:</label> {{ App\Archive::convertSizeToReadable(App\Avatar::size()) }}<br>
                        <label>Path:</label> {{ App\Avatar::disk()->path('') }}
                    </div>
                    <div class="col-xs-12">
                        <hr>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="col-xs-12">
                        <h4><b>Covers</b></h4>
                    </div>
                    <div class="col-xs-12">
                        <label>Size:</label> {{ App\Archive::convertSizeToReadable(App\Cover::size()) }}<br>
                        <label>Path:</label> {{ App\Cover::disk()->path('') }}
                    </div>
                    <div class="col-xs-12">
                        <hr>
                    </div>
                </div>

                <div class="col-xs-12 col-md-4">
                    <div class="col-xs-12">
                        <h4><b>Images</b></h4>
                    </div>
                    <div class="col-xs-12">
                        <label>Size:</label> {{ App\Archive::convertSizeToReadable(App\Image::size()) }}<br>
                        <label>Path:</label> {{ App\Image::disk()->path('') }}
                    </div>
                    <div class="col-xs-6">
                        {{ Form::open(['action' => 'AdminController@patchImages', 'method' => 'patch']) }}
                        {{ Form::submit('Clean', ['class' => 'btn btn-warning form-control']) }}
                        {{ Form::close() }}
                    </div>
                    <div class="col-xs-6">
                        {{ Form::open(['action' => 'AdminController@deleteImages', 'method' => 'delete']) }}
                        {{ Form::submit('Wipe', ['class' => 'btn btn-danger form-control']) }}
                        {{ Form::close() }}
                    </div>
                    <div class="col-xs-12">
                        <hr>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="glyphicon glyphicon-info-sign"></span>&nbsp;Warnings
                @if(empty($warnings) == false)
                    <span class="label label-warning">{{ count($warnings) }}</span>
                @else
                    <span class="label label-success">0</span>
                @endif
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-hover table-condensed">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($warnings as $warning)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($warning['datetime'])->diffForHumans() }}</td>
                            <td>{{ $warning['messagectx'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
