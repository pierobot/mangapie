@auth
    {{ Form::open(['action' => 'SearchController@basic', 'class' => 'form-inline mb-0']) }}
    {{ Form::hidden('type', 'basic') }}

    <div class="form-group mb-0">
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
