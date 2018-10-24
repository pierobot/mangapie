<a class="btn btn-primary mr-3" href="{{ URL::action('MangaEditController@index', [$manga]) }}">
    <span class="fa fa-pencil"></span>&#8203;
</a>

@php ($watchReference = $user->watchReferences->where('manga_id', $manga->id)->first())
@if (! $watchReference)
    {{ Form::open(['action' => 'WatchController@create', 'class' => 'mb-0 mr-3']) }}
    {{ Form::hidden('manga_id', $manga->id) }}
    <button class="btn btn-primary form-control" type="submit" title="Watch">
        <span class="fa fa-eye"></span>
    </button>
    {{ Form::close() }}
@else
    {{ Form::open(['action' => 'WatchController@delete', 'method' => 'delete', 'class' => 'mb-0 mr-3']) }}
    {{ Form::hidden('watch_reference_id', $watchReference->id) }}
    <button class="btn btn-success form-control" type="submit" data-subscribed="yes" title="Unwatch">
        <span class="fa fa-eye"></span>
    </button>
    {{ Form::close() }}
@endif

@php ($favorite = $user->favorites->where('manga_id', $manga->id)->first())
@if (! $favorite)
    {{ Form::open(['action' => 'FavoriteController@create', 'class' => 'mb-0 mr-3']) }}
    {{ Form::hidden('manga_id', $manga->id) }}
    <button class="btn btn-primary form-control" type="submit" title="Favorite">
        <span class="fa fa-heart"></span>
    </button>
    {{ Form::close() }}
@else
    {{ Form::open(['action' => 'FavoriteController@delete', 'method' => 'delete', 'class' => 'mb-0 mr-3']) }}
    {{ Form::hidden('favorite_id', $favorite->id) }}
    <button class="btn btn-success form-control" type="submit" data-favorited="yes" title="Unfavorite">
        <span class="fa fa-heart"></span>
    </button>
    {{ Form::close() }}
@endif
