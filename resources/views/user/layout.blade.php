@extends ('layout')

@section ('content')
    <div class="row">
        <div class="col-xs-12">
            <h3 class="text-center"><b>{{ $user->getName() }}</b></h3>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-2">
            <div class="visible-xs">
                <ul class="nav nav-pills">
                    <li @if ($currentNavPill === 'profile') class="active" @endif><a href="{{ URL::action('UserController@index', [$user->getId()]) }}">Profile</a></li>
                    <li @if ($currentNavPill === 'activity') class="active" @endif><a href="{{ URL::action('UserController@activity', [$user->getId()]) }}">Activity</a></li>
                    <li @if ($currentNavPill === 'comments') class="active" @endif class="disabled"><a>Comments</a></li>
                </ul>
            </div>
            <div class="visible-sm visible-md visible-lg">
                <ul class="nav nav-pills nav-stacked">
                    <li @if ($currentNavPill === 'profile') class="active" @endif><a href="{{ URL::action('UserController@index', [$user->getId()]) }}">Profile</a></li>
                    <li @if ($currentNavPill === 'activity') class="active" @endif><a href="{{ URL::action('UserController@activity', [$user->getId()]) }}">Activity</a></li>
                    <li @if ($currentNavPill === 'comments') class="active" @endif class="disabled"><a>Comments</a></li>
                </ul>
            </div>
        </div>
        <div class="col-xs-12 col-sm-10">
            @yield ('tab-content')
        </div>
    </div>
@endsection
