@auth

{{ Form::open(['action' => 'SearchController@basic', 'class' => 'navbar-form navbar-left']) }}
{{ Form::hidden('type', 'basic') }}

<div class="form-group">
    <div class="input-group">
        {{ Form::text('keywords', null, ['class' => 'form-control',
                                         'placeholder' => 'Quick search',
                                         'id' => 'autocomplete',
                                         'autocomplete' => 'off']) }}

        <div class="input-group-btn">
            <button type="submit" class="btn btn-primary">
                <!--
                    Without any text before the span, the button does not match the size of the input above.
                    Adding the class form-control fixes that but results in a square button.

                    Seems like just having any text resolves this so I'm using the zero-width space. *shrug*
                -->
                &#8203;<span class="glyphicon glyphicon-search"></span>
            </button>
        </div>
    </div>
</div>

{{ Form::close() }}

@endauth
