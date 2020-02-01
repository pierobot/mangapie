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
        @foreach ($roles as $role)
            <div class="col-12 col-md-3 mb-3">
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">{{ $role->name }}</span>
                    </div>
                    <div class="card-body">
                        @php($permissions = $role->permissions)

                        @if ($role->name == 'Administrator')
                            All permissions are granted.
                        @elseif (! empty($permissions->count()))
                            {{-- TODO: List these in a better looking way --}}
                            @foreach ($permissions as $permission)
                                {{ $permission->action }}
                                @if (! empty($permission->model_type))
                                    {{ class_basename($permission->model_type) }}
                                @endif

                                @if (! empty($permission->model_id))
                                    {{ $permission->model_id }}
                                @endif
                                <br>
                            @endforeach
                        @else
                            No permissions.
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
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
                    </div>
                    <br>

                    Has access to:
                    @php($libraries = App\Library::all())
                    @foreach ($libraries as $library)
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input form-control" type="checkbox" id="library-{{ $library->id }}" name="libraries[]" value="{{ $library->id }}">
                            <label class="custom-control-label" for="library-{{ $library->id }}">{{ $library->name }}</label>
                        </div>
                    @endforeach

                    <hr>

                    <button class="btn btn-primary" type="submit">Create</button>

            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
