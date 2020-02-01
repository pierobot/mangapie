@extends ('admin.layout')

@section ('title')
    Admin &middot; Libraries
@endsection

@section ('top-menu')
    <ul class="nav nav-pills mb-3 justify-content-center">
        <li class="nav-item"><a href="{{ URL::action('AdminController@statistics') }}" class="nav-link">Statistics</a></li>
        <li class="nav-item"><a href="{{ URL::action('AdminController@config') }}" class="nav-link">Config</a></li>
        <li class="nav-item"><a href="#" class="nav-link active">Libraries</a></li>
        <li class="nav-item"><a href="{{ URL::action('AdminController@users') }}" class="nav-link">Users</a></li>
        <li class="nav-item"><a href="{{ URL::action('AdminController@roles') }}" class="nav-link">Roles</a></li>
    </ul>
@endsection

@section ('card-content')
    <hr>
    <h4><strong>Create</strong></h4>

    {{ Form::open(['action' => 'LibraryController@create']) }}
    <div class="form-group">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Name" title="Name" id="create-name" name="name">
            <input type="text" class="form-control" placeholder="Path" title="Path" id="create-path" name="path">

            <div class="input-group-append">
                <button type="submit" class="form-control btn btn-primary">
                    <span class="fa fa-plus"></span>

                    <span class="d-none d-lg-inline-flex">
                        &nbsp;Create
                    </span>
                </button>
            </div>
        </div>
    </div>
    {{ Form::close() }}

    <hr>
    <h4><strong>Existing</strong></h4>
    @php
        $libraries = \App\Library::all('id', 'name', 'path');
    @endphp

    <table class="table">
        <thead class="bg-dark">
        <tr>
            <th>Name</th>
            <th class="d-none d-md-table-cell">Path</th>
            <th class="d-none d-md-table-cell">Count</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($libraries as $library)
                <tr>
                    <td>
                        {{ Form::open(['action' => ['LibraryController@update', $library], 'method' => 'patch']) }}

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
                let eventSource = new EventSource("{{ URL::to('/job') }}/" + jobId);
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