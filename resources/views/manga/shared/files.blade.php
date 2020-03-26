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
                <div class="card mb-3">
                    <a href="{{ $resumeUrl }}">
                        <img class="card-img-top" src="{{ URL::action('CoverController@small', [$manga, $archive, 1]) }}">
                    </a>

                    @php
                        $volCh = App\Scanner::getVolumesAndChapters($archive->name);
                        // If there is no volume or chapter in the name, or if the parsing failed
                        // then just use the archive name :shrug:
                        if (empty($volCh)) {
                            $nameVolCh = $archive->name;
                        } else {
                            $nameVolCh = '';
                            foreach ($volCh as $part) {
                                $nameVolCh .= $part . ' ';
                            }
                        }
                    @endphp

                    <div class="card-footer bg-dark">

                            <a href="{{ $resumeUrl }}">
                                <h5 class="text-truncate">
                                    <strong title="{{ $nameVolCh }}">
                                        {{ $nameVolCh }}
                                    </strong>
                                </h5>
                            </a>

                        <small><strong>Added: </strong>{{ $archive->created_at->diffForHumans() }}</small><br>
                        @if (! empty($archiveHistory))
                            <small><strong>Read: </strong>{{ $archiveHistory->updated_at->diffForHumans() }}</small><br>
                        @endif

                        <small><a href="{{ URL::action('PreviewController@index', [$manga, $archive]) }}">Preview</a></small>

                        @if (! empty($archiveHistory))
                            <div class="progress mt-1" style="height: 0.5em;" title="Page {{ $archiveHistory->page }}">
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
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
