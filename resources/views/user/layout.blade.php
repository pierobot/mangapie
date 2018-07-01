@extends ('layout')

@section ('content')
    <div class="visible-xs">
        <br>
        <div class="row">
            <div class="col-xs-12">
                <div class="thumbnail">
                    <img class="img-rounded" src="{{ URL::action('AvatarController@index', [$user->getId()]) }}">
                    <h3 class="text-center">{{ $user->getName() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-2">
            <div class="visible-xs">
                <ul class="nav nav-pills">
                    <li @if ($currentNavPill === 'profile') class="active" @endif><a href="{{ URL::action('UserController@index', [$user->getId()]) }}">Profile</a></li>
                    <li @if ($currentNavPill === 'activity') class="active" @endif><a href="{{ URL::action('UserController@activity', [$user->getId()]) }}">Activity</a></li>
                    <li class="disabled"><a>Comments</a></li>
                </ul>
            </div>
            <div class="hidden-xs text-center">
                <div class="thumbnail">
                    <img class="img-rounded" src="{{ URL::action('AvatarController@index', [$user->getId()]) }}">
                    <h3>{{ $user->getName() }}</h3>
                </div>
                <ul class="nav nav-pills nav-stacked">
                    <li @if ($currentNavPill === 'profile') class="active" @endif><a href="{{ URL::action('UserController@index', [$user->getId()]) }}">Profile</a></li>
                    <li @if ($currentNavPill === 'activity') class="active" @endif><a href="{{ URL::action('UserController@activity', [$user->getId()]) }}">Activity</a></li>
                    <li class="disabled"><a>Comments</a></li>
                </ul>
            </div>
        </div>
        <div class="col-xs-12 col-sm-10">
            @yield ('tab-content')
        </div>
    </div>
@endsection
