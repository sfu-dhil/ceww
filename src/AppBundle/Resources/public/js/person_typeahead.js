(function ($) {
    function setupTypeahead($input, $hidden) {
        var bloodhound = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: search_api + '?q=%QUERY',
                wildcard: '%QUERY'
            }
        });

        $input.typeahead(null, {
            display: 'name',
            source: bloodhound,
            templates: {
                empty: "<div class='typeahead-result'>No results</div>",
                suggestion: Handlebars.compile('<div class="typeahead-result"><strong>{{name}}</strong></div>'),
            }
        });

        $input.bind('typeahead:select', function (ev, suggestion) {
            $hidden.val(suggestion.id);
        });
    }

    $(document).ready(function () {
        setupTypeahead($('#author_birthplace'), $('#author_birthplace_id'));
    });

})(jQuery);
