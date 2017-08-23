@extends ('layout')

@section ('title')
:: Libraries
@endsection

@section ('content')

    <div class="panel panel-default">
                
        <div class="panel-heading">
            <h2 class="panel-title">Libraries</h2>
        </div>

        <div class="panel-body">

            {{ Form::open(['action' => 'LibraryController@update']) }}

            <table class="table table-responsive table-hover ">
                <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Id</th>
                    <th class="visible-md visible-lg">Path</th>
                    <th>Count</th>
                </tr>
                </thead>

                <tbody>
                @foreach ($libraries as $library)
                    <tr>
                        <th>{{ Form::checkbox('ids[]', $library->id) }}</th>
                        <th>{{ $library->name }}</th>
                        <th>{{ $library->id }}</th>
                        <th class="visible-md visible-lg">{{ $library->path }}</th>
                        <th>{{ \App\Manga::where('library_id', '=', $library->id)->count() }}</th>
                    </tr>
                @endforeach
                </tbody>

            </table>

            {{ Form::submit('Update', ['class' => 'btn btn-success', 'id' => 'action', 'name' => 'action', 'value' => 'update']) }}
            {{ Form::submit('Delete', ['class' => 'btn btn-danger', 'id' => 'action', 'name' => 'action', 'value' => 'delete']) }}
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ Form::close() }}
        </div>
    </div>

@endsection