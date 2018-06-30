@auth
    <li @if ($notificationCount > 0) class="wiggle" @endif>
        <a href="{{ URL::action('NotificationController@index') }}">
            <span class="glyphicon glyphicon-bell"></span>&nbsp;Notifications&nbsp;

            @if ($notificationCount > 0)
                <span class="badge" id="notification-count">{{ $notificationCount }}</span>
            @endif
        </a>
    </li>
@endauth
