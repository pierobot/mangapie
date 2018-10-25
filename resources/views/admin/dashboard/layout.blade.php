@extends ('admin.layout')

@section ('side-top-menu')
    <div class="list-group side-top-menu d-inline-flex d-md-flex flex-wrap flex-md-nowrap flex-row flex-md-column mb-3 mb-md-auto">
        <a class="list-group-item active" href="{{ URL::action('AdminController@index') }}">
            <span class="fa fa-dashboard"></span>

            <div class="d-none d-md-inline-block">
                &nbsp;Dashboard
            </div>
        </a>

        <a class="list-group-item" href="{{ URL::action('AdminController@libraries') }}">
            <span class="fa fa-book"></span>

            <div class="d-none d-md-inline-block">
                &nbsp;Libraries
            </div>
        </a>

        <a class="list-group-item" href="{{ URL::action('AdminController@users') }}">
            <span class="fa fa-users"></span>

            <div class="d-none d-md-inline-block">
                &nbsp;Users
            </div>
        </a>

        <a class="list-group-item" href="{{ URL::action('AdminController@logs') }}">
            <span class="fa fa-clipboard-list"></span>

            <div class="d-none d-md-inline-block">
                &nbsp;Logs
            </div>
        </a>
    </div>
@endsection
