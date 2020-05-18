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
                <table class="table table-borderless table-striped">
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
                                <td class="col">
                                    <h5>
                                        <strong class="text-wrap text-break">
                                            <a href="{{ URL::action('MangaController@show', [$item->manga]) }}">{{ $item->manga->name }}</a>
                                        </strong>
                                    </h5>

                                    <p class="text-wrap text-break">
                                        @if (! empty($basePath))
                                            <small class="d-flex d-md-none text-muted">{{ $basePath }} -</small>
                                            <span class="d-none d-md-inline-flex text-muted">{{ $basePath }} -</span>
                                        @endif

                                        <small class="d-flex d-md-none">{{ \App\Scanner::removeExtension(\App\Scanner::simplifyName($item->archive->name)) }}</small>
                                        <span class="d-none d-md-inline-flex">{{ \App\Scanner::removeExtension(\App\Scanner::simplifyName($item->archive->name)) }}</span>
                                    </p>
                                    <p class="text-wrap text-break">
                                        <small class="d-flex d-md-none">{{ $item->updated_at->diffForHumans() }}</small>
                                        <span class="d-none d-md-inline-flex">{{ $item->updated_at->diffForHumans() }}</span>
                                    </p>

                                    @if (empty($continueUrl))
                                        <button class="btn btn-warning disabled" disabled>
                                            Next archive not found
                                        </button>
                                    @else
                                        <a class="btn btn-primary"
                                           href="{{ URL::action('ReaderController@index', [$item->manga, $item->archive, $item->page]) }}"
                                        >
                                            Continue
                                        </a>
                                    @endif
                                </td>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
