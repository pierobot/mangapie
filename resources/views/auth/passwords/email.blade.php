@extends('layout')

@section ('title')
    Forgot Password
@endsection

@section ('content')
    <div class="container mt-3">
        <div class="row mt-3 justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title text-center mb-0">Forgot Password</h4>
                    </div>
                    <div class="card-body">
                        {{ Form::open([ 'action' => 'Auth\ForgotPasswordController@sendResetLinkEmail']) }}

                        <div class="form-row justify-content-center">
                            <div class="col-12 col-xl-7 mb-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <span class="fa fa-envelope"></span>
                                        </span>
                                    </div>

                                    <input type="email" class="form-control" id="email" name="email" title="Email" placeholder="Email">
                                </div>
                            </div>
                        </div>

                        <div class="form-row justify-content-center">
                            <div class="col-12 col-xl-7">
                                <button class="btn btn-primary form-control" type="submit">
                                    <span class="fa fa-send"></span>&nbsp; Send reset link
                                </button>
                            </div>
                        </div>

                        {{ Form::close() }}

                        @include ('shared.errors')
                        @include ('shared.success')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
