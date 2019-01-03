<h5>
    Associated Name(s)
    @if ($user->admin || $user->maintainer)
        <a href="{{ action('MangaEditController@names', [$manga]) }}">
            <span class="fa fa-edit"></span>
        </a>
    @endif
</h5>

@if (! empty($manga->associatedNameReferences))
    <div class="row">
        @foreach ($manga->associatedNameReferences as $associatedNameReference)
            <div class="col-6 col-sm-4 col-md-3 col-lg-3">
                {{ $associatedNameReference->associatedName->name }}
            </div>
        @endforeach
    </div>
@else
    Unable to find associated names.
@endif
