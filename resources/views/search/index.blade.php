@extends ('layout')

@section ('title')
    Advanced Search &colon;&colon; Mangapie
@endsection

@section ('content')
    @include ('shared.errors')

    <h5 class="d-flex justify-content-center">
        <strong>Advanced Search</strong>
    </h5>

    <div class="card">
        <div class="card-body">
            {{ Form::open(['action' => 'SearchController@advanced']) }}
            {{ Form::hidden('type', 'advanced') }}

            <div class="row">
                <div class="col-12">
                    <h5>Genres</h5>
                </div>

                @php ($genres = \App\Genre::all())
                @foreach ($genres as $genre)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="{{ $genre->id }}" name="genres[]" value="{{ $genre->id }}">
                            <label class="custom-control-label" for="{{ $genre->id }}">{{ $genre->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <h5>People</h5>
                </div>

                <div class="col-12">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        Author
                                    </span>
                                </div>
                                <input type="text" class="form-control" title="Name of author (Surname first)" name="author">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 col-lg-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        Artist
                                    </span>
                                </div>
                                <input type="text" class="form-control" title="Name of artist (Surname first)" name="artist">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <h5>Search</h5>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                Keywords
                            </span>
                        </div>
                        <input type="text" class="form-control" title="The keywords to search for" name="keywords">
                    </div>
                </div>

                <div class="col-12">
                    <div class="row mt-3">
                        <div class="col-12 col-lg-6">
                            <button class="btn btn-primary form-control">
                                <span class="fa fa-search"></span>

                                &nbsp;Search
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{ Form::close() }}
@endsection
