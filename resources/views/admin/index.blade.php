@extends ('layout')

@section ('title')
:: Dashboard
@endsection

@section ('stylesheets')

    <link href="{{ \Config::get('mangapie.app_url') }}/public/css/admin.css" rel="stylesheet">

@endsection

@section ('content')

<div class="panel panel-default">

    <div class="panel-heading">
        <h2 class="panel-title">Dashboard</h2>
    </div>

    <div class="panel-body">

        <ul class="list-group">
            <li class="list-group-item">
                <label>Number of admins:</label> {{ $admin_count }} <br>
                <label>Number of users:</label> {{ $user_count }} <br>
            </li>
        </ul>

    </div>

</div>

@endsection
