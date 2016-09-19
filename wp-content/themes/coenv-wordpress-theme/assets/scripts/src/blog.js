jQuery(function ($) {
	'use strict';

	// handle blog header form
	$('#blog-header').blogHeader();

});

$.fn.blogHeader = function () {
	'use strict';

	var $header = $(this),
			$selectCategory = $header.find('.select-category select'),
			$selectMonth = $header.find('.select-month select');

	// use chosen for selects
	$selectCategory.chosen();
	$selectMonth.chosen();

	$selectCategory.on( 'change', function () {
		var term_id = $(this).val(),
				url = $(this).parent('div').attr('data-url');
		window.location.href = url + term_id;
	} );

	$selectMonth.on( 'change', function () {
		var url = $(this).val();
		window.location.href = url;
	} );
};