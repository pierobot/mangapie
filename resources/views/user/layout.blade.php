@extends ('layout')

@section ('content')
    <div class="d-none d-md-flex">
        <div class="row w-100">
            <div class="col-md-4 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <img class="img-fluid" src="{{ URL::action('AvatarController@index', [$user]) }}">

                        <h5 class="card-title"><strong>{{ $user->name }}</strong></h5>

                        <p class="card-text">
                            @if (! empty($user->about))
                                {!! nl2br(e($user->about)) !!}
                            @else
                                Nothing but default text here.
                            @endif
                        </p>

                        @if (! empty($user->getLastSeen()))
                            <p class="text-muted mt-3">Last seen {{ \Carbon\Carbon::createFromTimeString($user->getLastSeen())->diffForHumans() }}</p>
                        @endif

                        @if (! empty($user->getJoined()))
                            <p class="text-muted">Joined {{ \Carbon\Carbon::createFromTimeString($user->getJoined())->diffForHumans() }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-8 col-lg-9">
                @yield ('profile-content')
            </div>
        </div>
    </div>

    <div class="d-flex d-md-none flex-wrap">
        <div class="card w-100">
            <div class="card-body">
                <div class="media">
                    <img src="{{ URL::action('AvatarController@index', [$user]) }}" width="80">

                    <div class="media-body ml-2">
                        <h5 class="card-title"><strong>{{ $user->name }}</strong></h5>

                        <p class="card-text">
                            @if (! empty($user->about))
                                {!! nl2br(e($user->about)) !!}
                            @else
                                Nothing but default text here.
                            @endif
                        </p>
                    </div>
                </div>

                @if (! empty($user->getLastSeen()))
                    <p class="text-muted mt-3">Last seen {{ \Carbon\Carbon::createFromTimeString($user->getLastSeen())->diffForHumans() }}</p>
                @endif

                @if (! empty($user->getJoined()))
                    <p class="text-muted">Joined {{ \Carbon\Carbon::createFromTimeString($user->getJoined())->diffForHumans() }}</p>
                @endif
            </div>
        </div>

        @yield ('profile-content')
    </div>
@endsection
