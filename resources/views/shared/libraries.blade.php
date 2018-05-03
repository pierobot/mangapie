<li class="dropdown">
    <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="glyphicon glyphicon-book"></span>&nbsp;Libraries&nbsp;<span class="glyphicon glyphicon-chevron-down white"></span>
    </a>
    <ul class="dropdown-menu" style="color: black;">
        @admin
            @php
                $libraries = App\Library::all();
            @endphp
        @else
            @php
                $libraryIds = App\LibraryPrivilege::getIds();
                $libraries = App\Library::whereIn('id', $libraryIds)->get();
            @endphp
        @endadmin

        @foreach ($libraries as $library)
            <li>
                <a href="{{ URL::action('HomeController@library', ['id' => $library->getId()]) }}">{{ $library->getName() }}</a>
            <li>
        @endforeach
    </ul>
</li>