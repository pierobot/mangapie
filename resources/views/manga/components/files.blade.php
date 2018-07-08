<table class="table table-hover table-va-middle" style="word-break: break-all; ">
    <thead>
    <tr>
        <th class="col-xs-6 col-sm-6 col-md-7">
            <a href="{{ \URL::action('MangaController@index', [$manga, $sort == 'ascending' ? 'descending' : 'ascending']) }}">Filename&nbsp;
                @if ($sort == 'ascending')
                    <span class="glyphicon glyphicon-triangle-bottom"></span>
                @else
                    <span class="glyphicon glyphicon-triangle-top"></span>
                @endif
            </a>
        </th>
        <th class="col-xs-2 col-sm-2 col-md-1">Status</th>
        <th class="col-xs-4 col-sm-2 col-md-2">Last Read</th>
        <th class="col-sm-2 col-md-1 hidden-xs">Size</th>
    </tr>
    </thead>

    <tbody>
    @if (empty($manga->archives) === false)
        @foreach ($manga->archives as $archive)
            @php
                $isFavorited = $user->favorites->where('manga_id', $manga->id)->first() !== null;
                $isWatching = $user->watchReferences->where('manga_id', $manga->id)->first() !== null;
                $watchNotifications = $user->watchNotifications->where('manga_id', $manga->id);
                $readerHistory = $user->readerHistory->where('manga_id', $manga->id);

                $archiveHistory = $readerHistory->where('archive_name', $archive->getName())->first();
            @endphp

            <tr>
                <td class="col-sm-6 col-md-7">
                    <a href="{{ URL::action('ReaderController@index', [$manga, $archive->getId(), $archiveHistory != null ? $archiveHistory->getPage() : 1]) }}">
                        <div>
                            {{ $archive->getName() }}
                        </div>
                    </a>
                </td>
                <td class="col-sm-2 col-md-1">
                    <div class="row">
                        <div class="col-xs-12 span-label-ib">
                            @if ($archiveHistory !== null)
                                @if ($archiveHistory->getPage() < $archiveHistory->getPageCount())
                                    <span class="label label-warning" title="pg. {{ $archiveHistory->getPage() }} of {{ $archiveHistory->getPageCount() }}">Incomplete</span>
                                @else
                                    <span class="label label-success" title="pg. {{ $archiveHistory->getPage() }} of {{ $archiveHistory->getPageCount() }}">Complete</span>
                                @endif
                            @else
                                <span class="label label-default">Unread</span>
                            @endif
                        </div>
                        <div class="col-xs-12 span-label-ib">
                            @if ($watchNotifications->where('archive_id', $archive->getId())->first() != null)
                                <span class="label label-success">&nbsp;New!&nbsp;</span>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="col-sm-1 col-md-2">
                    {{ $archiveHistory !== null ? $archiveHistory->getLastUpdated()->diffForHumans() : "Never" }}
                </td>
                <td class="col-sm-1 col-md-1 visible-md visible-lg">
                    {{ $archive->getSize() }}
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
