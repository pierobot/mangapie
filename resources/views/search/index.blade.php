@extends ('layout')

@section ('title')
    Advanced Search &colon;&colon; Mangapie
@endsection

@section ('custom_navbar_right')
@endsection

@section ('content')
    @include ('shared.errors')

    <h3 class="text-center">
        <b>Advanced Search</b>
    </h3>

    {{ Form::open(['action' => 'SearchController@advanced']) }}
    {{ Form::hidden('type', 'advanced') }}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;Genres
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group row">
                @foreach ($genres as $genre)
                    <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="checkbox checkbox-success">
                            <input type="checkbox" name="genres[{{ $genre->getId() }}]" id="genres[{{ $genre->getId() }}]" value="{{ $genre->getName() }}" autocomplete="off" >
                            <label for="genres[{{ $genre->getId() }}]" title="{{ $genre->getDescription() }}">
                                {{ $genre->getName() }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;People
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group row">
                <div class="col-xs-12 col-md-3">
                    <label for="author">Author:</label>
                    <input class="form-control" type="text" id="author" name="author" placeholder="...">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-xs-12 col-md-3">
                    <label for="artist">Artist:</label>
                    <input class="form-control" type="text" id="artist" name="artist" placeholder="...">
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="glyphicon glyphicon-search"></span>&nbsp;&nbsp;Search
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group row">
                <div class="col-xs-12 col-md-3">
                    <label for="keywords">Keywords:</label>
                    <input class="form-control" type="text" id="keywords" name="keywords" placeholder="...">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-xs-12 col-md-3">
                    {{ Form::submit('Search', ['class' => 'btn btn-default']) }}
                </div>
            </div>
        </div>
    </div>

    {{ Form::close() }}
@endsection
