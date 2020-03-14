@extends ('admin.layout')

@section ('title')
    Admin &middot; Config
@endsection

@section ('top-menu')
    <ul class="nav nav-pills mb-3 justify-content-center">
        <li class="nav-item"><a href="{{ URL::action('AdminController@statistics') }}" class="nav-link">Statistics</a></li>
        <li class="nav-item"><a href="#" class="nav-link active">Config</a></li>
        <li class="nav-item"><a href="{{ URL::action('AdminController@libraries') }}" class="nav-link">Libraries</a></li>
        <li class="nav-item"><a href="{{ URL::action('AdminController@users') }}" class="nav-link">Users</a></li>
        <li class="nav-item"><a href="{{ URL::action('RoleController@index') }}" class="nav-link">Roles</a></li>
    </ul>
@endsection

@section ('card-content')
    <hr>
    <div class="row">
        <div class="col-12">
            <h4><strong>Registration</strong></h4>
        </div>
        <div class="col-12 col-md-6">
            <h5>General</h5>

            {{ Form::open(['action' => 'AdminController@patchRegistration', 'method' => 'patch']) }}

            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <div class="custom-control custom-checkbox">
                            @php ($registrationEnabled = \Cache::tags(['config', 'registration'])->get('enabled', false))
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

        <div class="col-12 col-md-6">
            <h5>Default Roles</h5>

            @php ($roles = App\Role::all())
            @php ($defaultRoles = \Cache::tags(['config', 'registration'])->get('roles', []))

            {{ Form::open(['action' => 'AdminController@putDefaultRoles', 'method' => 'put']) }}

            <div class="row">
                @foreach ($roles as $role)
                    @php ($isDefault = array_key_exists($role->id, $defaultRoles))

                    <div class="col-4">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="role-{{ $role->id }}" name="role_ids[]" value="{{ $role->id }}" title="Assign role to new users" @if ($isDefault) checked="checked" @endif>
                            <label class="custom-control-label" for="role-{{ $role->id }}">{{ $role->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>

            <button class="btn btn-primary form-control mt-3" type="submit">
                <span class="fa fa-check"></span>&nbsp;Set
            </button>

            {{ Form::close() }}
        </div>

        <div class="col-12 col-md-6">
            <h5></h5>
        </div>
    </div>

    <hr>
    <div class="row">
        <div class="col-12">
            <h4><strong>Image</strong></h4>
        </div>
        <div class="col-12 col-md-6">
            <h5>Extraction</h5>

            {{ Form::open(['action' => 'AdminController@patchImageExtraction', 'method' => 'patch']) }}

            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <div class="custom-control custom-checkbox">
                            @php ($extractEnabled = \Cache::tags(['config', 'image', 'extract'])->get('enabled', false))
                            <input class="custom-control-input" type="checkbox" title="Enable image extraction" id="extract" name="enabled" value="1" @if ($extractEnabled) checked="checked" @endif>
                            <label class="custom-control-label" for="extract">Enable</label>
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

        <div class="col-12 col-md-6">
            <h5>Scheduler</h5>

            {{ Form::open(['action' => 'AdminController@patchScheduler', 'method' => 'patch']) }}

            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <div class="custom-control custom-checkbox">
                                @php ($imageSchedulerEnabled = \Cache::tags(['config', 'image', 'scheduler'])->get('enabled', false))
                                <input class="custom-control-input" type="checkbox" title="Enable image scheduler" id="scheduler" name="enabled" value="1" @if ($imageSchedulerEnabled) checked="checked" @endif>
                                <label class="custom-control-label" for="scheduler">Enable</label>
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
            </div>

            {{ Form::close() }}

            {{ Form::open(['action' => 'AdminController@putScheduler', 'method' => 'put']) }}

            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            Cron
                        </span>
                    </div>

                    @php ($cronExpression = \Cache::tags(['config', 'image', 'scheduler'])->get('cron', '@daily'))
                    <input class="form-control" type="text" title="Cron expression" id="image-cron" name="cron" @if (! empty($cronExpression)) value="{{ $cronExpression }}" @endif>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <button class="btn btn-primary form-control" type="submit" name="action" value="reset">
                            <span class="fa fa-exclamation-circle"></span>

                            &nbsp;Reset
                        </button>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <button class="btn btn-primary form-control" type="submit" name="action" value="set">
                            <span class="fa fa-check"></span>

                            &nbsp;Set
                        </button>
                    </div>
                </div>
            </div>

            {{ Form::close() }}
        </div>
    </div>

    <hr>
    <div class="row">
        <div class="col-12">
            <h4><strong>Views</strong></h4>
        </div>
        <div class="col-12 col-md-6">
            <h5>General</h5>

            {{ Form::open(['action' => 'AdminController@patchViews', 'method' => 'patch']) }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <div class="custom-control custom-checkbox">
                            @php ($viewsEnabled = \Cache::tags(['config', 'views'])->get('enabled', true))
                            <input class="custom-control-input" type="checkbox" title="Enable view counting" id="views" name="enabled" value="1" @if ($viewsEnabled) checked="checked" @endif>
                            <label class="custom-control-label" for="views">Enable</label>
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

        <div class="col-12 col-md-6">
            <h5>Time <span class="fa fa-question text-info" data-toggle="collapse" data-target="#time-example"></span></h5>

            <div class="collapse" id="time-example">
                <p class="text-info">
                    Threshold is the amount of time that should pass from a user's last view in order for the count to be increased.
                    The default time required is 3 hours (3h). In order to always count views, regardless of time passed, simply disable it.
                </p>
            </div>

            {{ Form::open(['action' => 'AdminController@patchViewsTime', 'method' => 'patch']) }}

            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <div class="custom-control custom-checkbox">
                            @php ($viewsTimeEnabled = \Cache::tags(['config', 'views', 'time'])->get('enabled', true))
                            <input class="custom-control-input" type="checkbox" title="Enable view counting by time" id="views_time_checkbox" name="enabled" value="1" @if ($viewsTimeEnabled) checked="checked" @endif>
                            <label class="custom-control-label" for="views_time_checkbox">Enable</label>
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

            {{ Form::open(['action' => 'AdminController@putViewsTime', 'method' => 'put']) }}

            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            Threshold
                        </span>
                    </div>

                    @php ($timeThreshold = \Cache::tags(['config', 'views', 'time'])->get('threshold', '3h'))
                    <input class="form-control" type="text" name="threshold" value="{{ $timeThreshold }}" title="The default threshold value">
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <button class="btn btn-primary form-control" type="submit" name="action" value="reset">
                            <span class="fa fa-exclamation-circle"></span>

                            &nbsp;Reset
                        </button>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <button class="btn btn-primary form-control" type="submit" name="action" value="set">
                            <span class="fa fa-check"></span>

                            &nbsp;Set
                        </button>
                    </div>
                </div>
            </div>

            {{ Form::close() }}
        </div>
    </div>

    <hr>
    <div class="row">
        <div class="col-12">
            <h4><strong>Heat</strong></h4>
        </div>
        <div class="col-12 col-md-6">
            <h5>General</h5>

            {{ Form::open(['action' => 'AdminController@patchHeat', 'method' => 'patch']) }}

            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <div class="custom-control custom-checkbox">
                            @php ($heatEnabled = \Cache::tags(['config', 'heat'])->get('enabled', false))
                            <input class="custom-control-input" type="checkbox" title="Enable heat" id="heat" name="enabled" value="1" @if ($heatEnabled) checked="checked" @endif>
                            <label class="custom-control-label" for="heat">Enable</label>
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

        <div class="col-12 col-md-6">
            <h5>Parameters <span class="fa fa-question text-info" data-toggle="collapse" data-target="#heat-example"></span></h5>

            <div class="collapse" id="heat-example">
                <p class="text-info">
                    With default values, after 72 hours with no views, the heat value will
                    drop to approximately 1/2 the value. If the value drops
                    below the threshold, then those images will be removed.
                </p>
                <p class="text-warning">
                    The threshold should be a value less than the default value.
                </p>
                <p class="text-warning">
                    The cooldown value is small because the algorithm is exponential.<br>
                    value = value * e^(-(cooldownRate) * hourDifference);
                </p>
            </div>

            {{ Form::open(['action' => 'AdminController@postHeat']) }}

            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            Default
                        </span>
                    </div>

                    @php ($defaultHeat = \Cache::tags(['config', 'heat'])->get('default', 100))
                    <input class="form-control" type="number" name="heat_default" value="{{ $defaultHeat }}" title="Default heat value">
                </div>
            </div>

            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            Threshold
                        </span>
                    </div>

                    @php ($defaultThreshold = \Cache::tags(['config', 'heat'])->get('threshold', 50))
                    <input class="form-control" type="number" name="heat_threshold" value="{{ $defaultThreshold }}" title="Default threshold value">
                </div>
            </div>

            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            Heat up
                        </span>
                    </div>

                    @php ($defaultHeatup = \Cache::tags(['config', 'heat'])->get('heat', 3.0))
                    <input class="form-control" type="number" name="heat_heat" value="{{ $defaultHeatup }}" title="Default heat up value" step="0.5">
                </div>
            </div>

            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            Cooldown
                        </span>
                    </div>

                    @php ($defaultCooldown = \Cache::tags(['config', 'heat'])->get('cooldown', 0.01))
                    <input class="form-control" type="number" name="heat_cooldown" value="{{ $defaultCooldown }}" title="Default cooldown value" step="0.01">
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <button class="btn btn-primary form-control" type="submit" name="action" value="reset">
                            <span class="fa fa-exclamation-circle"></span>

                            &nbsp;Reset
                        </button>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <button class="btn btn-primary form-control" type="submit" name="action" value="set">
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
