
@php
    $completed = ! empty($user->completed->where('manga_id', $manga->id)->first());
    $dropped = ! empty($user->dropped->where('manga_id', $manga->id)->first());
    $onHold = ! empty($user->onHold->where('manga_id', $manga->id)->first());
    $reading = ! empty($user->reading->where('manga_id', $manga->id)->first());
    $planned = ! empty($user->planned->where('manga_id', $manga->id)->first());
@endphp

<h5>Actions</h5>

<div class="row">
    <div class="col-6 col-lg-3">
        @php ($watchReference = $user->watchReferences->where('manga_id', $manga->id)->first())
        @if (empty($watchReference))
            {{ Form::open(['action' => 'WatchController@create']) }}
            {{ Form::hidden('manga_id', $manga->id) }}

            <button class="btn btn-primary form-control" type="submit" title="Begin watching this series.">
                <span class="fa fa-eye"></span>
                &nbsp;Watch
            </button>

            {{ Form::close() }}
        @else
            {{ Form::open(['action' => 'WatchController@delete', 'method' => 'delete']) }}
            {{ Form::hidden('watch_reference_id', $watchReference->id) }}

            <button class="btn btn-danger form-control" type="submit" title="Stop watching this series.">
                <span class="fa fa-eye-slash"></span>
                &nbsp;Unwatch
            </button>

            {{ Form::close() }}
        @endif
    </div>

    <div class="col-6 col-lg-3">
        @php ($favorite = $user->favorites->where('manga_id', $manga->id)->first())
        @if (empty($favorite))
            {{ Form::open(['action' => 'FavoriteController@create']) }}
            {{ Form::hidden('manga_id', $manga->id) }}

            <button class="btn btn-primary form-control" type="submit" title="Add to your favorites.">
                <span class="fa fa-heart"></span>
                &nbsp;Favorite
            </button>

            {{ Form::close() }}
        @else
            {{ Form::open(['action' => 'FavoriteController@delete', 'method' => 'delete']) }}
            {{ Form::hidden('favorite_id', $favorite->id) }}

            <button class="btn btn-danger form-control" type="submit" title="Remove from your favorites.">
                <span class="fa fa-heart-o"></span>
                &nbsp;Unfavorite
            </button>

            {{ Form::close() }}
        @endif
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-6">
        {{ Form::open(['action' => 'UserController@putStatus', 'method' => 'put']) }}
        {{ Form::hidden('manga_id', $manga->id) }}

        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Status</span>
            </div>

            <select class="form-control" name="status" title="Choose a status">
                <option @if (! $completed && ! $dropped && ! $onHold && ! $reading && ! $planned) @endif>Choose...</option>
                <option value="completed" @if ($completed) class="text-primary" selected @endif>Completed</option>
                <option value="dropped" @if ($dropped) class="text-primary" selected @endif>Dropped</option>
                <option value="on_hold" @if ($onHold) class="text-primary" selected @endif>On Hold</option>
                <option value="reading" @if ($reading) class="text-primary" selected @endif>Reading</option>
                <option value="planned" @if ($planned) class="text-primary" selected @endif>Planned</option>
            </select>

            <div class="input-group-append" title="Set status for the series">
                <button class="btn btn-primary" type="submit">
                    <span class="fa fa-check"></span>
                </button>
            </div>
        </div>

        {{ Form::close() }}
    </div>
</div>




