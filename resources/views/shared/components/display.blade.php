@if (isset($items))
    @if ($display === 'list')
        <div class="row justify-content-center">
            <div class="col-12">
                <table class="table table-borderless table-striped">
                    <thead>
                        <tr>
                            <th scope="col">
                                <span class="fa fa-picture-o d-flex d-md-none"></span>
                                <span class="d-none d-md-flex">Cover</span>
                            </th>
                            <th scope="col">
                                <span class="fa fa-book d-flex d-md-none"></span>
                                <span class="d-none d-md-flex">Name</span>
                            </th>
                            <th scope="col">
                                <span class="fa fa-user d-flex d-md-none"></span>
                                <span class="d-none d-md-flex">Author(s)</span>
                            </th>
                            <th scope="col">
                                <span class="fa fa-heart  d-flex d-md-none"></span>
                                <span class="d-none d-md-flex">Favorites</span>
                            </th>
                            <th scope="col">
                                <span class="fa fa-star  d-flex d-md-none"></span>
                                <span class="d-none d-md-flex">Rating</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $series)
                            <tr class="d-table-row">
                                <td class="col-4 col-md-1">
                                    <a href="{{ URL::action('MangaController@show', [$series]) }}">
                                        <img class="img-fluid" src="{{ URL::action('CoverController@smallDefault', [$series]) }}" alt="cover">
                                    </a>
                                </td>
                                <td class="col">
                                    <strong>
                                        <a href="{{ URL::action('MangaController@show', [$series]) }}">{{ $series->name }}</a>
                                    </strong>
                                </td>
                                <td class="col">
                                    @foreach ($series->authors as $author)
                                        <small>
                                            <a href="{{ URL::action('PersonController@show', [$author]) }}">
                                                {{ $author->name }}
                                            </a>
                                        </small>
                                    @endforeach
                                </td>
                                <td class="col">
                                    <small>{{ $series->favorites->count() }}</small>
                                </td>
                                <td class="col">
                                    @php ($averageRating = \App\Rating::average($series))
                                    <small>{{ $averageRating == false ? 0 : $averageRating }}</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif ($display === 'grid')
        <div class="row">
            @foreach ($items as $series)
                <div class="col-6 col-sm-3 col-xl-2">
                    <div class="card mb-3">
                        <a href="{{ URL::action('MangaController@show', [$series]) }}">
                            <img class="card-img-top" src="{{ URL::action('CoverController@smallDefault', [$series]) }}" alt="cover">
                        </a>

                        <div class="card-body bg-dark">
                            <div class="card-title text-truncate" title="{{ $series->name }}">
                                <strong>
                                    <a class="card-link" href="{{ URL::action('MangaController@show', [$series]) }}">{{ $series->name }}</a>
                                </strong>
                            </div>
                            <div class="card-subtitle">
                                @foreach ($series->authors as $author)
                                    <small>
                                        <a class="card-link" href="{{ URL::action('PersonController@show', [$author]) }}">
                                            {{ $author->name }}
                                        </a>
                                    </small>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endif