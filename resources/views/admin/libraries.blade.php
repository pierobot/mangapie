@extends ('layout')

@section ('title')
    Admin &middot; Libraries
@endsection

@section ('custom_navbar_right')
@endsection

@section ('content')
    <h2 class="text-center"><b>Libraries</b></h2>

    @include ('shared.success')
    @include ('shared.warnings')
    @include ('shared.errors')

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="glyphicon glyphicon-plus"></span>&nbsp;Create
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                {{ Form::open(['action' => 'LibraryController@create']) }}

                    <div class="col-xs-12">
                        <div class="row">
                            <div class="form-group col-xs-12 col-md-4">

                                {{ Form::label('name:', null, ['for' => 'name']) }}
                                {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter name here...']) }}

                                {{ Form::label('path:', null, ['for' => 'path']) }}
                                {{ Form::text('path', null, ['class' => 'form-control', 'placeholder' => 'Enter path here'])}}

                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            {{ Form::submit('Create', ['class' => 'btn btn-success']) }}
                        </div>
                    </div>

                {{ Form::close() }}

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="glyphicon glyphicon-pencil"></span>&nbsp;Edit
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12">
                    <table class="table table-condensed table-hover table-va-middle" id="libraries-table">
                        <thead>
                        <tr>
                            <th class="col-xs-9 col-sm-3">Name</th>
                            <th class="visible-sm visible-md visible-lg">Path</th>
                            <th>Count</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($libraries as $library)
                            <tr>
                                <td class="col-xs-9 col-sm-3">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="checkbox checkbox-success">
                                                <input type="checkbox" id="ids[{{ $library->getId() }}]" name="ids[{{ $library->getId() }}]" value="{{ $library->getId() }}">
                                                <label for="ids[{{ $library->getId() }}]" title="{{ $library->getPath() }}">
                                                    <div class="truncate-ellipsis">
                                                        <span>{{ $library->name }}</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="visible-sm visible-md visible-lg">{{ $library->path }}</td>
                                <td>{{ \App\Manga::where('library_id', '=', $library->id)->count() }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="alert alert-warning">
                        <span class="glyphicon glyphicon-warning-sign"></span>&nbsp; Deleted libraries will <b>NOT</b> be deleted from the filesystem.
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <div class="panel-heading">
                <div class="panel-title">
                    <div class="row">
                        <div class="col-xs-12 text-center">
                            <button type="submit" class="btn btn-success" id="update-selected-btn">Update selected</button>
                            <button type="submit" class="btn btn-danger" id="remove-selected-btn">Remove selected</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Progress</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-condensed" id="progress-table">
                        <thead>
                            <tr>
                                <th class="col-xs-2">Name</th>
                                <th class="hidden-xs hidden-sm hidden-md hidden-lg">JobId</th>
                                <th class="col-xs-4">Status</th>
                                <th class="col-xs-6">Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-xs-offset-6 col-xs-6">
                            <button class="btn btn-default" data-toggle="modal" data-target="#modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section ('scripts')
    <script type="text/javascript">
        function insertJobRow(tableBody, job) {
            const name = job['name'];
            const jobId = job['id'];
            const status = 'queued';

            tableBody.append($('<tr>')
                         .append($('<td>').addClass('col-xs-2').text(name))
                         .append($('<td>').addClass('hidden-xs hidden-sm hidden-md hidden-lg').attr('id', 'job-' + jobId + '-name').text(jobId))
                         .append($('<td>').addClass('col-xs-4').attr('id', 'job-' + jobId + '-status').text(status))
                         .append($('<td>').addClass('col-xs-6')
                             .append($('<div>').addClass('progress')
                                 .append($('<div>').attr('id', 'job-' + jobId + '-progress').addClass('progress-bar progress-bar-striped'))
                             )
                         )
                     );
        }

        function updateJobRow(tableBody, job) {
            const jobId = job['id'];
            const status = job['status'];
            const progress = job['progress'];

            let statusText = $(tableBody.find('tr > td[id=job-' + jobId + '-status]'));
            let progressBar = $(tableBody.find('tr > td > div div[id=job-' + jobId + '-progress]'));

            statusText.text(status);
            progressBar.css('width', progress + '%');

            if (status === 'finished')
                progressBar.addClass('progress-bar-success');

            if (status === 'failed')
                progressBar.addClass('progress-bar-error');
        }

        function jobsAreDone(tableBody) {
            const statuses = tableBody.find('tr > td:nth(2)');
            for (let i = 0; i< statuses.length; i++) {
                let status = $(statuses[i]);

                if (status.text() === 'queued' || status.text() === 'executing')
                    return false;
            }

            return true;
        }

        $(function () {

            let updateBtn = $('#update-selected-btn');
            let removeBtn = $('#remove-selected-btn');

            let progressTableBody = $('#progress-table tbody');

            updateBtn.click(function () {
                let selectedCheckboxes = $('#libraries-table > tbody > tr > td input[type=checkbox]:checked');
                let libraryIds = [];
                selectedCheckboxes.each(function (index, checkbox) {
                    libraryIds.push(checkbox.value);
                });

                if (libraryIds.length === 0) {
                    alert('You must first select one or more libraries.');

                    return;
                }

                $(progressTableBody).find('tr').remove();

                axios.post('{{ \URL::action('LibraryController@update') }}', { ids: libraryIds })
                     .then(function (response) {

                         $('#modal').show();

                         if (! jobsAreDone(progressTableBody))
                             return;

                         let jobIds = [];
                         let jobs = response.data['jobs'];

                         jobs.forEach(function (job) {
                            insertJobRow(progressTableBody, job);

                             jobIds.push(job['id']);
                         });

                         const timerId = setInterval(function () {
                             axios.post('{{ \URL::action('LibraryController@status') }}', { ids: jobIds})
                                  .then(function (response) {
                                      jobs = response.data['jobs'];

                                      jobs.forEach(function (job) {
                                          updateJobRow(progressTableBody, job);
                                      });

                                      if (jobsAreDone(progressTableBody))
                                          clearInterval(timerId);
                                  })
                                  .catch(function (error) {
                                      clearInterval(timerId);

                                      console.log(error);

                                      alert('An error was encountered. Please check the console for more information.');
                                  });
                         }, 500);
                     })
                     .catch(function (error) {
                         console.log(error);

                         alert('An error was encountered. Please check the console for more information.');
                     });
            });

            removeBtn.click(function () {
                let selectedCheckboxes = $('#libraries-table > tbody > tr > td input[type=checkbox]:checked');
                let libraryIds = [];
                selectedCheckboxes.each(function (index, checkbox) {
                    libraryIds.push(checkbox.value);
                });

                if (libraryIds.length === 0) {
                    alert('You must first select one or more libraries.');

                    return;
                }

                axios.post('{{ \URL::action('LibraryController@delete') }}', { ids: libraryIds})
                     .then(function (response) {
                         if (response.status !== 200) {
                             console.log(response);

                             alert('Unexpected status code. Please check the console for more information');
                             return;
                         }

                         window.location = '{{ \URL::action('AdminController@libraries') }}';
                     })
                     .catch(function (error) {
                         console.log(error);

                         alert('An error was encountered. Please check the console for more information.');
                     });
            });

        });
    </script>
@endsection
