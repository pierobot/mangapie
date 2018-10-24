@extends ('admin.dashboard.layout')

@section ('title')
    Admin &middot; Config
@endsection

@section ('card-content')
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <a class="nav-link" href="{{ URL::action('AdminController@statistics') }}">Statistics</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="#">Config</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <h4><strong>Registration</strong></h4>

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
            </div>
        </div>
    </div>
@endsection
