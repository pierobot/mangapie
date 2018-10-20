@extends ('layout')

@section ('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section ('title')
    Notifications&nbsp;&colon;&colon;&nbsp;Mangapie
@endsection

@section ('custom_navbar_right')
@endsection

@section ('content')

<h3 class="d-flex d-md-none justify-content-center">
    <b id="notification-count">Notifications ({{ $notificationCount }})</b>
</h3>

<h2 class="d-none d-md-flex justify-content-center">
    <b id="notification-count">Notifications ({{ $notificationCount }})</b>
</h2>


<div class="card">
    <div class="card-body p-0">
        {{ Form::open(['action' => 'NotificationController@delete', 'method' => 'delete']) }}
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>Message</th>
                    <th class="d-none d-md-table-cell">Date</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($watchNotifications as $index => $notification)
                @php ($manga = $notification->getData())
                <tr>
                    <th scope="row">
                        <a href="{{ URL::action('MangaController@files', [$manga, 'descending']) }}">
                            <img class="rounded img-fluid" src="{{ URL::action('CoverController@smallDefault', [empty($manga) ? 0 : $manga->getId()]) }}">
                        </a>
                    </th>

                    <td>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="ids[{{ $notification->getId() }}]" name="ids[{{ $notification->getId() }}]" value="{{ $notification->getId() }}">
                            <label class="custom-control-label" for="ids[{{ $notification->getId() }}]">
                                {{ $manga->getName() }}
                            </label>
                        </div>

                        <ul class="list-unstyled ml-4">
                            <li>
                                @if ($notification->getData() instanceof \App\Manga)
                                    <a href="{{ URL::action('MangaController@files', [$manga, 'descending']) }}">
                                        {{ $notification->getMessage() }}
                                    </a>
                                @else
                                    {{ $notification->getMessage() }}
                                @endif
                            </li>
                            <li>
                                <span class="d-inline-block d-md-none text-muted">
                                    {{ $notification->getDateTime()->diffForHumans() }}
                                </span>
                            </li>
                        </ul>
                    </td>

                    <td class="d-none d-md-table-cell">
                        <span class="text-muted">
                            {{ $notification->getDateTime()->diffForHumans() }}
                        </span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        <div class="input-group justify-content-center">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    Dismiss
                </span>
            </div>
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary" id="action" name="action" value="dismiss.selected">Selected</button>
                <button type="submit" class="btn btn-danger" id="action" name="action" value="dismiss.all">All</button>
            </div>
        </div>
    </div>

    {{ Form::close() }}
</div>


@endsection
