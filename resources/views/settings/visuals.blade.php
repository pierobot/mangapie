@section ('title')
    Visual Settings :: Mangapie
@endsection

@extends ('settings.layout')

@section ('side-top-menu')
    @component ('settings.components.side-top-menu', [
        'active' => 'Visuals',
        'items' => [
            ['title' => 'Account', 'icon' => 'user', 'action' => 'UserSettingsController@account'],
            ['title' => 'Visuals', 'icon' => 'user', 'action' => 'UserSettingsController@visuals'],
            ['title' => 'Profile', 'icon' => 'user', 'action' => 'UserSettingsController@profile']
        ]
    ])
    @endcomponent
@endsection

@section ('tab-content')
    <div class="card">
        <div class="card-header">
            Visual Settings
        </div>
        <div class="card-body">
            <label>Default Reader Direction</label>
            {{ Form::open(['action' => 'UserSettingsController@patchReaderDirection', 'method' => 'patch']) }}
            {{ Form::hidden('user_id', \Auth::user()->id) }}

            <div class="form-group">
                <div class="form-row">
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="custom-control custom-radio">
                            <input class="custom-control-input" type="radio" id="ltr" name="direction" value="ltr" @if ($user->read_direction === 'ltr') checked @endif autocomplete="off"/>
                            <label class="custom-control-label @if ($user->read_direction === 'ltr') active @endif" for="ltr">Left to Right</label>
                        </div>
                    </div>

                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="custom-control custom-radio">
                            <input class="custom-control-input" type="radio" id="rtl" name="direction" value="rtl" @if ($user->read_direction === 'rtl') checked @endif autocomplete="off"/>
                            <label class="custom-control-label @if ($user->read_direction === 'rtl') active @endif" for="rtl">Right to Left</label>
                        </div>
                    </div>

                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="custom-control custom-radio">
                            <input class="custom-control-input" type="radio" id="vrt" name="direction" value="vrt" @if($user->read_direction === 'vrt') checked @endif autocomplete="off"/>
                            <label class="custom-control-label @if ($user->read_direction === 'ltr') active @endif" for="vrt">Vertical</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="form-row">
                    <div class="col-12 col-md-2">
                        <button class="btn btn-primary form-control" type="submit">
                            <span class="fa fa-check"></span>
                            &nbsp;Set
                        </button>
                    </div>
                </div>
            </div>

            {{ Form::close() }}
        </div>
    </div>
@endsection
