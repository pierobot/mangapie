<ul class="nav nav-pills nav-fill">
    @foreach ($topMostDirectories as $directory)
        <li class="nav-item">
            <a href="{{ URL::action('MangaController@files', [$manga, 'filter' => $directory]) }}"
               class="nav-link @if ($filter === $directory) active @endif"
            >
                {{ $directory }}
            </a>
        </li>
    @endforeach
</ul>

<table class="table table-borderless table-striped mt-3">
    <thead>
        <tr class="d-flex">
            <th class="col-5">
                {{ Form::open(['action' => ['ReaderHistoryController@markAllArchivesRead', $manga, 'filter' => $filter], 'class' => 'd-inline-flex p-0 m-0']) }}
                <button class="btn m-0 p-0 pr-1" type="submit" title="Mark all as read">
                    <span class="fa fa-check text-success"></span>
                </button>
                {{ Form::close() }}
                <span class="fa fa-book d-inline-flex d-md-none">
                    &nbsp;
                    @if ($sort === 'asc' || empty($sort))
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'desc']) }}"><span class="fa fa-sort-alpha-up"></span></a>
                    @else
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'asc']) }}"><span class="fa fa-sort-alpha-down"></span></a>
                    @endif
                </span>
                <span class="d-none d-md-inline-flex">
                    Name&nbsp;
                    @if ($sort === 'asc' || empty($sort))
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'desc']) }}"><span class="fa fa-sort-alpha-up"></span></a>
                    @else
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'asc']) }}"><span class="fa fa-sort-alpha-down"></span></a>
                    @endif
                </span>
            </th>
            <th class="col-1">
            </th>
            <th class="col">
                <span class="fa fa-history d-flex d-md-none"></span>
                <span class="d-none d-md-flex">Last Read</span>
            </th>
            <th class="col">
                <span class="fa fa-clock d-flex d-md-none"></span>
                <span class="d-none d-md-flex">Date Added</span>
            </th>
        </tr>
    </thead>
    <tbody>
    @foreach ($items as $item)
        @php
            $readerHistory = $user->readerHistory->where('manga_id', $manga->id);
            $archiveHistory = $readerHistory->where('archive_id', $item->id)->first();
            $hasRead = ! empty($archiveHistory);
            $hasCompleted = $hasRead ? $archiveHistory->page === $archiveHistory->page_count : false;

            $colorType = $hasRead ? ($hasCompleted ? "success" : "warning") : false;
            $status = $hasRead ? ($hasCompleted ? "Complete" : "Incomplete") : "Unread";

            $resumeUrl = URL::action('ReaderController@index', [$manga, $item, ! empty($archiveHistory) ? $archiveHistory->page : 1]);
        @endphp

        <tr class="d-flex">
            {{-- Use text-left as this column is centered on Safari --}}
            <th class="col-5 text-left">
                @php
                    if ($status === 'Incomplete' || $status === 'Unread') {
                        $actionParams = ['ReaderHistoryController@markArchiveRead', $item];
                        $title = 'Mark as read';
                    } else {
                        $actionParams = ['ReaderHistoryController@markArchiveUnread', $item];
                        $title = 'Mark as unread';
                    }
                @endphp
                {{ Form::open(['action' => $actionParams, 'class' => 'd-inline-flex m-0 p-0']) }}
                <button class="btn m-0 p-0 pr-1 toggle-archive-read"
                        data-status="{{ $status }}"
                        type="submit"
                        title="{{ $title }}"
                        id="archive-{{ $item->id }}"
                >
                    <span class="fa fa-check-circle"></span>
                </button>
                {{ Form::close() }}

                <strong class="text-wrap text-break">
                    <a href="{{ URL::action('ReaderController@index', [$manga, $item, 1]) }}">
                        {{ \App\Scanner::removeExtension(\App\Scanner::simplifyName($item->name)) }}
                    </a>
                </strong>
            </th>
            <td class="col-1">
                <a href="{{ URL::action('PreviewController@index', [$manga, $item]) }}">
                    <strong>
                        <span class="fa fa-search"></span>
                    </strong>
                </a>
            </td>
            <td class="col">
                @if (! empty($archiveHistory))
                    <div class="d-flex d-sm-none">
                        <small class="text-{{ $colorType }}">{{ $archiveHistory->updated_at->diffForHumans(null, \Carbon\CarbonInterface::DIFF_ABSOLUTE) }}</small>
                    </div>
                    <div class="d-none d-sm-flex">
                        <small class="text-{{ $colorType }}">{{ $archiveHistory->updated_at->diffForHumans() }}</small>
                    </div>
                @endif
            </td>
            <td class="col">
                <div class="d-flex d-sm-none">
                    <small>{{ $item->created_at->diffForHumans(null, \Carbon\CarbonInterface::DIFF_ABSOLUTE) }}</small>
                </div>
                <div class="d-none d-sm-flex">
                    <small>{{ $item->created_at->diffForHumans() }}</small>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

