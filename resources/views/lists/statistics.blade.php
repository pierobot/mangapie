@extends ('lists.layout')

@section ('list-content')
    @component ('lists.nav-pills', ['active' => 'statistics', 'user' => $user])
    @endcomponent

    @php ($listTotal = $user->completed->count() +
        $user->dropped->count() +
        $user->reading->count() +
        $user->onhold->count() +
        $user->planned->count())
    <div class="row mt-3">
        <div class="col-12 col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <span class="fa fa-circle text-success"></span>&nbsp; Completed
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: {{ ($user->completed->count() / $listTotal) * 100 }}%;">
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            {{ $user->completed->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <span class="fa fa-circle text-danger"></span>&nbsp; Dropped
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <div class="progress">
                                <div class="progress-bar bg-danger" style="width: {{ ($user->dropped->count() / $listTotal) * 100 }}%;">
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            {{ $user->dropped->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 ">
            <div class="card mb-3">
                <div class="card-header">
                    <span class="fa fa-circle text-primary"></span>&nbsp; Reading
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width: {{ ($user->reading->count() / $listTotal) * 100 }}%;">
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            {{ $user->reading->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 ">
            <div class="card mb-3">
                <div class="card-header">
                    <span class="fa fa-circle text-warning"></span>&nbsp; On hold
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <div class="progress">
                                <div class="progress-bar bg-warning" style="width: {{ ($user->onhold->count() / $listTotal) * 100 }}%;">
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            {{ $user->onhold->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 ">
            <div class="card mb-3">
                <div class="card-header">
                    <span class="fa fa-circle text-secondary"></span>&nbsp; Planned
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <div class="progress">
                                <div class="progress-bar bg-secondary" style="width: {{ ($user->planned->count() / $listTotal) * 100 }}%;">
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            {{ $user->planned->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection