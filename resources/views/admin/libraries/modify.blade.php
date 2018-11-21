@extends ('admin.layout')

@section ('title')
    Modify Library :: Mangapie
@endsection

@section ('side-top-menu')
    <div class="list-group side-top-menu d-inline-flex d-md-flex flex-wrap flex-md-nowrap flex-row flex-md-column mb-3 mb-md-auto">
        <a class="list-group-item" href="{{ URL::action('AdminController@index') }}">
            <span class="fa fa-dashboard"></span>

            <div class="d-none d-md-inline-block">
                &nbsp;Dashboard
            </div>
        </a>

        <a class="list-group-item active" href="{{ URL::action('AdminController@libraries') }}">
            <span class="fa fa-book"></span>

            <div class="d-none d-md-inline-block">
                &nbsp;Libraries
            </div>
        </a>

        <a class="list-group-item" href="{{ URL::action('AdminController@users') }}">
            <span class="fa fa-users"></span>

            <div class="d-none d-md-inline-block">
                &nbsp;Users
            </div>
        </a>

        <a class="list-group-item" href="{{ URL::action('AdminController@logs') }}">
            <span class="fa fa-clipboard-list"></span>

            <div class="d-none d-md-inline-block">
                &nbsp;Logs
            </div>
        </a>
    </div>
@endsection

@section ('card-content')
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <a class="nav-link" href="{{ URL::action('AdminController@createLibraries') }}">Create</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active">Modify</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ URL::action('AdminController@deleteLibraries') }}">Delete</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            @php
                $libraries = \App\Library::all('id', 'name', 'path');
            @endphp

            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Name</th>
                    <th class="d-none d-md-table-cell">Path</th>
                    <th class="d-none d-md-table-cell">Manga #</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($libraries as $library)
                    <tr>
                        <td>
                            {{ Form::open(['action' => 'LibraryController@update', 'method' => 'patch']) }}
                            {{ Form::hidden('library_id', $library->id) }}

                            <div class="input-group">
                                <input class="form-control" type="text" placeholder="{{ $library->name }}" title="Enter new name" id="name" name="name">

                                <div class="input-group-append">
                                    <button class="btn btn-primary" id="action" name="action" value="rename" title="Rename">
                                        <span class="fa fa-check"></span>

                                        <span class="d-none d-lg-inline-flex">
                                            &nbsp;Rename
                                        </span>
                                    </button>

                                    <button class="btn btn-outline-primary" id="action" name="action" value="refresh" title="Refresh">
                                        <span class="fa fa-refresh"></span>

                                        <span class="d-none d-lg-inline-flex">
                                            &nbsp;Refresh
                                        </span>
                                    </button>
                                </div>
                            </div>

                            {{ Form::close() }}

                            <div class="progress">
                                @php
                                    $job = \Imtigger\LaravelJobStatus\JobStatus::whereType(\App\Jobs\ScanLibrary::class)
                                                                               ->where('input', json_encode(['library_id' => $library->id]))
                                                                               ->orderByDesc('created_at')
                                                                               ->first();
                                @endphp
                                @if (! empty($job))
                                    <div class="progress-bar job-progress-bar"
                                         id="progress-{{ $library->id }}"
                                         data-job-id="{{ $job->id }}"
                                         data-job-progress="{{ $job->progress_percentage }}"
                                         {{-- Seems like is_ended can be null? --}}
                                         data-job-ended="{{ ! empty($job->is_ended) ? $job->is_ended : 0 }}"
                                         data-job-status="{{ $job->status }}">
                                    </div>
                                @endif
                            </div>
                        </td>

                        <td class="d-none d-md-table-cell library-path-wrap">
                            {{ $library->path }}
                        </td>

                        <td class="d-none d-md-table-cell">
                            {{ $library->manga->count('id') }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
@endsection

@section ('scripts')
    <script type="text/javascript">
        $(function () {
            let progressBars = $("div.progress-bar");
            let jobIds = [];

            progressBars.each(function (index, pb) {
                if ($(pb).attr("data-job-ended") === "0") {
                    jobIds.push($(pb).attr("data-job-id"));
                }
            });

            $(jobIds).each(function (i, jobId) {
                let eventSource = new EventSource("{{ config('app.url') }}job/" + jobId);
                eventSource["jobId"] = jobId;

                eventSource.onmessage = function (event) {
                    const job = JSON.parse(event.data);
                    const id = job["id"];
                    const status = job["status"];
                    const ended = job["ended"];
                    const finished = job["finished"];
                    const progress = job["progress"];

                    let progressBar = progressBars.filter((index, pb) => $(pb).attr("data-job-id") === `${id}`);

                    if (ended === true) {
                        eventSource.close();
                    }

                    progressBar.attr("data-job-progress", progress)
                               .attr("data-job-status", status)
                               .attr("data-job-ended", (status === "finished" || status === "failed") ? 1 : 0);
                };

                eventSource.onerror = function (event) {
                    eventSource.close();

                    let progressBar = progressBars.filter((index, pb) => $(pb).attr("data-job-id") === `${eventSource["jobId"]}`);
                    progressBar.attr("data-job-status", "failed")
                               .attr("data-job-ended", 1);
                };
            });
        });
    </script>
@endsection
