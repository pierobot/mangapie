<div class="row">
    <div class="col-12">
        @if (isset($header))
            <h3 class="text-center">
                <b>{{ $header }}</b>
            </h3>
        @endif
    </div>
</div>

@php($user = request()->user())

<div class="row mb-3">
    <div class="col-12 text-center">
        <button class="btn btn-primary @if ($user->display === 'list') active @endif" id="display-list" title="Display in a list">
            <span class="fa fa-list"></span>
        </button>

        <button class="btn btn-primary @if ($user->display === 'grid') active @endif" id="display-grid" title="Display in a grid">
            <span class="fa fa-th-large"></span>
        </button>

        <a href="{{ request()->fullUrlWithQuery(['sort' => 'asc']) }}" class="btn btn-primary @if ($sort === 'asc') active @endif" title="Display in ascending order">
            <span class="fa fa-sort-alpha-asc"></span>
        </a>

        <a href="{{ request()->fullUrlWithQuery(['sort' => 'desc']) }}" class="btn btn-primary @if ($sort === 'desc') active @endif" title="Display in descending order">
            <span class="fa fa-sort-alpha-desc"></span>
        </a>
    </div>
</div>

@component('shared.components.display', [
    'display' => $user->display,
    'items' => $manga_list
])
@endcomponent

<div class="row mt-3">
    <div class="col-12">
        @if (isset($manga_list))
            {{ $manga_list->render('vendor.pagination.bootstrap-4') }}
        @endif
    </div>
</div>

@section ('scripts')
    <script type="text/javascript">
        $(function () {
            const displayList = document.getElementById('display-list');
            const displayGrid = document.getElementById('display-grid');

            displayList.addEventListener('click', function (event) {
                axios.default.put("{{ URL::to('/settings/display') }}", { display: 'list' })
                    .catch(function (error) {
                        console.log(error.toJSON());
                        alert(`Received unexpected response from server. Check the console output for more information.`);
                    })
                    .then(function (response) {
                        location.reload();
                    });
            });

            displayGrid.addEventListener('click', function (event) {
                axios.default.put("{{ URL::to('/settings/display') }}", { display: 'grid' })
                    .catch(function (error) {
                        console.log(error.toJSON());
                        alert(`Received unexpected response from server. Check the console output for more information.`);
                    })
                    .then(function (response) {
                        location.reload();
                    });
            });
        });
    </script>
@endsection