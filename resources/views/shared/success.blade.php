@if (\Session::has('success'))
    <div class="alert alert-success">
        <span class="glyphicon glyphicon-ok"></span>&nbsp; {{ \Session::get('success') }}
    </div>
@endif