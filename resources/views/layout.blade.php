<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield ('title')</title>

    <link href="{{ URL::to('public/assets/mangapie.css') }}" rel="stylesheet">

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
                <ul class="nav navbar-nav text-right">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ URL::action('AdminController@index') }}">
                            <span class="fa fa-dashboard"></span>
                            &nbsp;Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ URL::action('AdminController@users') }}">
                            <span class="fa fa-users"></span>
                            &nbsp;Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ URL::action('AdminController@libraries') }}">
                            <span class="fa fa-book"></span>
                            &nbsp;Libraries
                        </a>
                    </li>
                </ul>
            </div>
        @endadmin

        <div class="collapse navbar-collapse" id="menu-collapse">
            <ul class="nav navbar-nav">
                {{--<div class="d-block d-sm-none">--}}
                    {{--@include ('shared.searchbar')--}}
                {{--</div>--}}

                @auth
                    <li class="dropdown text-right">
                        <a href="#" class="nav-link dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="glyphicon glyphicon-user white"></span>&nbsp; {{ Auth::user()->getName() }} &nbsp;<span class="glyphicon glyphicon-chevron-down white"></span>
                        </a>
                        <ul class="dropdown-menu border-0">
                            <li class="nav-item">
                                <a class="dropdown-item text-right" href="{{ URL::action('UserController@index', [\Auth::user()->getId()]) }}">&nbsp;Profile</a>
                                <a class="dropdown-item text-right" href="{{ URL::action('FavoriteController@index') }}"><span class="glyphicon glyphicon-heart"></span>&nbsp;Favorites</a>
                                <a class="dropdown-item text-right" href="{{ URL::action('UserSettingsController@index') }}"><span class="glyphicon glyphicon-cog"></span>&nbsp;Settings</a>
                                <a class="dropdown-item text-right" href="{{ URL::action('LoginController@logout') }}"><span class="glyphicon glyphicon-off"></span>&nbsp;Logout</a>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    @yield ('content')
</div>

@yield ('footer-contents')

@auth
    @include ('shared.autocomplete')
    @yield ('scripts')
@endauth

</body>
