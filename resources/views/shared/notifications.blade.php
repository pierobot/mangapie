{{--The ml-auto is important.--}}
{{--If another button is to be placed before this one, then move the ml-auto to that one.--}}
@auth
    <a class="ml-auto" href="{{ URL::action('NotificationController@index') }}" title="{{ $notificationCount }} notification(s)">
        <button class="navbar-toggler @if (! empty($notificationCount)) btn btn-outline-secondary @endif" type="button">
            <span class="fa fa-bell text-warning"></span>
        </button>
    </a>
@endauth