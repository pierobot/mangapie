@php($currentNavPill = 'profile')

@section ('title')
    {{ $user->getName() }}&apos;s Profile :: Mangapie
@endsection

@extends ('user.layout')

@section ('tab-content')
    <div class="tab-pane">
        <div class="row">
            <div class="col-xs-12">
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-xs-3">
                                <b>Last Seen</b>
                            </div>
                            <div class="col-xs-9">
                                @if (! empty($user->getLastSeen()))
                                    {{ \Carbon\Carbon::createFromTimeString($user->getLastSeen())->diffForHumans() }}
                                @else
                                    This user transcends time.
                                @endif
                            </div>
                        </div>
                    </li>

                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-xs-3">
                                <b>Joined</b>
                            </div>
                            <div class="col-xs-9">
                                @if (! empty($user->getJoined()))
                                    {{ \Carbon\Carbon::createFromTimeString($user->getJoined())->diffForHumans() }}
                                @else
                                    This user transcends time.
                                @endif
                            </div>
                        </div>
                    </li>

                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-xs-3">
                                <b>About</b>
                            </div>
                            <div class="col-xs-9">
                                @if (! empty($user->getAbout()))
                                    {!! nl2br(e($user->getAbout())) !!}
                                @else
                                    Nothing but default text here.
                                @endif
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
