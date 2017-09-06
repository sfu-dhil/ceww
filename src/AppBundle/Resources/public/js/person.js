(function ($) {
    function setupTypeahead($input, $hidden) {
        var bloodhound = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: person_typeahead_uri + '?q=%QUERY',
                wildcard: '%QUERY'
            }
        });

        $input.typeahead(null, {
            display: 'name',
            source: bloodhound,
            limit: 10,
            templates: {
                pending: "<div class='typeahead-result'>Searching</div>",
                notFound: "<div class='typeahead-result'>No results</div>",
                suggestion: Handlebars.compile('<div class="typeahead-result"><strong>{{name}}</strong></div>'),
            }
        });

        $input.bind('typeahead:select', function (ev, suggestion) {
            $hidden.val(suggestion.id);
        });
    }

    $(document).ready(function () {
        setupTypeahead($('#person_birthPlace'), $('#person_birthPlace_id'));
    });

})(jQuery);
