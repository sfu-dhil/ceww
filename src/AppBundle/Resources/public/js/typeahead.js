(function ($) {
    function setupTypeahead(opts) {

        var bloodhound = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: opts.input.data('url') + '?q=%QUERY',
                wildcard: '%QUERY'
            }
        });

        opts.input.typeahead(null, {
            display: 'name',
            source: bloodhound,
            limit: 10,
            templates: {
                pending: "<div class='typeahead-result'>Searching</div>",
                notFound: "<div class='typeahead-result'>No results</div>",
                suggestion: Handlebars.compile(opts.input.data('template')),
            }
        });

        opts.input.bind('typeahead:select', function (ev, suggestion) {
            opts.target.val(suggestion.id);
        });
    }

    $(document).ready(function () {
        $('input.typeahead').each(function () {
            var $self = $(this);
            var formName = $self.closest('form').attr('name');
            var $target = $('#' + formName + '_' + $self.data('target'));
            setupTypeahead({
                input: $self,
                target: $target
            });
        });
    });
})(jQuery);
