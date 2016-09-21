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
