jQuery(function ($) {
	'use strict';

	// no feature animations for < ie8
	if ( !$('body').hasClass('lt-ie8') ) {
		$('.home #features').homeFeatures();
	}

});

$.fn.homeFeatures = function() {
	'use strict';

	var $container = $(this),
			$rsContainer = $container.find('.features-container'),
			$features = $container.find('.feature'),
			$nav = $('<div></div>'),
			offsetTop,
			rsInstance,
			navOutput;

	if ( !$rsContainer.length ) {
		return;
	}

	offsetTop = $rsContainer.offset().top;

	// init royalSlider
	$rsContainer.royalSlider({
		loop: true,
		fadeInLoadedSlide: false,
		autoHeight: true,
		navigateByClick: false,
		autoPlay: {
			enabled: true,
			pauseOnHover: true,
			stopAtAction: false,
			delay: 8000
		}
	});

	rsInstance = $rsContainer.data('royalSlider');

	$features.removeClass('loading');

	// start/stop autoplay when scrolling up or down past 
	// halfway point of feature
	$(window).on( 'scroll', $.debounce( 200, function () {
		var scrollTop = $(window).scrollTop(),
				rsContainerOffset = $rsContainer.offset().top,
				rsContainerHeight = $rsContainer.outerHeight(true);

		if ( scrollTop > rsContainerOffset + ( rsContainerHeight / 2 ) ) {
			rsInstance.stopAutoPlay();
		} else {
			rsInstance.startAutoPlay();
		}
	} ) );

	// set up nav links
	navOutput = '<ul>';

	$nav.addClass('features-nav');

	for ( var i = 0, len = rsInstance.numSlides; i < len; i++ ) {
		navOutput += '<li><a href="#"><span>' + ( i + 1 ) + '</span></a></li>';
	}

	navOutput += '</ul>';

	$nav.append( $(navOutput) );
	$nav.appendTo( $container );

	$nav.find('a').eq( rsInstance.currSlideId ).addClass( 'active' );

	$nav.find('a').on( 'click', function (e) {
		e.preventDefault();
		$rsContainer.royalSlider( 'goTo', $(this).parent().index() );
		$nav.find('a').removeClass( 'active' );
		$(this).addClass( 'active' );
	} );

	// update nav on slide change
	rsInstance.ev.on( 'rsAfterSlideChange', function () {
		$nav.find('a').removeClass( 'active' );
		$nav.find('a').eq( rsInstance.currSlideId ).addClass( 'active' );
	} );

};