@if ($sortByTopMostDirectories)
    <ul class="nav nav-pills nav-fill">
        @foreach ($topMostDirectories as $directory)
            <li class="nav-item">
                <a href="{{ URL::action('MangaController@files', [$manga]) . '?' . \Illuminate\Support\Arr::query(['filter' => $directory]) }}"
                   class="nav-link @if ($filter === $directory) active @endif"
                >
                    {{ $directory }}
                </a>
            </li>
        @endforeach
    </ul>
@endif

<div class="row justify-content-center">
    <div class="col-12">
        <table class="table table-borderless table-striped">
            <thead>
            <tr>
                <th scope="col">
                    <span class="fa fa-book d-flex d-md-none"></span>
                    <span class="d-none d-md-flex">
                        Name&nbsp;
                        @if ($sort === 'asc' || empty($sort))
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'desc']) }}"><span class="fa fa-sort-alpha-up"></span></a>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'asc']) }}"><span class="fa fa-sort-alpha-down"></span></a>
                        @endif
                    </span>
                </th>
                <th scope="col">
                </th>
                <th scope="col" class="pr-0">
                    <span class="fa fa-history d-flex d-md-none"></span>
                    <span class="d-none d-md-flex">Last Read</span>
                </th>
                <th scope="col" class="pr-0">
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

                <tr class="d-table-row">
                    <td class="col-5 col-md-6">
                        <strong class="text-wrap text-break">
                            <a href="{{ URL::action('ReaderController@index', [$manga, $item, 1]) }}">
                                {{ \App\Scanner::simplifyName($item->name) }}
                            </a>
                        </strong>
                    </td>
                    <td class="col-1">
                        <a href="{{ URL::action('PreviewController@index', [$manga, $item]) }}">
                            <strong>
                                <span class="fa fa-search"></span>
                            </strong>
                        </a>
                    </td>
                    <td class="col-3 pr-0">
                        @if (! empty($archiveHistory))
                            <div class="d-flex d-sm-none">
                                <small class="text-{{ $colorType }}">{{ $archiveHistory->updated_at->diffForHumans(null, \Carbon\CarbonInterface::DIFF_ABSOLUTE) }}</small>
                            </div>
                            <div class="d-none d-sm-flex">
                                <small class="text-{{ $colorType }}">{{ $archiveHistory->updated_at->diffForHumans() }}</small>
                            </div>
                        @endif
                    </td>
                    <td class="col-3 pr-0">
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
    </div>
</div>

