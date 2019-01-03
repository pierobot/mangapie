<h5>
    Author(s)
    @if ($user->admin || $user->maintainer)
        <a href="{{ action('MangaEditController@authors', [$manga]) }}">
            <span class="fa fa-edit"></span>
        </a>
    @endif
</h5>

@if (! empty($manga->authorReferences))
    <div class="row">
        @foreach ($manga->authorReferences as $authorReference)
            @php
                $author = $authorReference->author;
            @endphp
            <div class="col-6 col-sm-4 col-md-3 col-lg-3">
                <a href="{{ \URL::action('PersonController@index', [$author]) }}">
                    {{ $author->name }}
                </a>
            </div>
        @endforeach
    </div>
@else
    Unable to find authors.
@endif
