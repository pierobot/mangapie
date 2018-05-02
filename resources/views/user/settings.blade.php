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

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="glyphicon glyphicon-book"></span>&nbsp;Reader
            </div>
        </div>
        <div class="panel-body">
            <label>Direction:</label>
            {{ Form::open(['action' => 'UserSettingsController@update']) }}
            {{ Form::hidden('action', 'reader.update') }}

            <div class="form-group">
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-default {{ \Auth::user()->getLtr() ? "active" : "" }}">
                        <span class="glyphicon glyphicon-arrow-right"></span>
                        <input type="radio" name="ltr" id="ltr-true" value="1" autocomplete="off" {{ \Auth::user()->getLtr() ? 'checked' : '' }}> Left to Right
                        <span class="glyphicon glyphicon-arrow-right"></span>
                    </label>
                    <label class="btn btn-default {{ \Auth::user()->getLtr() ? "" : "active" }}">
                        <span class="glyphicon glyphicon-arrow-left"></span>
                        <input type="radio" name="ltr" id="ltr-false" value="0" autocomplete="off" {{ \Auth::user()->getLtr() ? '' : 'checked' }}> Right to Left
                        <span class="glyphicon glyphicon-arrow-left"></span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                {{ Form::submit('Save', ['class' => 'btn btn-success']) }}
            </div>

            {{ Form::close() }}
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

                <div class="form-group row">
                    <div class="col-xs-12 col-md-3">
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

                <div class="form-group row">
                    <div class="col-xs-6">

                        {{ Form::submit('Save', ['class' => 'btn btn-success']) }}
                    </div>
                </div>


            {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
