@section ('title')
    Account Settings :: Mangapie
@endsection

@extends ('settings.layout')

@section ('tab-content')
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ URL::action('UserSettingsController@account') }}">Account</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ URL::action('UserSettingsController@visuals') }}">Visuals</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ URL::action('UserSettingsController@profile') }}">Profile</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            {{ Form::open(['action' => 'UserSettingsController@patchPassword', 'method' => 'patch']) }}

            <div class="row">
                <div class="col-12 col-md-6">
                    <label>Change Password</label>

                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Current</span>
                            </div>

                            <input class="form-control" type="password" name="current" title="Your current password" placeholder="Your current password">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">New</span>
                            </div>

                            <input class="form-control" type="password" name="new" title="The new password" placeholder="The new password">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Verify</span>
                            </div>

                            <input class="form-control" type="password" name="verify" title="Verify your new password" placeholder="Verify your new password">
                        </div>
                    </div>

                    <div class="form-group">
                        <button class="btn btn-primary form-control" type="submit">
                            <span class="fa fa-check"></span>
                            &nbsp;Set
                        </button>
                    </div>
                </div>
            </div>

            {{ Form::close() }}
        </div>
    </div>
@endsection
