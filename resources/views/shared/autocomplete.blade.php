<script type="text/javascript">
    $(function () {
        let baseUrl = '{{ \URL::to('/manga') }}';

        $('#searchbar').typeahead({
            delay: 250,
            fitToElement: true,
            followLinkOnSelect: true,
            minLength: 3,
            selectOnBlur: false,
            theme: "bootstrap4",

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
            fitToElement: true,
            followLinkOnSelect: true,
            minLength: 3,
            selectOnBlur: false,
            theme: "bootstrap4",

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
