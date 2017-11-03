@extends ('layout')

@section ('title')
    Index
@endsection

@section ('custom_navbar_right')
    <li>
    {{ Form::open(['action' => 'SearchController@search', 'class' => 'navbar-form form-inline']) }}

        <div class="form-group">
        {{ Form::text('query', null, ['class' => 'form-control',
                                      'placeholder' => '...',
                                      'id' => 'autocomplete']) }}
        </div>

        {{ Form::submit('Search', ['class' => 'btn btn-primary btn-navbar']) }}

    {{ Form::close() }}
    </li>

    <li class="dropdown">
        <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="glyphicon glyphicon-book"></span>&nbsp;Libraries&nbsp;<span class="glyphicon glyphicon-chevron-down white"></span>
        </a>
        <ul class="dropdown-menu" style="color: black;">
            @foreach ($libraries as $library)
                <li>
                   <a href="{{ URL::action('MangaController@library', ['id' => $library->getId()]) }}">{{ $library->getName() }}</a>
                <li>
            @endforeach
        </ul>
    </li>
@endsection

@section ('content')
    @include ('shared.errors')
    @include ('shared.index')
@endsection

@section ('scripts')
    @include ('shared.autocomplete')
@endsection