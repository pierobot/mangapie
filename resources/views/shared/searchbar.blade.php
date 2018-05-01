<li>
    {{ Form::open(['action' => 'SearchController@basic', 'class' => 'navbar-form navbar-inline']) }}
    {{ Form::hidden('type', 'basic') }}
    <div class="input-group">
        {{ Form::text('keywords', null, ['class' => 'form-control',
                                         'placeholder' => '...',
                                         'id' => 'autocomplete']) }}

        <span class="input-group-btn">
            <button type="submit" class="btn btn-primary btn-navbar">
                <!--
                    Without any text before the span, the button does not match the size of the input above.
                    Adding the class form-control fixes that but results in a square button.

                    Seems like just having any text resolves this so I'm using the zero-width space. *shrug*
                -->
                &#8203;<span class="glyphicon glyphicon-search"></span>
            </button>
        </span>
    </div>

    {{ Form::close() }}
</li>
