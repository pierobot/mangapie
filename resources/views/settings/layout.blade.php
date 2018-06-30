@extends ('layout')

@section ('content')
    <div class="visible-xs">
        <h3 class="text-center"><b>Settings</b></h3>
    </div>
    <div class="visible-sm visible-md visible-lg">
        <h2 class="text-center"><b>Settings</b></h2>
    </div>

    @include ('shared.success')
    @include ('shared.errors')

    <div class="row">
            <div class="col-xs-12 col-sm-2">
                <div class="visible-xs">
                    <ul class="nav nav-pills">
                        <li @if ($currentNavPill === 'account') class="active" @endif><a href="{{ URL::action('UserSettingsController@account') }}">Account</a></li>
                        <li @if ($currentNavPill === 'visuals') class="active" @endif><a href="{{ URL::action('UserSettingsController@visuals') }}">Visuals</a></li>
                        <li @if ($currentNavPill === 'profile') class="active" @endif><a href="{{ URL::action('UserSettingsController@profile') }}">Profile</a></li>
                    </ul>
                </div>
                <div class="visible-sm visible-md visible-lg">
                    <ul class="nav nav-pills nav-stacked">
                        <li @if ($currentNavPill === 'account') class="active" @endif><a href="{{ URL::action('UserSettingsController@account') }}">Account</a></li>
                        <li @if ($currentNavPill === 'visuals') class="active" @endif><a href="{{ URL::action('UserSettingsController@visuals') }}">Visuals</a></li>
                        <li @if ($currentNavPill === 'profile') class="active" @endif><a href="{{ URL::action('UserSettingsController@profile') }}">Profile</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-xs-12 col-sm-10">
                @yield('tab-content')
            </div>
    </div>
@endsection
