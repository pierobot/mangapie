    @extends ('layout')

@section ('content')

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">Advanced Search</h4>
        </div>
        <div class="panel-body">
            
            <h4><span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;Genres</h4>
            {{ Form::open(['action' => 'SearchController@search']) }}

                {{ Form::hidden('type', 'advanced') }}

                <div class="row">
                @foreach ($genres as $genre)
                    <div class="form-group col-xs-6 col-sm-4 col-md-3 col-lg-2">
                        {{ Form::checkbox('genres[]', $genre->name) }}
                        {{ Form::label($genre->name) }}
                    </div>            
                @endforeach
                </div>
            
            <hr>
            <div class="form-group">
                {{ Form::label('Name', null, ['for' => 'query']) }}
                {{ Form::text('Name', null, ['class' => 'form-control advanced-search-box', 'placeholder' => 'Enter name here']) }}
            </div>

            {{ Form::submit('Search', ['class' => 'btn btn-default']) }}
            
            {{ Form::close() }}

            @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>

@endsection