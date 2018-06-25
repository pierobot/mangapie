@extends ('settings.layout')

@section ('tab-content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="glyphicon glyphicon-pencil"></span>&nbsp;Personal
            </div>
        </div>
        <div class="panel-body">

            {{ Form::open(['action' => 'UserSettingsController@update']) }}

            <div class="form-group row">
                <div class="col-xs-12 col-md-3">
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
            <div class="form-group row">
                <div class="col-xs-12 col-md-3">
                    {{ Form::submit('Save', ['class' => 'btn btn-warning']) }}
                </div>
            </div>

            {{ Form::close() }}

        </div>
    </div>
@endsection