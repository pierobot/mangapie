@extends ('edit.manga.layout')

@section ('side-top-menu')
    @component ('edit.manga.components.side-top-menu', [
        'manga' => $manga,
        'active' => 'Description',
        'items' => [
            ['title' => 'Mangaupdates', 'icon' => 'database', 'action' => 'MangaEditController@mangaupdates'],
            ['title' => 'Name(s)', 'icon' => 'globe-americas', 'action' => 'MangaEditController@names'],
            ['title' => 'Description', 'icon' => 'file-text', 'action' => 'MangaEditController@description'],
            ['title' => 'Author(s)', 'icon' => 'pencil', 'action' => 'MangaEditController@authors'],
            ['title' => 'Artist(s)', 'icon' => 'brush', 'action' => 'MangaEditController@artists'],
            ['title' => 'Genre(s)', 'icon' => 'tags', 'action' => 'MangaEditController@genres'],
            ['title' => 'Cover', 'icon' => 'file-image', 'action' => 'MangaEditController@covers'],
            ['title' => 'Type', 'icon' => 'list', 'action' => 'MangaEditController@type'],
            ['title' => 'Year', 'icon' => 'calendar', 'action' => 'MangaEditController@year']
        ]
    ])
    @endcomponent
@endsection

{{-- TODO: Fix this arrrrrgh --}}
@section ('tab-content')
    <div class="card">
        <div class="card-header">
            Description
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    {{ Form::open(['action' => 'MangaEditController@patchDescription', 'method' => 'patch']) }}
                    {{ Form::hidden('manga_id', $manga->id) }}

                    {{ Form::textarea('description', ! empty($manga->description) ? $manga->description : '', ['class' => 'form-control', 'placeholder' => 'Enter description...']) }}
                    {{ Form::submit('Set', ['class' => 'btn btn-primary form-control mt-2']) }}

                    {{ Form::close() }}
                </div>

                <div class="col-12 mt-0">
                    @if (! empty($manga->description))
                        {{ Form::open(['action' => 'MangaEditController@deleteDescription', 'method' => 'delete']) }}
                        {{ Form::hidden('manga_id', $manga->id) }}
                        {{ Form::submit('Delete', ['class' => 'btn btn-danger form-control']) }}
                        {{ Form::close() }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{--<div class="tab-content">--}}
        {{--<div class="tab-pane active" id="Information-description">--}}
            {{--<div class="panel panel-default">--}}
                {{--<div class="panel-heading">--}}
                    {{--<div class="panel-title">--}}
                        {{--Description--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="panel-body">--}}
                    {{--<div class="row">--}}
                        {{--<div class="col-xs-12 col-md-6">--}}
                            {{--<h4>New</h4>--}}
                            {{--<hr>--}}
                            {{--{{ Form::open(['action' => 'MangaEditController@patchDescription', 'method' => 'patch']) }}--}}
                            {{--{{ Form::hidden('manga_id', $manga->id) }}--}}
                            {{--{{ Form::textarea('description', null, ['class' => 'form-control',--}}
                            {{--'placeholder' => 'Enter description...']) }}--}}
                            {{--<br>--}}
                            {{--{{ Form::submit('Save', ['class' => 'btn btn-success']) }}--}}
                            {{--{{ Form::close() }}--}}
                        {{--</div>--}}
                        {{--<div class="col-xs-12 col-md-6">--}}
                            {{--<h4>Current</h4>--}}
                            {{--<hr>--}}
                            {{--@if (isset($description))--}}
                                {{--{{ $description }}--}}
                                {{--{{ Form::open(['action' => 'MangaEditController@deleteDescription', 'method' => 'delete']) }}--}}
                                {{--{{ Form::hidden('manga_id', $manga->id) }}--}}
                                {{--<br>--}}
                                {{--{{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}--}}
                                {{--{{ Form::close() }}--}}
                            {{--@endif--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
@endsection