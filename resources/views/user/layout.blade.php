@extends ('layout')

@section ('content')
    <div class="row">
        <div class="col-xs-12">
            <h3 class="text-center"><b>{{ $user->getName() }}</b></h3>
        </div>
    </div>
    <div class="row">
        <div class="visible-xs">
            <div class="col-xs-12">
                <ul class="nav nav-pills">
                    <li><a href="{{ URL::action('UserController@index', [$user->getId()]) }}">Profile</a></li>
                    <li><a href="{{ URL::action('UserController@activity', [$user->getId()]) }}">Activity</a></li>
                    <li class="disabled"><a href="">Comments</a></li>
                </ul>
            </div>
            <div class="col-xs-12">
                @yield ('tab-content')
            </div>
        </div>

        <div class="visible-sm visible-md visible-lg">
            <div class="col-sm-2">
                <ul class="nav nav-pills nav-stacked">
                    <li><a href="{{ URL::action('UserController@index', [$user->getId()]) }}">Profile</a></li>
                    <li><a href="{{ URL::action('UserController@activity', [$user->getId()]) }}">Activity</a></li>
                    <li class="disabled"><a href="">Comments</a></li>
                </ul>
            </div>
            <div class="col-sm-10">
                @yield ('tab-content')
            </div>
        </div>
    </div>
@endsection
