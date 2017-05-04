@extends('layout')

@section ('content')

<div class="panel panel-default center-block" style="max-width:600px">
    <div class="panel-body">
    {{ Form::open([ 'action' => 'LoginController@login']) }}

        <div class="row input-group col-xs-12 col-sm-6 col-lg-6 center-block">
            {{ Form::label('username', null, ['for' => 'username']) }}
            {{ Form::text('username', null, ['class' => 'form-control']) }}
        </div>
        <div class="row input-group col-xs-12 col-sm-6 col-lg-6 center-block">
            {{ Form::label('password', null, ['for' => 'password']) }}

            {{-- the below doesn't want to display properly *shrug* --}}
            {{-- Form::password('password', null, ['class' => 'form-control', 'type' => 'password']) --}}
            <input name="password" id="password" type="password" class="form-control">
        </div>
        <div class="row">
            <br>
            {{ Form::submit('Login', ['class' => 'btn btn-default center-block', 'type' => 'submit']) }}
        </div>

    {{ Form::close() }}

    @if (compact('login_failed') == true)
        @if ($login_failed == true)
        <div class="row alert alert-danger col-xs-12 col-sm-12 col-lg-12 center-block">
            Authentication failure. Incorrect username or password.
        </div>
        @endif
    @endif
    </div>
</div>

@endsection

@section ('scripts')



@endsection