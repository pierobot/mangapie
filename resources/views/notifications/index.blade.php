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

<h3 class="visible-xs text-center">
    <b id="notification-count">Notifications ({{ $notificationCount }})</b>
</h3>

<h2 class="hidden-xs text-center">
    <b id="notification-count">Notifications ({{ $notificationCount }})</b>
</h2>

<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default">
            {{ Form::open(['action' => 'NotificationController@dismiss']) }}
            <table class="table table-hover table-condensed table-va-middle">
                <thead>
                    <tr>
                        <th class="col-xs-2 col-sm-1"></th>
                        <th class="col-xs-6 col-sm-8">Message</th>
                        <th class="col-xs-4 col-sm-3">Date</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($watchNotifications as $index => $notification)
                    @php ($manga = $notification->getData())
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-xs-12">
                                    <a href="{{ URL::action('MangaController@index', [$manga->getId(), 'descending']) }}">
                                        <img class="notification-img" src="{{ URL::action('CoverController@smallDefault', [empty($manga) ? 0 : $manga->getId()]) }}">
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="checkbox checkbox-success">
                                        <input type="checkbox" id="ids[{{ $notification->getId() }}]" name="ids[{{ $notification->getId() }}]" value="{{ $notification->getId() }}">
                                        <label for="ids[{{ $notification->getId() }}]">
                                            <div class="truncate-ellipsis">
                                                <span>{{ $manga->getName() }}</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <div class="row">
                                        <ul>
                                            <li>
                                                {{ $notification->getMessage() }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                {{ $notification->getDateTime()->diffForHumans() }}
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="panel-footer">
                <div class="panel-heading">
                    <div class="panel-title">
                        <div class="row">
                            <div class="col-xs-12 text-center">
                                <button type="submit" class="btn btn-success" id="action" name="action" value="dismiss.selected">Dismiss selected</button>
                                <button type="submit" class="btn btn-danger" id="action" name="action" value="dismiss.all">Dismiss all</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{ Form::close() }}
        </div>
    </div>

</div>

@endsection
