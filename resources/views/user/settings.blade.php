@extends ('layout')

@section ('title')
    Settings &middot; {{ \Auth::user()->getName() }}
@endsection

@section ('content')
    <h2 class="text-center"><b>Settings &middot; {{ \Auth::user()->getName() }}</b></h2>

    @include ('shared.success')
    @include ('shared.errors')

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="glyphicon glyphicon-pencil"></span>&nbsp;Personal
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                {{ Form::open(['action' => 'UserSettingsController@update']) }}

                <div class="col-xs-12 col-md-6">
                    <h4>Password</h4>
                    <hr>
                    <div class="row">
                        <div class="form-group col-xs-12 col-md-6">
                            {{ Form::hidden('action', 'password.update') }}
                            {{ Form::label('old password:', null, ['for' => 'old-password']) }}
                            <input name="old-password" id="old-password" type="password" class="form-control"
                                   placeholder="Enter old password here...">

                            {{ Form::label('new password:', null, ['for' => 'new-password']) }}
                            <input name="new-password" id="new-password" type="password" class="form-control"
                                   placeholder="Enter new password here...">

                            {{ Form::label('confirm password:', null, ['for' => 'confirm-password']) }}
                            <input name="confirm-password" id="confirm-password" type="password"
                                   class="form-control" placeholder="Confirm new password here...">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                {{ Form::submit('Save', ['class' => 'btn btn-warning']) }}
                            </div>
                        </div>
                    </div>
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="glyphicon glyphicon-pencil"></span>&nbsp;Themes
            </div>
        </div>
        <div class="panel-body">
            {{ Form::open(['action' => 'UserSettingsController@update']) }}
            {{ Form::hidden('action', 'theme.update') }}
            <div class="row">
                <div class="col-xs-12 col-md-3">
                    <div class="form-group">
                        {{ Form::label('theme:', null, ['for' => 'theme']) }}

                        <select name="theme" class="form-control">
                            <option disabled="disabled" selected="selected">{{ $current_theme }}</option>
                                @foreach ($theme_collections as $collection_name => $theme)
                                    <optgroup label="{{ $collection_name }}">
                                        @foreach ($theme as $theme_name => $theme_path)
                                            <option value="{{ $collection_name . '/' . $theme_name }}">{{ $theme_name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">
                        {{ Form::submit('Save', ['class' => 'btn btn-success']) }}
                    </div>
                </div>
            </div>

            {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
