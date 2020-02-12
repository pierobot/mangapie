@extends ('lists.layout')

@section ('title')
    {{ $user->name }}'s Dropped &colon;&colon; Mangapie
@endsection

@section ('list-content')
    @component ('lists.nav-pills', ['active' => 'dropped', 'user' => $user])
    @endcomponent

    <div class="row">
        @php ($droppedList = $user->dropped->load('manga'))
        @foreach ($droppedList as $dropped)
            @php ($manga = $dropped->manga)
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card w-100 mt-3">
                    <div class="card-body">
                        <div class="media">
                            <a href="{{ URL::action('MangaController@show', [$manga]) }}">
                                <img width="90" class="rounded mr-3" src="{{ URL::action('CoverController@smallDefault', [$manga]) }}">
                            </a>

                            <div class="media-body">
                                <a href="{{ URL::action('MangaController@show', [$manga]) }}">
                                    <h5>{{ $manga->name }}</h5>
                                </a>

                                <span class="text-muted">{{ $dropped->updated_at->diffForHumans() }}</span>
                                <br>
                                @php ($userVote = $user->votes->where('manga_id', $manga->id)->first())
                                @if (! empty($userVote))
                                    @for ($i = 0; $i < $userVote->rating; $i++)
                                        <span class="fa fa-star text-warning"></span>
                                    @endfor
                                @else
                                    <span class="text-muted">No rating</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
