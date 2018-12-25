<h5>Ratings</h5>

@php
    $voteCount = $manga->votes->count();
    if ($voteCount > 0) {
        $averageRating = \App\Rating::average($manga);
        if ($averageRating !== false)
            $averageRating = floor(($averageRating) * 2) / 2;

        $userVote = $user->votes->where('manga_id', $manga->id)->first();
    }
@endphp

<div class="row">
    <div class="col-6 col-lg-4">
        <label>Overall ({{ $voteCount }})</label>
        <select class="rating overall-rating">
            <option hidden="" value="0">0</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
    </div>

    <div class="col-6 col-lg-4">
        <label>Yours</label>

        @if (isset($userVote) && ! empty($userVote))
            {{ Form::open(['action' => 'VoteController@delete', 'method' => 'delete', 'class' => 'form-inline d-inline']) }}
            {{ Form::hidden('vote_id', $userVote->id) }}
            <button class="btn bg-transparent border-0 p-0 text-danger" title="Delete your vote">
                <span class="fa fa-times"></span>
            </button>
            {{ Form::close() }}
        @endif

        <select class="rating your-rating">
            <option hidden="" value="0">0</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
    </div>
</div>

@section ('scripts')
    <script type="text/javascript">
        $(function() {
            $('select.overall-rating').each(function (k, v) {
                $(v).barrating('show', {
                    theme: 'fontawesome-stars-o',
                    allowEmpty: true,
                    emptyValue: 0,
                    readonly: true,
                    initialRating: @if (isset($averageRating) && ! empty($averageRating)) {{ $averageRating }} @else 0 @endif,
                });
            });

            $('select.your-rating').each(function (k, v) {
                $(v).barrating('show', {
                    theme: 'fontawesome-stars-o',
                    allowEmpty: true,
                    emptyValue: 0,
                    initialRating: @if (isset($userVote) && ! empty($userVote)) {{ $userVote->rating }} @else 0 @endif,
                    onSelect: function (value, text, event) {
                        let confirmed = confirm(`You are about to cast a vote with a rating of ${value} star(s).\nIs this ok?`);
                        if (confirmed) {
                            axios.put('{{ config('app.url') }}vote', { _method: "PUT", manga_id: "{{ $manga->id }}", rating: value })
                                .catch(function () {
                                    alert('Unable to cast vote.');
                                });

                            // refresh the page to update the overall and ratings in smaller/larger displays
                            window.location = window.location;
                        } else {
                            $(v).barrating('clear');
                        }
                    }
                });
            });
        });
    </script>
@endsection
