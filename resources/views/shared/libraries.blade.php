<li class="dropdown">
    <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="glyphicon glyphicon-book"></span>&nbsp;Libraries&nbsp;<span class="glyphicon glyphicon-chevron-down white"></span>
    </a>
    <ul class="dropdown-menu" style="color: black;">
        @if (isset($libraries))
            @foreach ($libraries as $library)
                <li>
                    <a href="{{ URL::action('HomeController@library', ['id' => $library->getId()]) }}">{{ $library->getName() }}</a>
                <li>
            @endforeach
        @endif
    </ul>
</li>