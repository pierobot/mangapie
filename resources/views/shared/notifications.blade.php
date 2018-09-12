{{--The ml-auto is important.--}}
{{--If another button is to be placed before this one, then move the ml-auto to that one.--}}
@auth
    <a class="btn ml-auto @if (! empty($notificationCount)) btn-outline-warning @endif" type="button" href="{{ URL::action('NotificationController@index') }}" title="{{ $notificationCount }} notification(s)">
        <span class="fa fa-bell"></span>
    </a>
@endauth