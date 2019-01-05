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
                $archiveHistory = $readerHistory->where('archive_id', $archive->id)->first();
                $hasRead = ! empty($archiveHistory);
                $hasCompleted = $hasRead ? $archiveHistory->page === $archiveHistory->page_count : false;

                $colorType = $hasRead ? ($hasCompleted ? "success" : "warning") : false;
                $status = $hasRead ? ($hasCompleted ? "Complete" : "Incomplete") : "Unread";

                $resumeUrl = URL::action('ReaderController@index', [$manga, $archive, ! empty($archiveHistory) ? $archiveHistory->page : 1]);
            @endphp

            {{-- TODO: Add something to indicate an archive is new --}}

            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                <div class="card mb-3 @if (! empty($colorType)) border-{{ $colorType }} @else @endif">
                    <a href="{{ $resumeUrl }}">
                        <img class="card-img-top" src="{{ URL::action('CoverController@small', [$manga, $archive, 1]) }}">
                    </a>

                    @php
                        $volCh = App\Scanner::getVolumesAndChapters($archive->name);
                        // If there is no volume or chapter in the name, or if the parsing failed
                        // then just use the archive name :shrug:
                        if (empty($volCh) || empty($volCh[0]))
                            $nameVolCh = $archive->name;
                        else
                            $nameVolCh = $volCh[0][0];
                    @endphp

                    <div class="card-body card-overlay-bottom">
                        <a href="{{ $resumeUrl }}">
                            <h5 class="text-center text-truncate">
                                <strong title="{{ $archive->name }}"
                                        @if (! empty($archiveHistory))
                                            @if ($archiveHistory->page == $archiveHistory->page_count)
                                                class="text-success"
                                            @else
                                                class="text-warning"
                                            @endif
                                        @else
                                            class="text-primary"
                                        @endif
                                >
                                    {{ $nameVolCh }}
                                </strong>
                            </h5>
                        </a>
                    </div>

                    @if (! empty($archiveHistory))
                        <div class="progress" style="height: 0.5em;" title="Page {{ $archiveHistory->page }}">
                            <div class="progress-bar @if ($hasCompleted) bg-success @else bg-warning @endif"
                                 @if (! $hasCompleted)
                                    style="width: {{ ($archiveHistory->page / $archiveHistory->page_count) * 100 }}%;"
                                 @else
                                    style="width: 100%;"
                                 @endif
                            >
                            </div>
                        </div>
                    @endif

                    <div class="card-footer">
                        <small><strong>Added: </strong>{{ $archive->created_at->diffForHumans() }}</small><br>
                        @if (! empty($archiveHistory))
                            <small><strong>Read: </strong>{{ $archiveHistory->updated_at->diffForHumans() }}</small>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
