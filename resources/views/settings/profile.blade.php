@php($currentNavPill = 'profile')

@section ('title')
    Profile Settings :: Mangapie
@endsection

@extends ('settings.layout')

@section ('tab-content')
    <div class="tab-content">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    <span class="glyphicon glyphicon-question-sign"></span>&nbsp;About
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">
                        {{ Form::open(['action' => 'UserSettingsController@updateProfile']) }}
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-9">
                                {{ Form::textarea('about', ! empty($user->getAbout()) ? $user->getAbout() : "", ['class' => 'form-control', 'maxlength' => 1024]) }}
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-2">
                                {{ Form::submit('Update', ['class' => 'btn btn-success form-control']) }}
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
