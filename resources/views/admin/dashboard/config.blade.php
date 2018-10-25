@extends ('admin.dashboard.layout')

@section ('title')
    Admin &middot; Config
@endsection

@section ('card-content')
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <a class="nav-link active" href="#">Config</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ URL::action('AdminController@statistics') }}">Statistics</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <h4><strong>Registration</strong></h4>

                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <h5>General</h5>

                            {{ Form::open(['action' => 'AdminController@patchRegistration', 'method' => 'patch']) }}

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <div class="custom-control custom-checkbox">
                                            @php ($registrationEnabled = \Cache::get('app.registration.enabled', false))
                                            <input class="custom-control-input" type="checkbox" title="Enable registration" id="registration" name="enabled" value="1" @if ($registrationEnabled) checked="checked" @endif>
                                            <label class="custom-control-label" for="registration">Enable</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <span class="fa fa-check"></span>

                                        <span class="d-none d-lg-inline-flex">
                                            &nbsp;Set
                                        </span>
                                    </button>
                                </div>
                            </div>

                            {{ Form::close() }}
                        </div>

                        <div class="col-12 col-lg-6">
                            <h5>Default libraries</h5>

                            @php ($libraries = App\Library::all())
                            @php ($defaultLibraries = \Cache::get('app.registration.libraries', []))

                            {{ Form::open(['action' => 'AdminController@putDefaultLibraries', 'method' => 'put']) }}

                            <div class="row">
                                @foreach ($libraries as $library)
                                    @php ($isDefault = array_key_exists($library->id, $defaultLibraries))

                                    <div class="col-4">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" type="checkbox" id="library-{{ $library->id }}" name="library_ids[]" value="{{ $library->id }}" title="Enable default access for new users" @if ($isDefault) checked="checked" @endif>
                                            <label class="custom-control-label" for="library-{{ $library->id }}">{{ $library->name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button class="btn btn-primary form-control mt-3" type="submit">
                                <span class="fa fa-check"></span>&nbsp;Set
                            </button>

                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
