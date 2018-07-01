@extends ('edit.manga.layout')

@section ('tab-content')
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        Covers
                    </div>
                </div>
                <div class="panel-body">
                    @if (empty($archives) === false)
                        @foreach ($archives as $archive_index => $archive)
                            <div class="panel-group">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <a data-toggle="collapse" href="#{{ $archive_index }}">{{ $archive->getName() }}</a>
                                        </h3>
                                    </div>
                                    <div id="{{ $archive_index }}" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="row">
                                                @for ($i = 1; $i <= 4; $i++)
                                                    <div class="col-lg-2 col-sm-4 col-xs-6 set-cover thumbnail">
                                                        {{ Form::open(['action' => 'CoverController@update'], [$id]) }}

                                                        {{ Form::hidden('manga_id', $id) }}
                                                        {{ Form::hidden('archive_id', $archive->getId()) }}
                                                        {{ Form::hidden('page', $i) }}

                                                        <div>
                                                            {{ Html::image(URL::action('CoverController@small', [
                                                                $id,
                                                                $archive->getId(),
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
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection