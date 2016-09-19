jQuery(function ($) {
	'use strict';

	if ( !$('body').hasClass('lt-ie8') ) {

		// use chosen for form inputs
		$('select.chosen').chosen();

		// placeholders for older browsers
		$('input, textarea').placeholder();

		// fitvids for responsive videos
		$('.article-content').handleFitVids();

		// single faculty member tabs
		$('#member-tab-nav').memberTabs();

		// banner image reveals
		$('.banner-wrapper').bannerReveals();

		// share buttons
		//$('.share').coenvshare();

	}

});

/**
 * Banner image reveals
 */
$.fn.bannerReveals = function () {
	'use strict';

	return this.each( function () {

		var $container = $(this),
				$revealBtn = $('.banner-info'),
				activeClass = 'banner-revealed';



		$revealBtn.on( 'click', function ( e ) {
			e.preventDefault();
			e.stopPropagation();

			$('body').toggleClass( activeClass );
		} );

		$container.on( 'click', function () {
			if ( $('body').hasClass( activeClass ) ) {
				$('body').removeClass( activeClass );
			}
		} );

	} );
};

/**
 * Handle responsive videos
 */
$.fn.handleFitVids = function () {
	'use strict';

	$(this).fitVids();

	$('.fluid-width-video-wrapper').each( function () {
		var $this = $(this),
				maxWidth = parseFloat( $this.css('max-width') ),
				paddingTop = parseFloat( $this[0].style['padding-top'] );

		// increase padding-top relative to max-width set on this element
		var adjustment = maxWidth * ( paddingTop * 0.01 ) + '%';

		$this.css( 'padding-top', adjustment );
	} );
};

$.fn.memberTabs = function () {
	'use strict';

	var $nav = $(this),
			$tabs = $('.member-tabs'),
			activeClass = 'active-tab';

	$nav.find('a').click( function (e) {
		e.preventDefault();

		var $navItem = $(this),
				$tab = $tabs.find('.' + $(this).attr('data-tab') );

		$nav.find('.' + activeClass).removeClass( activeClass );
		$(this).parent('li').addClass( activeClass );

		$tabs.find('.' + activeClass).removeClass( activeClass );
		$tab.addClass( activeClass );
	} );
};