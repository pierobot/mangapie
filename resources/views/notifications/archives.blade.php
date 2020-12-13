@extends ('notifications.layout')

@section ('title')
    Notifications&nbsp;&colon;&colon;&nbsp;Mangapie
@endsection

@section ('notification-content')
    @component('notifications.components.menu', [
        'active' => 'archives',
        'archiveNotificationCount' => $archiveNotifications->count(),
    ])
    @endcomponent

    <div class="container">
        <div class="row mt-3">
            <div class="col">
                @include('shared.success')
                @include('shared.errors')
            </div>
        </div>
        @if (! $archiveNotifications->count())
            <div class="row">
                <div class="col">
                    You have no archive notifications.
                </div>
            </div>
        @else
            <div class="row">
                <div class="col">
                    <table class="table table-borderless table-striped" style="table-layout: fixed;">
                        <thead>
                        <tr class="d-flex">
                            <th class="col-4 col-sm-2 col-lg-1">
                                <span class="fa fa-picture-o d-flex d-md-none"></span>
                                <span class="d-none d-md-inline-flex">Cover</span>
                            </th>
                            <th class="col">
                                <span class="fa fa-envelope-open-text d-flex d-md-none"></span>
                                <span class="d-none d-md-inline-flex">Series</span>
                            </th>
                            <th class="col d-none d-md-flex">
                                <span class="fa fa-envelope-open-text d-flex d-md-none"></span>
                                <span class="d-none d-md-inline-flex">Message</span>
                            </th>
                            <th class="d-none col-md">
                                <span class="fa fa-calendar d-flex d-md-none"></span>
                                <span class="d-none d-md-inline-flex">Time</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            // Merge all the same series notifications by the series id
                            $seriesNotifications = $archiveNotifications->unique('data.series.id');

                            // Group all the archive notifications by the series id
                            $archives = $archiveNotifications->groupBy('data.series.id');
                        @endphp

                        @foreach ($seriesNotifications as $index => $notification)
                            @php
                                $series = $notification->data['series'];
                                $seriesArchives = $archives->get($series['id'])
                                    ->pluck('data.archive')
                                    ->sortByDesc('name')
                                    ->groupBy(function ($item, $key) {
                                        // Group the Collection by the subdirectory
                                        $finfo = new \SplFileInfo($item['name']);
                                        return $finfo->getPath() ?? 'Root';
                                    })
                                    ->toArray();
                            @endphp

                            <tr class="d-flex">
                                <th class="col-4 col-sm-2 col-lg-1">
                                    <div class="custom-image-checkbox">
                                        <input type="checkbox" id="notification-{{ $index }}" name="notification-{{ $index }}" value="{{ $series['id'] }}">
                                        <label class=" text-center text-primary my-auto" for="notification-{{ $index }}">
                                            <img class="img-fluid" src="{{ URL::action('CoverController@smallDefault', [$series['id']]) }}" alt="Cover">
                                            <span class="fa fa-check"></span>
                                        </label>
                                    </div>
                                </th>
                                <td class="col text-truncate" style="text-overflow: ellipsis;">
                                    <p>
                                        <strong>
                                            <a href="{{ URL::action('MangaController@files', [$series['id'], 'sort' => 'desc']) }}">{{ $series['name'] }}</a>
                                        </strong>
                                    </p>

                                    <div class="row d-md-none">
                                        @foreach ($seriesArchives as $directory => $directoryArchives)
                                            <ul>
                                                <li><a href="{{ URL::action('MangaController@files', [$series['id'], 'filter' => $directory]) }}">{{ $directory }}</a>
                                                    <ul>
                                                        @foreach ($directoryArchives as $archive)
                                                            <li>
                                                                {{ \App\Scanner::simplifyName(\App\Scanner::removeExtension($archive['name'])) }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            </ul>
                                        @endforeach
                                    </div>

                                    <p class="d-flex text-muted">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </td>
                                <td class="col d-none d-md-flex">
                                    <p class="d-none d-md-inline-block">
                                    @foreach ($seriesArchives as $directory => $directoryArchives)
                                        <ul>
                                            <li>
                                                <a href="{{ URL::action('MangaController@files', [$series['id'], 'filter' => $directory]) }}">{{ $directory }}</a>
                                                <ul>
                                                    @foreach ($directoryArchives as $archive)
                                                        <li>
                                                            {{ \App\Scanner::simplifyName(\App\Scanner::removeExtension($archive['name'])) }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        </ul>
                                    @endforeach
                                    </p>
                                </td>
                                <td class="d-none col-md">
                                    {{ $notification->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    Dismiss
                </div>
                <div class="col-6 col-md-2">
                    {{ Form::open(['action' => 'NotificationController@destroyArchiveNotifications', 'method' => 'delete']) }}
                    {{ Form::hidden('series', '', ['id' => 'seriesIds']) }}
                    <button class="btn btn-primary form-control" id="button-delete-selected">
                        Selected
                    </button>
                    {{ Form::close() }}
                </div>
                <div class="col-6 col-md-2">
                    {{ Form::open(['action' => 'NotificationController@destroyAllArchiveNotifications', 'method' => 'delete']) }}
                    <button class="btn btn-danger form-control">
                        All
                    </button>
                    {{ Form::close() }}
                </div>
            </div>
        @endif
    </div>
@endsection

@section ('scripts')
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function () {
            let deleteButton = document.getElementById("button-delete-selected");

            deleteButton.addEventListener("click", function () {
                // Get an array of all the selected series ids
                const selectedIds =
                    Array.from(document.querySelectorAll(".custom-image-checkbox > input:checked"))
                        .map((element) => parseInt(element.getAttribute('value')));

                let hiddenSeriesIds = document.querySelector('#seriesIds');
                hiddenSeriesIds.setAttribute('value', JSON.stringify(selectedIds));
            });
        });
    </script>
@endsection