@extends ('admin.layout')

@section ('title')
    Admin &middot; Users
@endsection

@section ('top-menu')
    <ul class="nav nav-pills mb-3 justify-content-center">
        <li class="nav-item"><a href="{{ URL::action('AdminController@statistics') }}" class="nav-link">Statistics</a></li>
        <li class="nav-item"><a href="{{ URL::action('AdminController@config') }}" class="nav-link">Config</a></li>
        <li class="nav-item"><a href="{{ URL::action('AdminController@libraries') }}" class="nav-link">Libraries</a></li>
        <li class="nav-item"><a href="#" class="nav-link active">Users</a></li>
    </ul>
@endsection

@section ('card-content')
    <hr>

    <div class="row">
        <div class="col-12">
            {{ Form::open(['action' => 'AdminController@searchUsers']) }}
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Username" name="name">
                <div class="input-group-append">
                    <button class="form-control btn btn-primary" type="submit">
                        <span class="fa fa-search"></span>
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <table class="table">
                <thead class="bg-dark">
                    <tr>
                        <td>Id</td>
                        <td>Name</td>
                        <td>Created</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr @if ($user->admin || $user->maintainer) class="bg-secondary" @endif>
                            <td>{{ $user->id }}</td>
                            <td><a href="{{ URL::action('UserController@index', [$user]) }}">{{ $user->name }}</a></td>
                            <td>{{ $user->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            {{-- User searches are not paginated at the moment, so do not render --}}
            @if (isset($users) && ($users instanceof \Illuminate\Pagination\LengthAwarePaginator))
                {{ $users->render('vendor.pagination.bootstrap-4') }}
            @elseif (isset($users) && ! ($users instanceof \Illuminate\Pagination\LengthAwarePaginator))
                <a href="{{ URL::action('AdminController@users') }}" class="btn btn-danger">
                    <span class="fa fa-times"></span>&nbsp;Reset
                </a>
            @endif
        </div>
    </div>
@endsection
