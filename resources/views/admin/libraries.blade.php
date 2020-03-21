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
        <li class="nav-item"><a href="{{ URL::action('RoleController@index') }}" class="nav-link">Roles</a></li>
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

                                <button class="btn btn-primary" id="action" name="action" value="refresh" title="Refresh">
                                    <span class="fa fa-refresh"></span>

                                    <span class="d-none d-lg-inline-flex">
                                        &nbsp;Refresh
                                    </span>
                                </button>

                                <button class="btn btn-danger" id="delete-{{ $library->id }}" data-id="{{ $library->id }}" name="delete-library" title="Delete">
                                    <span class="fa fa-trash"></span>

                                    <span class="d-none d-lg-inline-flex">
                                        &nbsp;Delete
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
            const progressBars = document.querySelectorAll('.progress-bar');
            let jobIds = [];

            // Iterate through the progress bars and get the latest job
            progressBars.forEach((progressBar) => {
                const done = progressBar.getAttribute('data-job-ended') === '0';
                const jobId = progressBar.getAttribute('data-job-id');
                if (done) {
                    jobIds.push(jobId);
                }
            });

            jobIds.forEach((jobId) => {
                let eventSource = new EventSource(`{{ URL::to('/job') }}/${jobId}`);
                eventSource['jobId'] = jobId;

                eventSource.onmessage = (event) => {
                    const job = JSON.parse(event.data);
                    const id = job["id"];
                    const status = job["status"];
                    const done = job["ended"];
                    const finished = job["finished"];
                    const progress = job["progress"];

                    let progressBar = Array.from(progressBars)
                        .find((element) => element.getAttribute('data-job-id') === `${id}`);

                    if (done === true) {
                        eventSource.close();
                    }

                    progressBar.setAttribute('data-job-progress', progress);
                    progressBar.setAttribute('data-job-status', status);
                    progressBar.setAttribute('data-job-ended', (status === 'finished' || status === 'failed') ? '1' : '0');
                };

                eventSource.onerror = (event) => {
                    console.log(event);
                    eventSource.close();

                    let progressBar = Array.from(progressBars)
                        .find((element) => element.getAttribute('data-job-id') === `${id}`);

                    progressBar.setAttribute('data-job-status', 'failed');
                    progressBar.setAttribute('data-job-ended', '1');
                };
            });

            const deleteButtons = document.getElementsByName('delete-library');
            deleteButtons.forEach(function (element) {

                element.addEventListener('click', function (event) {
                    event.preventDefault();

                    const confirmed = confirm('Are you sure you want to delete this library?');
                    if (confirmed) {
                        const libraryId = element.getAttribute('data-id');
                        axios.default.delete(`{{ URL::to('/library') }}/${libraryId}`)
                            .catch((error) => {
                                console.log(error);
                                alert('Unable to delete library, check the console for more information.');
                            })
                            .then((response) => {
                                location.reload();
                            });
                    }
                });
            });
        });
    </script>
@endsection