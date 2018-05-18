@extends ('layout')

@section ('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section ('title')
    Notifications&nbsp;&colon;&colon;&nbsp;Mangapie
@endsection

@section ('custom_navbar_right')
    @include ('shared.libraries')
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
                            <img class="notification-img" src="{{ URL::action('ThumbnailController@smallDefault', [empty($manga) ? 0 : $manga->getId()]) }}">
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-xs-12">
                                    <h4 class="text-ellipsis">
                                        <a href="{{ URL::action('MangaController@index', [$manga->getId(), 'descending']) }}">
                                            {{ $manga->getName() }}
                                        </a>
                                    </h4>
                                </div>
                                <div class="col-xs-12">
                                    <div class="row">
                                        <ul>
                                            <li id="watch-notification" type="{{ $notification->getType() }}" value="{{ $notification->getId() }}">
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
                                <button class="btn btn-success" id="btn-dismiss-selected">Dismiss selected</button>
                                <button class="btn btn-danger" id="btn-dismiss-all">Dismiss all</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section ('scripts')
    <script type="text/javascript">
        $(function () {
            $('table.table > tbody > tr').click(function () {
                $(this).toggleClass("active");
            });

            var dismissNotifications = function (all) {
                // get all the selected rows
                var trs = all === true ? $('table.table > tbody > tr') :
                                         $('table.table > tbody > tr.active');
                var watchIds = [];

                // go through each row
                trs.each(function () {
                    // collect the id of the notification
                    var notification = $(this).find('li#watch-notification');
                    var type = $(notification).attr('type');
                    var id = $(notification).attr('value');

                    if (type === 'watch') {
                        watchIds.push(parseInt(id));
                    }
                });

                axios.post("{{ URL::action('NotificationController@dismiss') }}", {
                    'watch': watchIds
                }).then(function () {
                    // everything went ok server side so remove them
                    $(trs).remove();

                    // update the notification counts
                    var notificationCount = $('table.table > tbody > tr').length;
                    // notification count in the navbar
                    $('span#notification-count').html(notificationCount);
                    // the two notification count b elements
                    $('b#notification-count').each(function () {
                        $(this).html('Notifications (' + notificationCount + ')');
                    });
                }).catch(function () {
                    alert('Uh.. something went wrong. *shrug*');
                });
            };

            var dismissButtons = $('#btn-dismiss-selected, #btn-dismiss-all');

            $('#btn-dismiss-selected').click(function () {
                dismissButtons.toggleClass('disabled');

                dismissNotifications(false);

                dismissButtons.toggleClass('disabled');
            });

            $('#btn-dismiss-all').click(function () {
                dismissButtons.toggleClass('disabled');

                dismissNotifications(true);

                dismissButtons.toggleClass('disabled');
            });
        });
    </script>
@endsection
