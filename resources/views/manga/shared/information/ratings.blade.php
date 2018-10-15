<h5>Ratings</h5>

<div class="row">
    <div class="col-12">
        <div class="row">
            <div class="col-6 col-sm-4">
                <strong>Average</strong>
                @if ($manga->votes->count() > 0)
                    @php
                        $averageRating = \App\Rating::average($manga);
                        if ($averageRating !== false)
                            $averageRating = round($averageRating);

                        $userVote = $user->votes->where('manga_id', $manga->id)->first();
                    @endphp

                    <p>{{ $averageRating }}</p>
                @else
                    <p>N/A</p>
                @endif
            </div>

            <div class="col-6 col-sm-4">
                <strong>Yours</strong>
                @if (! empty($userVote))
                    <p class="text-success">{{ $userVote->rating }}</p>
                @else
                    <p>N/A</p>
                @endif
            </div>

            @admin
                <div class="col-6 col-sm-4">
                    <strong title="Lower bound Wilson score">Wilson&nbsp;<a href="https://www.evanmiller.org/how-not-to-sort-by-average-rating.html">?</a></strong>
                    @if ($manga->votes->count() > 0)
                        @php
                            $rating = \App\Rating::get($manga);
                            if ($rating !== false)
                                $rating = round($rating, 2);
                        @endphp
                        <p title="Lower bound Wilson score">{{$rating }}</p>
                    @else
                        <p title="Lower bound Wilson score">N/A</p>
                    @endif
                </div>
            @endadmin
        </div>
        <div class="row">
            <div class="col-12">
                <strong>Vote</strong>

                <div class="row">
                    <div class="col-12">
                        @if (empty($userVote))
                            {{ Form::open(['action' => 'VoteController@put', 'method' => 'put', 'style' => 'display:inline-block;']) }}
                            {{ Form::hidden('manga_id', $manga->id) }}
                        @else
                            {{ Form::open(['action' => 'VoteController@patch', 'method' => 'patch', 'style' => 'display:inline-block;']) }}
                            {{ Form::hidden('vote_id', $userVote->id) }}
                        @endif
                        <div class="input-group">
                            <select class="custom-select" name="rating">
                                @for ($i = 100; $i > 0; $i--)
                                    <option value="{{ $i }}"
                                            @if (! empty($userVote) && ($userVote->rating === $i))
                                                selected="selected"
                                            @elseif ($i === 70)
                                                selected="selected"
                                            @endif
                                    >
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-primary" type="submit">
                                    <span class="fa fa-check"></span>
                                </button>
                            </div>
                        </div>
                        {{ Form::close() }}

                        @if (! empty($userVote))
                            {{ Form::open(['action' => 'VoteController@delete', 'method' => 'delete', 'style' => 'display:inline-block']) }}
                            {{ Form::hidden('vote_id', $userVote->id) }}
                            <button type="submit" class="btn  btn-danger"><span class="fa fa-times"></span>&#8203;</button>
                            {{ Form::close() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
