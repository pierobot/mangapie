<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale = 1, maximum-scale = 1, user-scalable = no">

    <title>@yield ('title')</title>

    @if (Auth::check() == false)
        <link href="{{ URL::to('/public/themes/bootswatch/slate/bootstrap.min.css') }}" rel="stylesheet">
    @else
        <link href="{{ URL::to(\App\Theme::path(Auth::user()->getTheme())) }}" rel="stylesheet">
    @endif
    <link href="{{ URL::to('/public/css/layout.css') }}" rel="stylesheet">

    @yield ('stylesheets')

    <script src="{{ URL::to('/public/jquery/jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::to('/public/bootstrap/dist/js/bootstrap.min.js') }}" type="text/javascript"></script>
</head>
<body>

@if (empty($page_count) == true)
<div class="navbar navbar-default navbar-static-top">
@else
<div class="reader navbar navbar-default navbar-static-top">
@endif
    <div class="container">
        <div class="navbar-header">
            <a class="btn btn-navbar navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-div" aria-expanded="false">
                <span class="glyphicon icon-bar"></span>
                <span class="glyphicon icon-bar"></span>
                <span class="glyphicon icon-bar"></span>
            </a>

            {{ Html::link(URL::action('HomeController@index'), 'MangaPie', ['class' => 'navbar-brand']) }}
        </div>

        <div class="collapse navbar-collapse" id="navbar-collapse-div">
            <ul class="nav navbar-nav navbar-right">

            @yield ('custom_navbar_right')

            @if (Auth::check())
                @if (Auth::user()->isAdmin() == true)
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="glyphicon glyphicon-wrench"></span>&nbsp;Admin&nbsp;<span class="glyphicon glyphicon-chevron-down white"></span>
                        </a>
                        <ul class="dropdown-menu" style="color: black;">
                            <li>
                                <a href="{{ URL::action('AdminController@index') }}"><span class="glyphicon glyphicon-th-large"></span>&nbsp;Dashboard</a>
                                <a href="{{ URL::action('AdminController@users') }}"><span class="glyphicon glyphicon-user"></span>&nbsp;Users</a>
                                <a href="{{ URL::action('AdminController@libraries') }}"><span class="glyphicon glyphicon-book"></span>&nbsp;Libraries</a>
                            </li>
                        </ul>
                    </li>
                @endif

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="glyphicon glyphicon-user white"></span>&nbsp; {{ Auth::user()->getName() }} &nbsp;<span class="glyphicon glyphicon-chevron-down white"></span>
                    </a>
                    <ul class="dropdown-menu" style="color: black;">
                        <li>
                            <a href="{{ URL::action('FavoriteController@index') }}"><span class="glyphicon glyphicon-heart"></span>&nbsp;Favorites</a>
                            <a href="{{ URL::action('UserSettingsController@index') }}"><span class="glyphicon glyphicon-cog"></span>&nbsp;Settings</a>
                            <a href="{{ URL::action('LoginController@logout') }}"><span class="glyphicon glyphicon-off"></span>&nbsp;Logout</a>
                        </li>
                    </ul>
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
