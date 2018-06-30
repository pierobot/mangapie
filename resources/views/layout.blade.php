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

<div class="navbar navbar-default navbar-static-top @if (! empty($page_count)) reader @endif">
    <div class="container">
        <div class="navbar-header">
            <span class="btn btn-navbar navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-div" aria-expanded="false">
                <span class="glyphicon glyphicon-menu-hamburger"></span>
            </span>

            <a href="{{ URL::action('HomeController@index') }}">
                {{--<img class="navbar-brand" src="{{ URL::to('/public/mangapie.svg') }}">--}}
                {{ Html::link(URL::action('HomeController@index'), 'MangaPie', ['class' => 'navbar-brand']) }}
            </a>
        </div>

        <div class="collapse navbar-collapse" id="navbar-collapse-div">
            <div class="container-fluid">

                @include ('shared.searchbar')

                <ul class="nav navbar-nav navbar-right">

                    @include ('shared.notifications')

                    @yield ('custom_navbar_right')

                    @auth
                        @admin
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="glyphicon glyphicon-wrench"></span>&nbsp;Admin&nbsp;<span class="glyphicon glyphicon-chevron-down white"></span>
                            </a>
                            <ul class="dropdown-menu" style="color: black;">
                                <li>
                                    <a href="{{ URL::action('AdminController@index') }}"><span class="glyphicon glyphicon-th-large"></span>&nbsp;Dashboard</a>
                                </li>
                                <li><a href="{{ URL::action('AdminController@users') }}"><span class="glyphicon glyphicon-user"></span>&nbsp;Users</a></li>
                                <li>
                                    <a href="{{ URL::action('AdminController@libraries') }}"><span class="glyphicon glyphicon-book"></span>&nbsp;Libraries</a>
                                </li>
                            </ul>
                        </li>
                        @endadmin

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="glyphicon glyphicon-user white"></span>&nbsp; {{ Auth::user()->getName() }} &nbsp;<span class="glyphicon glyphicon-chevron-down white"></span>
                            </a>
                            <ul class="dropdown-menu" style="color: black;">
                                <li>
                                    @auth
                                    <a href="{{ URL::action('UserController@index', [\Auth::user()->getId()]) }}">&nbsp;Profile</a>
                                    @endauth
                                    <a href="{{ URL::action('FavoriteController@index') }}"><span class="glyphicon glyphicon-heart"></span>&nbsp;Favorites</a>
                                    <a href="{{ URL::action('UserSettingsController@index') }}"><span class="glyphicon glyphicon-cog"></span>&nbsp;Settings</a>
                                    <a href="{{ URL::action('LoginController@logout') }}"><span class="glyphicon glyphicon-off"></span>&nbsp;Logout</a>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="container">
    @yield ('content')
</div>

@auth
    @include ('shared.autocomplete')
    @yield ('scripts')
@endauth

</body>
