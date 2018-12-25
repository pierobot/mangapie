<div class="row">
    <div class="col-12">
        <ul class="nav nav-pills justify-content-center align-content-center">
            <li class="nav-item">
                <a class="nav-link @if ($active === 'statistics') active @endif" href="{{ URL::action('UserController@statistics', [$user]) }}">Statistics</a>
            </li>

            <li class="nav-item">
                <a class="nav-link @if ($active === 'completed') active @endif" href="{{ URL::action('UserController@completed', [$user]) }}">Completed</a>
            </li>

            <li class="nav-item">
                <a class="nav-link @if ($active === 'dropped') active @endif" href="{{ URL::action('UserController@dropped', [$user]) }}">Dropped</a>
            </li>

            <li class="nav-item">
                <a class="nav-link @if ($active === 'reading') active @endif" href="{{ URL::action('UserController@reading', [$user]) }}">Reading</a>
            </li>

            <li class="nav-item">
                <a class="nav-link @if ($active === 'onhold') active @endif" href="{{ URL::action('UserController@onHold', [$user]) }}">On hold</a>
            </li>

            <li class="nav-item">
                <a class="nav-link @if ($active === 'planned') active @endif" href="{{ URL::action('UserController@planned', [$user]) }}">Planned</a>
            </li>
        </ul>
    </div>
</div>
