<script src="{{ \URL::to('public/bootstrap-3-typeahead/bootstrap3-typeahead.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        $('#autocomplete').typeahead({
            minLength: 3,
            delay: 250,
            source: function (query, process) {
                return $.getJSON('{{ \URL::to('/search/autocomplete') }}', { query : query}, function (data) {
                    return process(data);
                });
            }
        });
    });
</script>