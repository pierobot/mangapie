@if (\Session::has('success'))
    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <span class="glyphicon glyphicon-ok"></span>&nbsp; {{ \Session::get('success') }}
    </div>
@endif