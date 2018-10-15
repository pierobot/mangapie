<h5>Associated Name(s)</h5>

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
