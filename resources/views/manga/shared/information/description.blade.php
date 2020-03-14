<h5>
    Description
    @if ($user->hasRole('Administrator') || $user->hasRole('Editor'))
        <a href="{{ action('MangaEditController@description', [$manga]) }}">
            <span class="fa fa-edit"></span>
        </a>
    @endif
</h5>

@if (! empty($manga->description))
    {!! nl2br(e($manga->description)) !!}
@else
    Unable to find description.
@endif
