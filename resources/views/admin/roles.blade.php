@extends ('admin.layout')

@section ('title')
    Admin &middot; Users
@endsection

@section ('top-menu')
    <ul class="nav nav-pills mb-3 justify-content-center">
        <li class="nav-item"><a href="{{ URL::action('AdminController@statistics') }}" class="nav-link">Statistics</a></li>
        <li class="nav-item"><a href="{{ URL::action('AdminController@config') }}" class="nav-link">Config</a></li>
        <li class="nav-item"><a href="{{ URL::action('AdminController@libraries') }}" class="nav-link">Libraries</a></li>
        <li class="nav-item"><a href="{{ URL::action('AdminController@users') }}" class="nav-link">Users</a></li>
        <li class="nav-item"><a href="#" class="nav-link active">Roles</a></li>
    </ul>
@endsection

@section ('card-content')
    <hr>
    <h4><strong>Existing</strong></h4>
    <div class="row">
        <div class="col-12">
            <div class="nav nav-pills mb-3">
                @foreach ($roles as $role)
                    <a href="#{{ $role->name }}" class="nav-link @if ($role->name === "Administrator") active @endif" data-toggle="pill">{{ $role->name }}</a>
                @endforeach
            </div>
        </div>

        <div class="col-12">
            <div class="tab-content">
                @foreach ($roles as $index => $role)
                    <div class="tab-pane fade @if ($role->name === "Administrator") show active @endif" id="{{ $role->name }}" role="tabpanel">
                        {{ Form::open(['action' => ['AdminController@putRole', $role], 'method' => 'put']) }}
                        {{ Form::hidden('role_id', $role->id) }}

                        <strong>Comments</strong>
                        {{ Form::hidden("actions[0][model_type]", \App\Comment::class) }}

                            @foreach ($allActions as $action)
                                @php($hasPermissionForAction = $role->hasPermission($action, \App\Comment::class))

                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="{{ $role->name }}-comment-{{ $action }}" @if ($hasPermissionForAction) checked @endif name="{{ "actions[0][class][actions][]" }}"  value="{{ $action }}">
                                    <label class="custom-control-label" for="{{ $role->name }}-comment-{{ $action }}">{{ $action }}</label>
                                </div>
                            @endforeach

                        <hr>
                        <strong>Library</strong>

                        {{ Form::hidden("actions[1][model_type]", \App\Library::class) }}

                            @foreach ($allActions as $action)
                                @php($hasPermissionForAction = $role->hasPermission($action, \App\Library::class))

                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="{{ $role->name }}-library-{{ $action }}" @if ($hasPermissionForAction) checked @endif name="{{ "actions[1][class][actions][]" }}" value="{{ $action }}">
                                    <label class="custom-control-label" for="{{ $role->name }}-library-{{ $action }}">{{ $action }}</label>
                                </div>
                            @endforeach

                            @php($hasPermissionForAction = $role->hasPermission('viewAny', \App\Library::class))
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="{{ $role->name }}-library-viewAny" @if ($hasPermissionForAction) checked @endif name="{{ "actions[1][class][actions][]" }}" value="viewAny">
                                <label class="custom-control-label" for="{{ $role->name }}-library-viewAny">viewAny</label>
                            </div>

                            @foreach ($libraries as $libraryIndex => $library)
                            <div class="custom-control custom-checkbox">
                                {{ Form::hidden("actions[1][object][${libraryIndex}][model_id]", $library->id) }}
                                @php($hasPermissionForAction = $role->hasPermission('view', \App\Library::class, $library->id))
                                <input class="custom-control-input" type="checkbox" id="{{ $role->name }}-library-view-{{ $library->id }}" @if ($hasPermissionForAction) checked @endif name="{{ "actions[1][object][${libraryIndex}][actions][]" }}" value="view">
                                <label class="custom-control-label" for="{{ $role->name }}-library-view-{{ $library->id }}">view <mark>{{ $library->name }}</mark></label>
                            </div>
                            @endforeach

                        <hr>
                        <strong>Manga</strong>

                        {{ Form::hidden("actions[2][model_type]", \App\Manga::class) }}
                        @foreach ($allActions as $action)
                            @php($hasPermissionForAction = $role->hasPermission($action, \App\Manga::class))

                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="{{ $role->name }}-manga-{{ $action }}" @if ($hasPermissionForAction) checked @endif name="{{ "actions[2][class][actions][]" }}"  value="{{ $action }}">
                                <label class="custom-control-label" for="{{ $role->name }}-manga-{{ $action }}">{{ $action }}</label>
                            </div>
                        @endforeach

                        <button class="btn btn-primary mt-3"><span class="fa fa-check"></span>&nbsp;Save</button>

                        {{ Form::close() }}

                        <hr>
                        <div class="row">
                            <div class="col">
                                {{ Form::open(['action' => ['AdminController@destroyRole', $role], 'method' => 'delete']) }}
                                <button class="btn btn-danger" type="submit">
                                    <span class="fa fa-times"></span>&nbsp;Delete
                                </button>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <hr>
    <h4><strong>Create</strong></h4>
    <div class="row">
        <div class="col">
            {{ Form::open(['action' => 'AdminController@createRole']) }}
            <div class="row">
                <div class="col-12 col-sm-6">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text fa fa-user-alt"></span>
                        </div>

                        <input class="form-control" autocomplete="off" placeholder="Role name" name="name">

                        <div class="input-group-append">
                            <button class="btn btn-primary"  type="submit">
                                <span class="fa fa-check"></span>

                                <span class="d-none d-lg-inline-flex">
                                    &nbsp;Create
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
