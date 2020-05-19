@extends ('layout')

@section ('title')
    {{ $user->name }}&apos;s History :: Mangapie
@endsection

@section ('content')
    <div class="container mt-3">
        <div class="row">
            <div class="col">
                <h3><strong>{{ $user->name }}'s History</strong></h3>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <table class="table table-borderless table-striped" style="table-layout: fixed;">
                    <thead>
                        <tr class="d-flex">
                            <th class="col-4 col-md-2">
                                <span class="fa fa-image d-flex d-md-none"></span>&nbsp;
                                <span class="d-none d-md-inline-flex">Cover</span>
                            </th>
                            <th class="col">
                                <span class="fa fa-book d-flex d-md-none"></span>&nbsp;
                                <span class="d-none d-md-inline-flex">Series</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            @php
                                $finfo = new \SplFileInfo($item->archive->name);
                                $basePath = $finfo->getPath();

                                $nextArchive = $item->archive->getNextArchive();
                                if ($item->page === $item->page_count){
                                    if (! empty($nextArchive)) {
                                        $continueUrl = URL::action('ReaderController@index', [$item->manga, $nextArchive, 1]);
                                    } else {
                                        $continueUrl = null;
                                    }
                                } else {
                                    $continueUrl = URL::action('ReaderController@index', [$item->manga, $item->archive, $item->page]);
                                }
                            @endphp
                            <tr class="d-flex">
                                <th class="col-4 col-md-2">
                                    <img class="img-fluid" src="{{ URL::action('CoverController@smallDefault', [$item->manga]) }}" alt="Cover for {{ $item->manga->name }}">
                                </th>
                                <td class="col text-wrap text-break">
                                    <h5>
                                        <strong>
                                            <a href="{{ URL::action('MangaController@show', [$item->manga]) }}">{{ $item->manga->name }}</a>
                                        </strong>
                                    </h5>

                                    @if (! empty($basePath))
                                        <p class="text-muted">
                                            {{ $basePath }}
                                        </p>
                                    @endif

                                    <p>
                                        {{ \App\Scanner::removeExtension(\App\Scanner::simplifyName($item->archive->name)) }}
                                    </p>

                                    <p>
                                        {{ $item->updated_at->diffForHumans() }}
                                    </p>

                                    @if (empty($continueUrl))
                                        <div class="d-flex d-md-none">
                                            <button class="btn btn-secondary disabled w-100" disabled>
                                                Next archive not found
                                            </button>
                                        </div>
                                        <div class="d-none d-md-flex">
                                            <button class="btn btn-secondary disabled" disabled>
                                                Next archive not found
                                            </button>
                                        </div>
                                    @else
                                        <div class="d-flex d-md-none">
                                            <a class="btn btn-primary w-100" href="{{ $continueUrl }}">
                                                Continue
                                            </a>
                                        </div>
                                        <div class="d-none d-md-flex">
                                            <a class="btn btn-primary" href="{{ $continueUrl }}">
                                                Continue
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
