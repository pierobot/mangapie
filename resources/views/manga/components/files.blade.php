<div class="row mt-2">
    @if (! empty($manga->archives))
        @php
            $archives = $sort === 'descending' ? $manga->archives->sortByDesc('name') : $manga->archives;
        @endphp
        @foreach ($archives as $archive)
            @php
                $readerHistory = $user->readerHistory->where('manga_id', $manga->id);
                $archiveHistory = $readerHistory->where('archive_name', $archive->name)->first();
                $hasRead = ! empty($archiveHistory);
                $hasCompleted = $hasRead ? $archiveHistory->page === $archiveHistory->page_count : false;

                $colorType = $hasRead ? ($hasCompleted ? "success" : "warning") : false;
                $status = $hasRead ? ($hasCompleted ? "Complete" : "Incomplete") : "Unread";

                $resumeUrl = URL::action('ReaderController@index', [$manga, $archive, ! empty($archiveHistory) ? $archiveHistory->page : 1]);
            @endphp

            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <div class="card mt-1 mb-1 @if (! empty($colorType)) border-{{ $colorType }} @else @endif">
                    <a href="{{ $resumeUrl }}">
                        @php
                            $isNew = $readerHistory->where('archive_id', $archive->getId())->first() != null;
                        @endphp

                        @if ($isNew)
                            {{-- Add something to indicate an archive is new --}}
                        @endif
                        <img class="card-img-top" src="{{ URL::action('CoverController@small', [$manga, $archive, 1]) }}">
                    </a>
                    <div class="card-body text-center pt-2 pb-0">
                        <div class="card-title" title="{{ $manga->name }}">
                            <a class="card-link" title="{{ $archive->name }}" href="{{ $resumeUrl }}">{{ $archive->name }}</a>
                        </div>
                    </div>
                    <div class="card-footer @if ($hasCompleted) bg-success @elseif ($hasRead) bg-warning @else @endif">
                        <div class="row">
                            <div class="col-12">
                                @if ($hasRead && $hasCompleted)
                                    <span class="fa fa-book"></span>
                                @else
                                    <span class="fa @if ($hasRead) fa-book-open @else fa-book @endif"></span>
                                @endif

                                <small class="@if ($hasCompleted || $hasRead) text-dark @else text-muted @endif">{{ $archive->getSize() }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
{{--<table class="table table-hover table-va-middle" style="word-break: break-all; ">--}}
    {{--<thead>--}}
    {{--<tr>--}}
        {{--<th class="col-6 col-sm-6 col-md-7">--}}
            {{--<a href="{{ \URL::action('MangaController@index', [$manga, $sort == 'ascending' ? 'descending' : 'ascending']) }}">Filename&nbsp;--}}
                {{--@if ($sort == 'ascending')--}}
                    {{--<span class="fa fa-sort-desc"></span>--}}
                {{--@else--}}
                    {{--<span class="fa fa-sort-asc"></span>--}}
                {{--@endif--}}
            {{--</a>--}}
        {{--</th>--}}
        {{--<th class="col-2 col-sm-2 col-md-1">Status</th>--}}
        {{--<th class="col-4 col-sm-2 col-md-2">Last Read</th>--}}
        {{--<th class="col-sm-2 col-md-1 d-none d-sm-block">Size</th>--}}
    {{--</tr>--}}
    {{--</thead>--}}

    {{--<tbody>--}}
    {{--@if (empty($manga->archives) === false)--}}
        {{--@foreach ($manga->archives as $archive)--}}
            {{--@php--}}
                {{--$isFavorited = $user->favorites->where('manga_id', $manga->id)->first() !== null;--}}
                {{--$isWatching = $user->watchReferences->where('manga_id', $manga->id)->first() !== null;--}}
                {{--$watchNotifications = $user->watchNotifications->where('manga_id', $manga->id);--}}
                {{--$readerHistory = $user->readerHistory->where('manga_id', $manga->id);--}}

                {{--$archiveHistory = $readerHistory->where('archive_name', $archive->getName())->first();--}}
            {{--@endphp--}}

            {{--<tr>--}}
                {{--<td class="col-6 col-md-7">--}}
                    {{--<a href="{{ URL::action('ReaderController@index', [$manga, $archive->getId(), $archiveHistory != null ? $archiveHistory->getPage() : 1]) }}">--}}
                        {{--<div>--}}
                            {{--{{ $archive->getName() }}--}}
                        {{--</div>--}}
                    {{--</a>--}}
                {{--</td>--}}
                {{--<td class="col-2 col-md-1">--}}
                    {{--<div class="row">--}}
                        {{--<div class="col span-label-ib">--}}
                            {{--@if ($archiveHistory !== null)--}}
                                {{--@if ($archiveHistory->getPage() < $archiveHistory->getPageCount())--}}
                                    {{--<span class="badge badge-warning" title="pg. {{ $archiveHistory->getPage() }} of {{ $archiveHistory->getPageCount() }}">Incomplete</span>--}}
                                {{--@else--}}
                                    {{--<span class="badge label-success" title="pg. {{ $archiveHistory->getPage() }} of {{ $archiveHistory->getPageCount() }}">Complete</span>--}}
                                {{--@endif--}}
                            {{--@else--}}
                                {{--<span class="badge badge-secondary">Unread</span>--}}
                            {{--@endif--}}
                        {{--</div>--}}
                        {{--<div class="col span-label-ib">--}}
                            {{--@if ($watchNotifications->where('archive_id', $archive->getId())->first() != null)--}}
                                {{--<span class="badge badge-success">&nbsp;New!&nbsp;</span>--}}
                            {{--@endif--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</td>--}}
                {{--<td class="col-1 col-md-2">--}}
                    {{--{{ $archiveHistory !== null ? $archiveHistory->getLastUpdated()->diffForHumans() : "Never" }}--}}
                {{--</td>--}}
                {{--<td class="col-1 col-md-1 d-none d-md-block">--}}
                    {{--{{ $archive->getSize() }}--}}
                {{--</td>--}}
            {{--</tr>--}}
        {{--@endforeach--}}
    {{--@endif--}}
    {{--</tbody>--}}
{{--</table>--}}
