@extends('layout')

@section ('title')
    Login
@endsection

@section ('content')
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Login</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open([ 'action' => 'LoginController@login']) }}
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-sm-offset-3">
                            <div class="input-group">
                                <span class="input-group-addon" id="addon-username">
                                    <span class="glyphicon glyphicon-user"></span>
                                </span>
                                {{ Form::text('username', null, ['class' => 'form-control', 'aria-describedby' => 'addon-username']) }}
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-sm-offset-3">
                            <div class="input-group">
                                <span class="input-group-addon" id="addon-password">
                                    <span class="glyphicon glyphicon-asterisk"></span>
                                </span>
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
        </div>
    </div>
@endsection

@section ('scripts')

@endsection
