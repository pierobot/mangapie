<h5>
    Associated Name(s)
    @if ($user->hasRole('Administrator') || $user->hasRole('Editor'))
        <a href="{{ action('MangaEditController@names', [$manga]) }}">
            <span class="fa fa-edit"></span>
        </a>
    @endif
</h5>

<div class="row">
    @if ($manga->associatedNames->count())
        @php
            $filesystemName = $manga->name;
            // Reject the filesystem name from the associated names as do not need it twice
            $associatedNames = $manga->associatedNames->reject(function (\App\AssociatedName $associatedName) use ($filesystemName) {
                return $associatedName->name === $filesystemName;
            });

            // Add the "official" name if it differs from the filesystem name
            if (! empty($manga->mu_name) && $manga->mu_name !== $manga->name) {
                $muName = new \App\AssociatedName(['name' => $manga->mu_name]);
                $associatedNames []= $muName;
            }

            $associatedNames = $associatedNames->sortBy('name');
        @endphp

        @foreach ($associatedNames as $associatedName)
            <div class="col-6 col-sm-4 col-md-3 col-lg-3">
                {{ $associatedName->name }}
            </div>
        @endforeach
    @else
        <div class="col">
            Unable to find associated names.
        </div>
    @endif
</div>
