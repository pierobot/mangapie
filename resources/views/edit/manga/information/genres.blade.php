@extends ('edit.manga.layout')

@section ('tab-content')
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        Genres
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="alert alert-info">
                                <ul>
                                    <li><div class="text-success">Green indicates genres already set.</div></li>
                                    <li><div class="text-danger">Red indicates genres to be removed.</div></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        {{ Form::open(['action' => 'MangaEditController@putGenres', 'method' => 'put']) }}
                        {{ Form::hidden('manga_id', $manga->id) }}
                        <div class="col-xs-12">
                            @php
                                $allGenres = \App\Genre::all();
                                $genreReferences = $manga->genreReferences;
                            @endphp

                            <div class="form-group" data-toggle="buttons" style="display: inline-block;">
                                @foreach ($allGenres as $genre)
                                    @php ($alreadySet = ! empty($genreReferences->where('genre_id', $genre->id)->count()))
                                    <label class="col-xs-6 col-sm-3 col-md-2 btn {{ $alreadySet ? "btn-success active" : "btn-default" }}">
                                        <input type="checkbox"
                                               id="genres[]"
                                               name="genres[]"
                                               value="{{ $genre->id }}"
                                               @if ($alreadySet === true)
                                                   checked="checked"
                                               @endif
                                        >{{ $genre->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-xs-12">
                            {{ Form::submit('Update', ['class' => 'btn btn-success']) }}
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section ('scripts')
    <script type="text/javascript">
        $(function () {
            $('label.btn.btn-success.active').each(function (index, label) {
                $(label).click(function () {
                    $(label).toggleClass('btn-success');
                    $(label).toggleClass('btn-danger');

                    let input = $(label).children($('input[type=checkbox]'))[0];

                    input.hasAttribute('checked') === true ?
                        $(input).removeAttr('checked') :
                        $(input).attr('checked', 'checked');
                });
            })
        });
    </script>
@endsection