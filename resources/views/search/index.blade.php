@extends ('layout')

@section ('title')
    Advanced Search &colon;&colon; Mangapie
@endsection

@section ('custom_navbar_right')
    @include ('shared.searchbar')
    @include ('shared.libraries')
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
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    @foreach ($genres as $genre)
                        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
                            <label class="btn btn-default btn-block"
                                   title="{{ $genre->getDescription() }}">
                                <input type="checkbox" name="genres[]" id="genres[]" value="{{ $genre->getName() }}" autocomplete="off" >&nbsp;{{ $genre->getName() }}
                            </label>
                        </div>
                    @endforeach
                </div>
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
                    {{ Form::label('Author:', null, ['for' => 'author']) }}
                    {{ Form::text('author', null, ['class' => 'form-control', 'placeholder' => '...']) }}
                </div>
            </div>
            <div class="form-group row">
                <div class="col-xs-12 col-md-3">
                    {{ Form::label('Artist:', null, ['for' => 'artist']) }}
                    {{ Form::text('artist', null, ['class' => 'form-control', 'placeholder' => '...']) }}
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
                    {{ Form::label('Keywords:', null, ['for' => 'keywords']) }}
                    {{ Form::text('keywords', null, ['class' => 'form-control', 'placeholder' => '...']) }}

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
