@extends('layout')

@section ('title')
    Register
@endsection

@section ('content')
    <div class="row mt-3 justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-center mb-0">Create an account</h4>
                </div>
                <div class="card-body">
                    @if (\Cache::tags(['config', 'registration'])->get('enabled', false) == true)
                        {{ Form::open([ 'action' => 'Auth\RegisterController@register']) }}
                        <div class="form-row justify-content-center">
                            <div class="col-12 col-xl-7 mb-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
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
                                            <span class="fa fa-envelope"></span>
                                        </span>
                                    </div>

                                    <input type="email" class="form-control" id="email" name="email" title="Email" placeholder="Email">
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
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <span class="fa fa-lock"></span>
                                        </span>
                                    </div>

                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" title="Password confirmation" placeholder="Password (again)">
                                </div>
                            </div>
                        </div>

                        <div class="form-row justify-content-center">
                            <div class="col-12 col-xl-7">
                                <button class="btn btn-primary form-control" type="submit">
                                    <span class="fa fa-check"></span>&nbsp; Register
                                </button>
                            </div>
                        </div>

                        {{ Form::close() }}

                        @include ('shared.errors')
                    @else
                        <p class="text-danger text-center">
                            Registration is currently <strong>disabled</strong>.<br>
                            Go pester the site admin(s) for an account.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
