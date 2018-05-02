@extends('layout')

@section ('title')
    Login
@endsection

@section ('content')

<div class="panel panel-default center-block" style="max-width:600px">
    <div class="panel-body">
        {{ Form::open([ 'action' => 'LoginController@login']) }}
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-sm-offset-3">
                    <div class="input-group">
                        <span class="input-group-addon glyphicon glyphicon-user" id="addon-username"></span>
                        {{ Form::text('username', null, ['class' => 'form-control', 'aria-describedby' => 'addon-username']) }}
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-sm-offset-3">
                    <div class="input-group">
                        <span class="input-group-addon glyphicon glyphicon-asterisk" id="addon-password"></span>

                        {{-- the below doesn't want to display properly *shrug* --}}
                        {{-- Form::password('password', null, ['class' => 'form-control', 'type' => 'password']) --}}
                        <input name="password" id="password" type="password" class="form-control" aria-describedby="addon-password">
                    </div>
                </div>
            </div>
            <br>
            <div class="row center-block">
                <button type="submit" class="btn btn-default center-block">
                    <span class="glyphicon glyphicon-log-in"></span>&nbsp; Login
                </button>
            </div>
        {{ Form::close() }}

        @include ('shared.errors')
    </div>
</div>

@endsection

@section ('scripts')

@endsection
