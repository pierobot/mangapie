<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>MangaPie @yield ('title')</title>

    <!-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="{{ URL::to('/public/css/bootswatch-slate.min.css') }}" rel="stylesheet">
    <link href="{{ URL::to('/public/css/layout.css') }}" rel="stylesheet">

    @yield ('stylesheets')

    <script src="{{ URL::to('/public/jquery/jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('/public/bootstrap/dist/js/bootstrap.min.js') }}" type="text/javascript"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<div class="navbar navbar-default navbar-static-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="btn btn-navbar navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-div" aria-expanded="false">
                <span class="glyphicon icon-bar"></span>
                <span class="glyphicon icon-bar"></span>
                <span class="glyphicon icon-bar"></span>
            </a>

            {{ Html::link(URL::action('MangaController@index'), 'MangaPie', ['class' => 'navbar-brand']) }}
        </div>

        <div class="collapse navbar-collapse" id="navbar-collapse-div">
            <ul class="nav navbar-nav navbar-right">

            @yield ('custom_navbar_right')

            @if (Auth::id() != null)

                @if (\App\User::find(Auth::id())['admin'] == true)
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="glyphicon glyphicon-chevron-down white"></span>Admin
                        </a>
                        <ul class="dropdown-menu" style="color: black;">
                            <li>
                                <a href="{{ URL::action('AdminController@index') }}"><span class="glyphicon glyphicon-th-large"></span>&nbsp; Dashboard</a>
                                <a href="{{ URL::action('AdminController@users') }}"><span class="glyphicon glyphicon-user"></span>&nbsp; Users</a>
                                <a href="{{ URL::action('AdminController@libraries') }}"><span class="glyphicon glyphicon-book"></span>&nbsp; Libraries</a>
                            </li>
                        </ul>
                    </li>
                @endif

                    <li>
                        <a href="{{ URL::action('LoginController@logout') }}"><span class="glyphicon glyphicon-off"></span> Logout</a>
                    </li>
            @endif
            </ul>
        </div>
    </div>
</div>

<div class="container">

@yield ('content')

</div>

@yield ('scripts')

</body>
