@extends ('layout')

@section ('title')
    Edit &middot; {{ $name }}
@endsection

@section ('stylesheets')
    <link href="{{ URL::to('/public/css/manga/information.css') }}" rel="stylesheet">
@endsection

@section ('custom_navbar_right')

    <li>
        {{ Form::open(['action' => 'SearchController@search', 'class' => 'navbar-form form-inline']) }}

        {{ Form::hidden('type', 'basic') }}

        <div class="form-group">
            {{ Form::text('query', null, ['class' => 'form-control', 'placeholder' => '...']) }}
        </div>

        {{ Form::submit('Search', ['class' => 'btn btn-primary btn-navbar']) }}

        {{ Form::close() }}
    </li>

    <li class="clickable navbar-link">
        <a href="{{ URL::action('MangaInformationController@index', [$id]) }}"><span class="glyphicon glyphicon-book white"></span> Information</a>
    </li>

@endsection

@section ('content')

    <h2 class="text-center">
        <b>Edit &middot; {{ $name }}</b>
    </h2>

    <div class="panel panel-default">
        <div class="panel-body">
            <h4>Mangaupdates</h4>

            {{ Form::open(['action' => 'MangaInformationController@update']) }}

            {{ Form::hidden('id', $id) }}

            <div class="input-group">

                {{ Form::label('id:', '', ['for' => 'mu_id']) }}
                @if (isset($mu_id))
                    {{ Form::text('mu_id', '', ['class' => 'form-control', 'placeholder' => $mu_id]) }}
                @else
                    {{ Form::text('mu_id', '', ['class' => 'form-control']) }}
                @endif

            </div>
            <br>
            {{ Form::submit('Update', ['class' => 'btn btn-success', 'id' => 'action', 'name' => 'action', 'value' => 'update']) }}

            {{ Form::close() }}

            <hr>

            <h4><span class="glyphicon glyphicon-picture"></span>&nbsp; Cover</h4>

            @if (empty($archives) === false)

                @foreach ($archives as $archive_index => $archive)
                    <div class="panel-group">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    <a data-toggle="collapse" href="#{{ $archive_index }}">{{ $archive['name'] }}</a>
                                </h3>
                            </div>
                            <div id="{{ $archive_index }}" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <div class="row">
                                        @for ($i = 1; $i <= 4; $i++)
                                            <div class="col-lg-2 col-sm-4 col-xs-6 set-cover thumbnail">
                                                {{ Form::open(['action' => 'ThumbnailController@update'], [$id]) }}

                                                {{ Form::hidden('id', $id) }}
                                                {{ Form::hidden('archive_name', $archive['name']) }}
                                                {{ Form::hidden('page', $i) }}

                                                <div>
                                                    {{ Html::image(URL::action('ThumbnailController@small', [
                                                                       $id,
                                                                       rawurlencode($archive['name']),
                                                                       $i]), null, ['class' => 'center-block'])
                                                    }}
                                                </div>

                                                <h4>
                                                    {{ Form::submit('Set', ['class' => 'btn btn-success center-block']) }}
                                                </h4>

                                                {{ Form::close() }}
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                @endforeach
            @else

            @endif

            <hr>
        </div>
    </div>

@endsection
