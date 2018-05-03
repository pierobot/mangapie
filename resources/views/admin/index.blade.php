@extends ('layout')

@section ('title')
    Admin &middot; Dashboard
@endsection

@section ('custom_navbar_right')
    @include ('shared.searchbar')
    @include ('shared.libraries')
@endsection

@section ('content')
    <h2 class="text-center"><b>Dashboard</b></h2>

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="glyphicon glyphicon-info-sign"></span>&nbsp;Statistics
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <ul class="list-group">
                    <div class="col-xs-12 col-md-4">
                        <h4>Users</h4>
                        <hr>
                        <div class="col-xs-12">
                            <label>Number of admins:</label> {{ $admin_count }} <br>
                            <label>Number of users:</label> {{ $user_count }} <br>
                        </div>
                    </div>
                </ul>
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
                            <td>{{ $warning['datetime'] }}</td>
                            <td>{{ $warning['messagectx'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
