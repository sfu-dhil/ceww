(function ($) {

    function setupTypeahead($input, $hidden)
    {
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
        setupTypeahead($('#author_deathplace'), $('#author_deathplace_id'));
    });

})(jQuery);

(function ($) {

    $(document).ready(function () {
        $("*[data-confirm]").each(function () {
            var $this = $(this);
            $this.click(function () {
                return window.confirm($this.data('confirm'));
            });
        });
    });

})(jQuery);

(function ($, window) {

    $(document).ready(function () {

        $(window).bind('beforeunload', function (e) {
            var clean = true;
            $('form').each(function () {
                var $form = $(this);
                if ($form.data('dirty')) {
                    clean = false;
                }
            });
            if (!clean) {
                var message = 'You have unsaved changes.';
                e.returnValue = message;
                return message;
            }
        });

        $('form').each(function () {
            var $form = $(this);
            $form.data('dirty', false);
            $form.on('change', function () {
                $form.data('dirty', true);
            });
            $form.on('submit', function () {
                $(window).unbind('beforeunload');
            });
        });
    });

})(jQuery, window);
