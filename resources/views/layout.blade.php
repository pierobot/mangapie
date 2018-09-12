<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield ('title')</title>

    <link href="{{ URL::to('public/assets/mangapie.css') }}" rel="stylesheet">
    {{--@auth--}}
        {{--<link href="{{ URL::to(\App\Theme::path(Auth::user()->getTheme())) }}" rel="stylesheet">--}}
    {{--@else--}}

        {{--<link href="{{ URL::to('/public/css/mangapie.css') }}" rel="stylesheet">--}}

    {{--@endauth--}}

    @yield ('stylesheets')

    <script src="{{ URL::to('public/assets/mangapie.js') }}"></script>
</head>
<body>

<nav class="navbar navbar-dark bg-dark sticky-top @if (! empty($page_count)) reader @endif">
    <div class="container">
        <a class="navbar-brand" href="{{ URL::action('HomeController@index') }}">Mangapie</a>

        <div class="d-none d-md-block">
            @include ('shared.searchbar')
        </div>

        @include ('shared.notifications')

        @admin
            <div class="ml-1 mr-1"></div>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#admin-collapse" aria-expanded="false">
                <span class="fa fa-wrench"></span>
            </button>
        @endadmin

        <div class="ml-1 mr-1"></div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#menu-collapse" aria-expanded="false">
            <span class="fa fa-navicon"></span>
        </button>

        @admin
            <div class="collapse navbar-collapse" id="admin-collapse">
                <ul class="nav navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link">
                            <span class="fa fa-users">&nbsp;Users</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link">
                            <span class="fa fa-list">&nbsp;Libraries</span>
                        </a>
                    </li>
                </ul>
            </div>
        @endadmin

        <div class="collapse navbar-collapse" id="menu-collapse">
            <ul class="nav navbar-nav">
                <div class="d-block d-sm-none">
                    @include ('shared.searchbar')
                </div>

                {{--@yield ('custom_navbar_right')--}}

                @auth
                    @admin
                    <li class="dropdown text-right">
                        <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="glyphicon glyphicon-wrench"></span>&nbsp;Admin&nbsp;<span class="glyphicon glyphicon-chevron-down white"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="nav-item"><a class="nav-link" href="{{ URL::action('AdminController@index') }}"><span class="glyphicon glyphicon-th-large"></span>&nbsp;Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ URL::action('AdminController@users') }}"><span class="glyphicon glyphicon-user"></span>&nbsp;Users</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ URL::action('AdminController@libraries') }}"><span class="glyphicon glyphicon-book"></span>&nbsp;Libraries</a></li>
                        </ul>
                    </li>
                    @endadmin

                    <li class="dropdown text-right">
                        <a href="#" class="nav-link dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="glyphicon glyphicon-user white"></span>&nbsp; {{ Auth::user()->getName() }} &nbsp;<span class="glyphicon glyphicon-chevron-down white"></span>
                        </a>
                        <ul class="dropdown-menu bg-dark text-secondary">
                            <li class="nav-item text-right">
                                @auth
                                    <a class="nav-link" href="{{ URL::action('UserController@index', [\Auth::user()->getId()]) }}">&nbsp;Profile</a>
                                @endauth
                                <a class="nav-link" href="{{ URL::action('FavoriteController@index') }}"><span class="glyphicon glyphicon-heart"></span>&nbsp;Favorites</a>
                                <a class="nav-link" href="{{ URL::action('UserSettingsController@index') }}"><span class="glyphicon glyphicon-cog"></span>&nbsp;Settings</a>
                                <a class="nav-link" href="{{ URL::action('LoginController@logout') }}"><span class="glyphicon glyphicon-off"></span>&nbsp;Logout</a>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<div class="container h-100">
    @yield ('content')
</div>

@yield ('footer-contents')

@auth
    {{--@include ('shared.autocomplete')--}}
    @yield ('scripts')
@endauth

</body>
