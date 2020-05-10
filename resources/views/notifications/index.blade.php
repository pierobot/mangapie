@extends ('layout')

@section ('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section ('title')
    Notifications&nbsp;&colon;&colon;&nbsp;Mangapie
@endsection

@section ('content')
    <div class="container mt-3">
        <div class="d-flex justify-content-center">
            <h4><strong id="notification-count">Notifications ({{ $notificationCount }})</strong></h4>
        </div>

        <div class="card">
            <div class="card-body">
                {{ Form::open(['action' => 'NotificationController@destroy', 'method' => 'delete']) }}
                <table class="table">
                    <thead>
                        <tr class="d-flex">
                            <th class="col-4 col-md-2 col-xl-1"></th>
                            <th class="col-8 col-md-6 col-xl-7">Message</th>
                            <th class="d-none col-md-4 col-xl-4 d-md-flex">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($watchNotifications as $index => $notification)
                        @php ($manga = $notification->getData())
                        <tr class="d-flex">
                            <th scope="row" class="col-4 col-md-2 col-xl-1">
                                <a href="{{ URL::action('MangaController@files', [$manga, 'sort' => 'desc']) }}">
                                    <img class="img-fluid rounded" src="{{ URL::action('CoverController@smallDefault', [empty($manga) ? 0 : $manga->getId()]) }}">
                                </a>
                            </th>

                            <td class="col-8 col-md-6 col-xl-7">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="ids[{{ $notification->getId() }}]" name="ids[{{ $notification->getId() }}]" value="{{ $notification->getId() }}">
                                    <label class="custom-control-label pt-1" for="ids[{{ $notification->getId() }}]">
                                        {{ $manga->getName() }}
                                    </label>
                                </div>

                                <ul class="list-unstyled ml-4 mt-2">
                                    <li>
                                        @if ($notification->getData() instanceof \App\Manga)
                                            <a href="{{ URL::action('MangaController@files', [$manga, 'sort' => 'desc']) }}">
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

                            <td class="d-none col-md-4 col-xl-4 d-md-flex">
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
    </div>


@endsection
