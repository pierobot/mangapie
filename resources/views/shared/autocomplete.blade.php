<script type="text/javascript">
    $(function () {
        let baseUrl = '{{ \URL::to('/manga') }}';

        $('#searchbar').typeahead({
            delay: 250,
            followLinkOnSelect: true,
            minLength: 3,
            selectOnBlur: false,
            theme: "bootstrap4",

            /* Required in order for results not to be ignored. */
            matcher: function (data) {
                return true;
            },

            itemLink: function (manga) {
                return baseUrl + '/' + manga.id;
            },

            source: function (query, process) {
                return $.getJSON('{{ \URL::to('/search/autocomplete') }}', { query : query}, function (data) {
                    return process(data);
                });
            }
        });

        $('#searchbar-small').typeahead({
            delay: 250,
            followLinkOnSelect: true,
            minLength: 3,
            selectOnBlur: false,
            theme: "bootstrap4",

            matcher: function (data) {
                return true;
            },

            itemLink: function (manga) {
                return baseUrl + '/' + manga.id;
            },

            source: function (query, process) {
                return $.getJSON('{{ \URL::to('/search/autocomplete') }}', { query : query}, function (data) {
                    return process(data);
                });
            }
        });
    });
</script>
