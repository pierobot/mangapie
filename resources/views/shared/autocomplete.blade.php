<script src="{{ \URL::to('public/bootstrap-3-typeahead/bootstrap3-typeahead.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        baseUrl = '{{ \URL::to('/manga') }}';

        $('#autocomplete').typeahead({
            minLength: 3,
            delay: 250,
            source: function (query, process) {
                return $.getJSON('{{ \URL::to('/search/autocomplete') }}', { query : query}, function (data) {
                    return process(data);
                });
            },
            followLinkOnSelect: true,
            itemLink: function (manga) {
                return baseUrl + '/' + manga.id;
            }
        });
    });
</script>