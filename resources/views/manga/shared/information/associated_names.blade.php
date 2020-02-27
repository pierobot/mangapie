<h5>
    Associated Name(s)
    @if ($user->hasRole('Administrator') || $user->hasRole('Editor'))
        <a href="{{ action('MangaEditController@names', [$manga]) }}">
            <span class="fa fa-edit"></span>
        </a>
    @endif
</h5>

@if ($manga->associatedNames->count())
    <div class="row">
        @php($associatedNames = $manga->associatedNames)

        @foreach ($associatedNames as $associatedName)
            <div class="col-6 col-sm-4 col-md-3 col-lg-3">
                {{ $associatedName->name }}
            </div>
        @endforeach
    </div>
@else
    Unable to find associated names.
@endif
