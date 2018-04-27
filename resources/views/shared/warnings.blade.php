@if (\Session::has('warnings.data'))
    <div class="alert alert-warning alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <span class="glyphicon glyphicon-info-sign"></span>&nbsp; {{ \Session::get('warnings.message') }}
        <ul>
            @foreach (\Session::get('warnings.data') as $warning)
                <li><b>{{ $warning }}</b></li>
            @endforeach
        </ul>
    </div>
@endif