(function ($) {

	function birthPlaceTypeahead() {

		var birthPlaces = new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: '/api/place/search?q=%QUERY',
				wildcard: '%QUERY'
			}
		});

		$('#author_birthplace').typeahead(null, {
			name: 'birth-places',
			display: 'name',
			source: birthPlaces
		});
	}

	$(document).ready(function () {
		birthPlaceTypeahead();
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
