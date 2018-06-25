@extends ('layout')

@section ('title')
    Settings &middot; {{ \Auth::user()->getName() }}
@endsection

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
        <div class="visible-xs">
            <div class="col-xs-12">
                <ul class="nav nav-pills">
                    <li role="presentation">
                        <a href="{{ URL::action('UserSettingsController@account') }}">Account</a>
                    </li>

                    <li role="presentation">
                        <a href="{{ URL::action('UserSettingsController@visuals') }}">Visuals</a>
                    </li>
                </ul>
            </div>
            <div class="col-xs-12">
                @yield('tab-content')
            </div>
        </div>

        <div class="visible-sm visible-md visible-lg">
            <div class="col-sm-2">
                <ul class="nav nav-pills nav-stacked">
                    <li role="presentation">
                        <a href="{{ URL::action('UserSettingsController@account') }}">Account</a>
                    </li>

                    <li role="presentation">
                        <a href="{{ URL::action('UserSettingsController@visuals') }}">Visuals</a>
                    </li>
                </ul>
            </div>
            <div class="col-sm-10">
                @yield('tab-content')
            </div>
        </div>
    </div>
@endsection
