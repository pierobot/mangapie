<div class="row">
    <div class="col-12 mb-3">
        @include ('manga.shared.information.description')
    </div>

    <div class="col-12 mb-3">
        @include ('manga.shared.information.genres')
    </div>

    <div class="col-12 mb-3">
        @include ('manga.shared.information.associated_names')
    </div>

    <div class="col-12 mb-3">
        @include ('manga.shared.information.authors')
    </div>

    <div class="col-12 mb-3">
        @include ('manga.shared.information.artists')
    </div>

    <div class="col-12 mb-3">
        @include ('manga.shared.information.ratings')
    </div>

    {{--<div class="col-12 mb-3">--}}
        {{--@include ('manga.shared.information.actions')--}}
    {{--</div>--}}

    @admin
    <div class="col-12 mb-3">
        @include ('manga.shared.information.path')
    </div>
    @endadmin
</div>
