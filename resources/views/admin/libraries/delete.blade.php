@extends ('admin.layout')

@section ('title')
    Delete Library :: Mangapie
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
                    <a class="nav-link" href="{{ URL::action('AdminController@modifyLibraries') }}">Modify</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active">Delete</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            @php ($libraries = \App\Library::all('id', 'name', 'path'))

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
                            {{ Form::open(['action' => 'LibraryController@delete', 'method' => 'delete']) }}
                            {{ Form::hidden('library_id', $library->id) }}

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        {{ $library->name }}
                                    </span>
                                </div>

                                <div class="input-group-append">
                                    <button class="btn btn-danger">
                                        <span class="fa fa-times"></span>

                                        <span class="d-none d-md-inline-flex">
                                            &nbsp;Delete
                                        </span>
                                    </button>
                                </div>
                            </div>

                            {{ Form::close() }}
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