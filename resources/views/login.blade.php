@extends('layout')

@section ('title')
    Login
@endsection

@section ('content')
    <div class="row pt-2">
        <div class="col-lg-6 mx-auto">
            <div class="card bg-transparent">
                <div class="card-header">
                    <h4 class="card-title text-center">Login</h4>
                </div>
                <div class="card-body">
                    {{ Form::open([ 'action' => 'LoginController@login']) }}
                    <div class="form-row">
                        <div class="col-12 col-md-6 mx-auto mb-3">
                            <div class="input-group">
                                <div class="input-group-prepend" id="prepend-username">
                                    <span class="input-group-text fa fa-user"></span>
                                </div>

                                {{ Form::text('username', null, ['class' => 'form-control', 'aria-describedby' => 'prepend-username']) }}
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-12 col-md-6 mx-auto mb-3">
                            <div class="input-group">
                                <div class="input-group-prepend" id="prepend-password">
                                    <span class="input-group-text fa fa-asterisk"></span>
                                </div>

                                <input name="password" id="password" type="password" class="form-control" aria-describedby="prepend-password">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-12 text-center">
                            <button class="btn bg-primary" type="submit">
                                <span class="fa fa-sign-in"></span>&nbsp; Login
                            </button>
                        </div>
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
