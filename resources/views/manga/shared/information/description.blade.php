<h5>
    Description
    @if ($user->admin || $user->maintainer)
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
