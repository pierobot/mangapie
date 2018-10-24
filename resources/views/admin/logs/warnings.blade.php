@extends ('admin.logs.layout')

@section ('title')
    Admin &middot; Warnings
@endsection

@section ('card-content')
    @php ($warnings = \LogParser::get('warning'))
    @php ($errors_ = \LogParser::get('error'))

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <a class="nav-link active" href="#">
                        Warnings
                        <span class="badge badge-warning">
                            @if (! count($warnings))
                                0
                            @else
                                {{ count($warnings) }}
                            @endif
                        </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ URL::action('AdminController@logErrors') }}">
                        Errors
                        <span class="badge badge-danger">
                            @if (! count($errors_))
                                0
                            @else
                                {{ count($errors_) }}
                            @endif
                        </span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            This feature is currently not implemented.
            Please check the log file.
            {{--<table class="table table-hover table-condensed">--}}
                {{--<thead>--}}
                    {{--<tr>--}}
                        {{--<th>Date</th>--}}
                        {{--<th>Message</th>--}}
                    {{--</tr>--}}
                {{--</thead>--}}
                {{--<tbody>--}}
                {{--@foreach ($warnings as $warning)--}}
                    {{--<tr>--}}
                        {{--<td>{{ \Carbon\Carbon::parse($warning['datetime'])->diffForHumans() }}</td>--}}
                        {{--<td>{{ $warning['messagectx'] }}</td>--}}
                    {{--</tr>--}}
                {{--@endforeach--}}
                {{--</tbody>--}}
            {{--</table>--}}
        </div>
    </div>
@endsection
