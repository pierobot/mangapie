<div class="container mt-3">
    <div class="row">
        <div class="col">
            <h3><strong>Notifications</strong></h3>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link @if ($active === 'archives') active @endif" href="{{ URL::action('NotificationController@archives') }}">
                        Archives
                        @if ($archiveNotificationCount > 0)
                            <span class="badge badge-pill badge-warning">{{ $archiveNotificationCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if ($active === 'mentions') active @endif disabled" href="#">
                        Mentions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if ($active === 'moderation') active @endif disabled" href="#">
                        Moderation
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if ($active === 'edits') active @endif disabled" href="#">
                        Edits
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
