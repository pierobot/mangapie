@extends ('layout')

@section ('title')
    Settings &middot; {{ \Auth::user()->getName() }}
@endsection

@section ('content')
    <h2 class="text-center"><b>Settings &middot; {{ \Auth::user()->getName() }}</b></h2>

    @include ('shared.success')
    @include ('shared.errors')

    <div class="panel-body">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#edit-user-content" data-toggle="tab"><span class="glyphicon glyphicon-pencil"></span> Edit</a></li>
            <li><a href="#edit-theme-content" data-toggle="tab"><span class="glyphicon glyphicon-pencil"></span> Themes</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="edit-user-content">

                <ul class="list-group">

                    {{ Form::open(['action' => 'UserSettingsController@update']) }}

                    <li class="list-group-item">
                        <div class="row">
                            <div class="form-group col-xs-12 col-lg-3">
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

                        <div class="row">
                            <div class="col-xs-4 col-lg-3">
                                {{ Form::submit('Save', ['class' => 'btn btn-warning']) }}
                            </div>
                        </div>
                    </li>

                    {{ Form::close() }}
                </ul>
            </div>

            <div class="tab-pane" id="edit-theme-content">
                <ul class="list-group">
                    <li class="list-group-item">

                        {{ Form::open(['action' => 'UserSettingsController@update']) }}

                        <div class="row">
                            <div class="form-group col-xs-12 col-lg-3">

                                {{ Form::hidden('action', 'theme.update') }}
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

                        <div class="row">
                            <div class="form-group col-xs-12 col-lg-3">
                                {{ Form::submit('Save', ['class' => 'btn btn-success']) }}
                            </div>
                        </div>

                        {{ Form::close() }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
