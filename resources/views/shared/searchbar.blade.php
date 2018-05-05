<li>
    {{ Form::open(['action' => 'SearchController@basic', 'class' => 'navbar-form']) }}
    {{ Form::hidden('type', 'basic') }}
    <div class="input-group">
        <div class="form-group">
            {{ Form::text('keywords', null, ['class' => 'form-control',
                                             'placeholder' => 'Quick search',
                                             'id' => 'autocomplete']) }}
        </div>
        <div class="input-group-btn">
            <span type="submit" class="btn btn-primary">
                <!--
                    Without any text before the span, the button does not match the size of the input above.
                    Adding the class form-control fixes that but results in a square button.

                    Seems like just having any text resolves this so I'm using the zero-width space. *shrug*
                -->
                &#8203;<span class="glyphicon glyphicon-search"></span>
            </span>
        </div>
    </div>
    {{ Form::close() }}
</li>
