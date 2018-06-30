@php ($currentNavPill = 'visuals')

@section ('title')
    Visual Settings :: Mangapie
@endsection

@extends ('settings.layout')

@section ('tab-content')
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

    {{--<div class="panel panel-default">--}}
        {{--<div class="panel-heading">--}}
            {{--<div class="panel-title">--}}
                {{--<span class="glyphicon glyphicon-pencil"></span>&nbsp;Themes--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<div class="panel-body">--}}
            {{--{{ Form::open(['action' => 'UserSettingsController@update']) }}--}}
            {{--{{ Form::hidden('action', 'theme.update') }}--}}

            {{--<div class="form-group row">--}}
                {{--<div class="col-xs-12 col-md-3">--}}
                    {{--{{ Form::label('theme:', null, ['for' => 'theme']) }}--}}

                    {{--<select name="theme" class="form-control">--}}
                        {{--<option disabled="disabled" selected="selected">{{ $current_theme }}</option>--}}
                        {{--@foreach ($theme_collections as $collection_name => $theme)--}}
                            {{--<optgroup label="{{ $collection_name }}">--}}
                                {{--@foreach ($theme as $theme_name => $theme_path)--}}
                                    {{--<option value="{{ $collection_name . '/' . $theme_name }}">{{ $theme_name }}</option>--}}
                                {{--@endforeach--}}
                            {{--</optgroup>--}}
                        {{--@endforeach--}}
                    {{--</select>--}}
                {{--</div>--}}
            {{--</div>--}}

            {{--<div class="form-group row">--}}
                {{--<div class="col-xs-6">--}}

                    {{--{{ Form::submit('Save', ['class' => 'btn btn-success']) }}--}}
                {{--</div>--}}
            {{--</div>--}}


            {{--{{ Form::close() }}--}}
        {{--</div>--}}
    {{--</div>--}}
@endsection
