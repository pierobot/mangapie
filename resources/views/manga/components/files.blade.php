<div class="row">
    @if (! empty($manga->archives))
        @php
            if (! isset($sort))
                $sort = 'ascending';

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
