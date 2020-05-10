@extends ('layout')

@section ('title')
    Advanced Search &colon;&colon; Mangapie
@endsection

@section ('content')
    <div class="container mt-3">
        @include ('shared.errors')

        <h5 class="d-flex justify-content-center mb-3">
            <strong>Advanced Search</strong>
        </h5>


        {{ Form::open(['action' => 'SearchController@postAdvanced']) }}

        <div class="row mb-3">
            <div class="col-12">
                <h5>Libraries</h5>
            </div>

            @php
                $user = auth()->user();
                $libraries = $user->libraries();
            @endphp
            @foreach ($libraries as $library)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" id="library-{{ $library->id }}" name="libraries[]" value="{{ $library->id }}">
                        <label class="custom-control-label" for="library-{{ $library->id }}">{{ $library->name }}</label>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <h5>Genres</h5>
            </div>

            @php ($genres = \App\Genre::all())
            @foreach ($genres as $genre)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" id="genre-{{ $genre->id }}" name="genres[]" value="{{ $genre->id }}">
                        <label class="custom-control-label" for="genre-{{ $genre->id }}">{{ $genre->name }}</label>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row mb-3">
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

        <div class="row mb-3">
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

        {{ Form::close() }}
    </div>
@endsection
