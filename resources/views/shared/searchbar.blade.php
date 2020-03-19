@auth
    {{ Form::open(['action' => 'SearchController@postBasic', 'class' => 'form-inline mb-0']) }}

    <div class="form-group mb-0 w-100">
        <div class="input-group">
            {{ Form::text('keywords', null, ['class' => 'form-control',
                                             'placeholder' => 'Quick search',
                                             'id' => $searchbarId,
                                             'autocomplete' => 'off']) }}

            <div class="input-group-append">
                <button class="btn btn-primary form-control" type="submit">
                    <span class="fa fa-search"></span>
                </button>
            </div>
        </div>
    </div>

    {{ Form::close() }}
@endauth
