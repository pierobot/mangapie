@extends('layout')

@section ('title')
    Login
@endsection

@section ('content')
    <div class="container mt-3">
        <div class="row mt-3 justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title text-center mb-0">Login</h4>
                    </div>
                    <div class="card-body">
                        {{ Form::open([ 'action' => 'Auth\LoginController@login']) }}
                        <div class="form-row justify-content-center">
                            <div class="col-12 col-xl-7 mb-3">
                                <div class="input-group">
                                    <div class="input-group-prepend" id="prepend-username">
                                        <span class="input-group-text">
                                            <span class="fa fa-user"></span>
                                        </span>
                                    </div>

                                    <input type="text" class="form-control" id="name" name="name" title="Username" placeholder="Username">
                                </div>
                            </div>
                        </div>

                        <div class="form-row justify-content-center">
                            <div class="col-12 col-xl-7 mb-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <span class="fa fa-lock"></span>
                                        </span>
                                    </div>

                                    <input type="password" class="form-control" id="password" name="password" title="Password" placeholder="Password">
                                </div>
                            </div>
                        </div>

                        <div class="form-row justify-content-center">
                            <div class="col-12 col-xl-7 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input form-control" type="checkbox" id="remember" name="remember">
                                    <label class="custom-control-label" for="remember">Remember me</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-row justify-content-center">
                            <div class="col-12 col-xl-7">
                                <button class="btn btn-primary form-control" type="submit">
                                    <span class="fa fa-sign-in"></span>&nbsp; Login
                                </button>
                            </div>
                        </div>

                        {{ Form::close() }}

                        <div class="row text-center">
                            <div class="col-12">
                                <a href="{{ URL::action('Auth\ForgotPasswordController@showLinkRequestForm') }}">I forgot my password</a>
                            </div>
                            <div class="col-12">
                                <a href="{{ URL::action('Auth\RegisterController@showRegistrationForm') }}">Create an account</a>
                            </div>
                        </div>

                        @include ('shared.errors')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
