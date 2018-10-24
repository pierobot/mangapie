<h5>Description</h5>

@if (! empty($manga->description))
    {!! nl2br(e($manga->description)) !!}
@else
    Unable to find description.
@endif
