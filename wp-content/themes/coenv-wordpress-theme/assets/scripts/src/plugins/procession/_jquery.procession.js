;(function ($, window, document, undefined) {

	'use strict';

	var Modernizr = window.Modernizr;

	// Helpers
	// =========================================================================

	// https://github.com/h5bp/html5-boilerplate/blob/master/js/plugins.js
	// Avoid `console` errors in browsers that lack a console.
	(function() {
			var method;
			var noop = function () {};
			var methods = [
					'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
					'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
					'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
					'timeStamp', 'trace', 'warn'
			];
			var length = methods.length;
			var console = (window.console = window.console || {});

			while (length--) {
					method = methods[length];

					// Only stub undefined methods.
					if (!console[method]) {
							console[method] = noop;
					}
			}
	}());

	// string capitalization
	var capitalize = function(str) {
		return str.charAt(0).toUpperCase() + str.slice(1);
	};

	// getStyleProperty by kangax
	// =========================================================================
	// http://perfectionkills.com/feature-testing-css-properties/

	var prefixes = 'Moz Webkit O Ms'.split(' ');

	var getStyleProperty = function( propName ) {
		var style = document.documentElement.style,
				prefixed;

		// test standard property first
		if ( typeof style[propName] === 'string' ) {
			return propName;
		}

		// capitalize
		propName = capitalize( propName );

		// test vendor specific properties
		for ( var i=0, len = prefixes.length; i < len; i++ ) {
			prefixed = prefixes[i] + propName;
			if ( typeof style[ prefixed ] === 'string' ) {
				return prefixed;
			}
		}
	};

	var transformProp = getStyleProperty('transform'),
			transitionProp = getStyleProperty('transitionProperty');

	// debouncedresize by Louis Remi
	// =========================================================================
	/*
	 * debouncedresize: special jQuery event that happens once after a window resize
	 *
	 * latest version and complete README available on Github:
	 * https://github.com/louisremi/jquery-smartresize
	 *
	 * Copyright 2012 @louis_remi
	 * Licensed under the MIT license.
	 *
	 * This saved you an hour of work? 
	 * Send me music http://www.amazon.co.uk/wishlist/HNTU0468LQON
	 */
	var $event = $.event,
		$special,
		resizeTimeout;

	$special = $event.special.debouncedresize = {
		setup: function() {
			$( this ).on( "resize", $special.handler );
		},
		teardown: function() {
			$( this ).off( "resize", $special.handler );
		},
		handler: function( event, execAsap ) {
			// Save the context
			var context = this,
				args = arguments,
				dispatch = function() {
					// set correct event type
					event.type = "debouncedresize";
					$event.dispatch.apply( context, args );
				};

			if ( resizeTimeout ) {
				clearTimeout( resizeTimeout );
			}

			execAsap ?
				dispatch() :
				resizeTimeout = setTimeout( dispatch, $special.threshold );
		},
		threshold: 150
	};

	// miniModernizr by desandro
	// =========================================================================
	// https://github.com/desandro/isotope/blob/master/jquery.isotope.js
	// <3<3<3 and thanks to Faruk and Paul for doing the heavy lifting
	/*!
	 * Modernizr v1.6ish: miniModernizr for Isotope
	 * http://www.modernizr.com
	 *
	 * Developed by:
	 * - Faruk Ates  http://farukat.es/
	 * - Paul Irish  http://paulirish.com/
	 *
	 * Copyright (c) 2009-2010
	 * Dual-licensed under the BSD or MIT licenses.
	 * http://www.modernizr.com/license/
	 */
	/*
	 * This version whittles down the script just to check support for
	 * CSS transitions, transforms, and 3D transforms.
	*/
	var tests = {
		csstransforms: function() {
			return !!transformProp;
		},

		csstransforms3d: function() {
			var test = !!getStyleProperty('perspective');
			// double check for Chrome's false positive
			if ( test ) {
				var vendorCSSPrefixes = ' -o- -moz- -ms- -webkit- -khtml- '.split(' '),
						mediaQuery = '@media (' + vendorCSSPrefixes.join('transform-3d),(') + 'modernizr)',
						$style = $('<style>' + mediaQuery + '{#modernizr{height:3px}}' + '</style>')
												.appendTo('head'),
						$div = $('<div id="modernizr" />').appendTo('html');

				test = $div.height() === 3;

				$div.remove();
				$style.remove();
			}
			return test;
		},

		csstransitions: function() {
			return !!transitionProp;
		}
	};

	var testName;

	if ( Modernizr ) {
		// if there's a previous Modernzir, check if there are necessary tests
		for ( testName in tests) {
			if ( !Modernizr.hasOwnProperty( testName ) ) {
				// if test hasn't been run, use addTest to run it
				Modernizr.addTest( testName, tests[ testName ] );
			}
		}
	} else {
		// or create new mini Modernizr that just has the 3 tests
		Modernizr = window.Modernizr = {
			_version : '1.6ish: miniModernizr for Isotope'
		};

		var classes = ' ';
		var result;

		// Run through tests
		for ( testName in tests) {
			result = tests[ testName ]();
			Modernizr[ testName ] = result;
			classes += ' ' + ( result ?  '' : 'no-' ) + testName;
		}

		// Add the new classes to the <html> element.
		$('html').addClass( classes );
	}

	// Get transitionEnd event
	// =========================================================================
	// https://github.com/desandro/isotope/blob/master/jquery.isotope.js

	var transitionEndEvent;

	if ( Modernizr.csstransitions ) {
		transitionEndEvent = {
			WebkitTransitionProperty: 'webkitTransitionEnd',  // webkit
			MozTransitionProperty: 'transitionend',
			OTransitionProperty: 'oTransitionEnd otransitionend',
			transitionProperty: 'transitionend'
		}[ transitionProp ];
	}

	// Procession plugin
	// =========================================================================
	$.Procession = function ( options, element ) {
		this.options = options;
		this.element = $(element);

		this._create( options );
	};

	$.Procession.settings = {
		orientation: 'horizontal',
		transitionDuration: 400,
		clickNav: true,
		align: 'none',
		origin: 'auto',
		active: 0,
		containerElement: '<div></div>',
		containerClass: 'procession-container',
		containerStyles: {
			position: 'relative',
			overflow: 'hidden'
		},
		innerElement: '<div></div>',
		innerClass: 'procession-inner',
		innerStyles: {
			position: 'relative',
			display: 'block'
		},
		sliderElement: '<ul></ul>',
		sliderClass: 'procession-slider',
		sliderStyles: {
			position: 'absolute',
			margin: 0,
			padding: 0,
			listStyle: 'none',
			width: '100%'
		},
		itemElement: '<li></li>',
		itemClass: 'procession-item',
		itemStyles: {
			position: 'absolute',
			visbility: 'visible',
			// might need these to prevent flickr on iOS
			'-webkit-backface-visibility': 'hidden',
			'-webkit-perspective': 1000
		}
	};

	// Create
	// =========================================================================
	$.Procession.prototype._create = function ( options ) {
		
		// set options
		this.options = $.extend(true, {}, $.Procession.settings, options);

		// first run flag
		this.firstRun = true;

		// add transition styles
		this._addTransitionStyles();

		// select inner
		this.inner = this._getInner();

		// select items
		this.items = this._getItems();

		// build DOM structure
		this._build();

		// select items
		this.items = this._getItems();

		// init first boundary position marker
		// either top or left container edge
		this.prependPos = 0;

		// init second boundary position marker
		// either bottom or right container edge
		this.appendPos = 0;

		// keep track of before and after item indices
		this.appendIndex = this.options.active;
		this.prependIndex = this.appendIndex > 0 ?
			this.appendIndex - 1 : this.items.length - 1;

		// position slider according to origin
		if ( this.options.origin !== 'center' ) {
			if ( this.options.orientation === 'horizontal' ) {
				this.slider.css( 'left', this.options.origin );
			} else if ( this.options.orientation === 'vertical' ) {
				this.slider.css( 'top', this.options.origin );
			}
		}

		// init activeItem
		this.activeItem = $();

		// save item dimensions
		this._saveItemDimensions();

		// detach items from DOM
		this.items = this.items.detach();

		// init active item
		this.activeItem = this.items.first();

		// handle interactions
		this._interactions();

		// look for modules

		// go
		this._init();

	};

	// Initialize
	// =========================================================================
	$.Procession.prototype._init = function () {
	
		this.layout();

		// activate first item
		this._activate( this._getItems('.origin') );

		// remove this from core?
		// this should probably be optional
		var _this = this;
		$(window).on( 'debouncedresize.procession', function () {
			_this.layout( false, true );
		} );

	};

	// Destroy
	// =========================================================================
	$.Procession.prototype._destroy = function () {
	

	};

	// Build DOM structure
	// =========================================================================
	$.Procession.prototype._build = function () {

		/*
		// Select inner element
		// =========================================================================
		$.Procession.prototype._getInner = function () {
			var selector = this.options.innerSelector;
			return !selector ? undefined : this.element.find(selector);
		};

		// Select slider element
		// =========================================================================
		$.Procession.prototype._getSlider = function () {
			var selector = this.options.sliderSelector;
			return !selector ? undefined : this.element.find(selector);
		};
		*/
		
		this.element.addClass( this.options.containerClass );
		this.element.css( this.options.containerStyles );

		// set up items
		this.items.addClass( this.options.itemClass );
		this.items.css( this.options.itemStyles );

		// set up inner element
		if ( this.inner === undefined || !this.inner.length ) {
			console.log('inner');
			this.inner = $( this.options.innerElement );
			this.inner.appendTo( this.element );
		}

		this.inner.addClass( this.options.innerClass );
		this.inner.css( this.options.innerStyles );

		// set up slider
		this.slider = $( this.options.sliderElement );
		this.slider.addClass( this.options.sliderClass );
		this.slider.css( this.options.sliderStyles );
		this.slider.appendTo( this.inner );

	};

	// Return origin position
	// =========================================================================
	$.Procession.prototype._getCenterPos = function () {
		

		return 50;

//		var origin = this.options.origin,
//				attr;
//
//		attr = this.options.orientation === 'horizontal' ? 'left' : 'top';
//
//		if ( origin === 'center' ) {
//
//			origin = this.options.orientation === 'horizontal' ?
//				( this.geometry.inner.width / 2 ) - ( this.activeItem.data('outerWidth') / 2 ):
//				( this.geometry.inner.height / 2 );
//		}
//
//		this.slider.css( attr, origin );
	};

	// Layout
	// =========================================================================
	$.Procession.prototype.layout = function ( delta, resize ) {
		
		// calculate current geometry
		this._geometry( delta );

		// add items
		this._appendItems();
		this._prependItems();

		// exit if this is the first layout
		if ( this.firstRun ) {
			this.firstRun = false;
			return;
		}

		// remove items outside containing element when transition is complete
		var _this = this;
		this.slider.one( transitionEndEvent, function () {
			_this._trimItems();
		} );

		// animationend is triggered for animations via _slideTo()
		// for browsers that don't support csstransition
		this.slider.one( 'animationend', function () {
			_this._trimItems();
		} );

		// if 'resize' is true, remove outside items
		if ( resize ) {
			_this._trimItems();
		}

	};

	// Remove items outside containing element
	// should only be called after transitions are complete
	// =========================================================================
	$.Procession.prototype._trimItems = function () {
		
		var _this = this;
		this._getItems().each( function () {

			var item = $(this),
					itemOffset,
					itemSize;

			if ( _this.options.orientation === 'horizontal' ) {

				itemOffset = item.offset().left;
				itemSize = item.data('outerWidth');

				// test for left items
				if ( ( itemOffset + itemSize ) < _this.geometry.element.left ) {

					// update prependPos
					_this.prependPos += itemSize;

					// update prependIndex
					_this.prependIndex = _this.prependIndex < _this.items.length - 1 ?
						_this.prependIndex + 1 : 0;

					item.remove();

				// test for right items
				} else if ( itemOffset > ( _this.geometry.element.left + _this.geometry.element.width ) ) {

					// update appendPos
					_this.appendPos -= itemSize;

					// update appendIndex
					_this.appendIndex = _this.appendIndex > 0 ?
						_this.appendIndex - 1 : _this.items.length - 1;

					item.remove();
				}

			} else if ( _this.options.orientation === 'vertical' ) {

				itemOffset = item.offset().top;
				itemSize = item.data('outerHeight');

				// test for top items
				if ( ( itemOffset + itemSize ) < _this.geometry.element.top ) {

					// update prependPos
					_this.prependPos += itemSize;

					// update prependIndex
					_this.prependIndex = _this.prependIndex < _this.items.length - 1 ?
						_this.prependIndex + 1 : 0;

					item.remove();

				// test for bottom items
				} else if ( itemOffset > ( _this.geometry.element.top + _this.geometry.element.height ) ) {

					// update appendPos
					_this.appendPos -= itemSize;

					// update appendIndex
					_this.appendIndex = _this.appendIndex > 0 ?
						_this.appendIndex - 1 : _this.items.length - 1;

					item.remove();

				}

			}

		} );

	};

	// Handle interactions
	// =========================================================================
	$.Procession.prototype._interactions = function () {
		
		var _this = this,
				key = []; // track active keys to prevent key holding

		// handle clicks
		if ( this.options.clickNav ) {
			this.slider.on( 'click', '.' + this.options.itemClass, function () {
				_this._activate( $(this) );
			} );
		}

	};

	// Activate a new item
	// =========================================================================
	$.Procession.prototype._activate = function ( item ) {
		
		var activeItemOffset,
				newItemOffset;

		if ( this.options.orientation === 'horizontal' ) {
			activeItemOffset = this.activeItem.position().left;
			newItemOffset = item.position().left;
		} else {
			activeItemOffset = this.activeItem.position().top;
			newItemOffset = item.position().top;
		}

		// calculate difference in position between new and old item
		var delta = newItemOffset - activeItemOffset;

		// update active class
		this.activeItem.removeClass('active');
		item.addClass('active');
		this.activeItem = item;

		if ( delta === 0 ) {
			return;
		}

		// run layout
		this.layout( delta );

		// slide to item
		this._slideToItem( item );

	};

	// Move the slider to a specific item
	// =========================================================================
	$.Procession.prototype._slideToItem = function ( item ) {

		var newPos;

		newPos = this.options.orientation === 'horizontal' ?
			item.position().left :
			item.position().top;

		if ( this.options.origin === 'center' ) {

			if ( this.options.orientation == 'horizontal' ) {
				newPos -= ( this.geometry.inner.width / 2 ) - ( item.data('outerWidth') / 2 );
			} else if ( this.options.orientation == 'vertical' ) {
				newPos -= ( this.geometry.inner.height / 2 ) - ( item.data('outerHeight') / 2 );
			}

		}

		this._slideTo( newPos );

	};

	// Move the slider to a new position
	// =========================================================================
	$.Procession.prototype._slideTo = function ( newPos ) {

		if ( Modernizr.csstransforms3d ) {

			this.options.orientation === 'horizontal' ?
				this.slider.css( 'transform', 'translate3d(' + -newPos + 'px, 0, 0)' ) :
				this.slider.css( 'transform', 'translate3d(0, ' + -newPos + 'px, 0)' );

		} else if ( Modernizr.csstransforms ) {

			this.options.orientation === 'horizontal' ?
				this.slider.css( 'transform', 'translateX(' + -newPos + 'px)' ) :
				this.slider.css( 'transform', 'translateY(' + -newPos + 'px)' );

		} else {

			if ( this.options.orientation === 'horizontal' ) {
				this.slider.animate( { left: -newPos }, this.options.transitionDuration, function () {
					$(this).trigger('animationend');
				} );
			} else {
				this.slider.animate( { top: -newPos }, this.options.transitionDuration, function () {
					$(this).trigger('animationend');
				} );
			}

		}

	};

	// Update geometry calculations
	// =========================================================================
	$.Procession.prototype._geometry = function ( delta ) {

		this.geometry = {
			element: {
				top: this.element.offset().top,
				left: this.element.offset().left,
				width: this.element.outerWidth(),
				height: this.element.outerHeight()
			},
			inner: {
				width: this.inner.width(),
				height: this.inner.height()
			},
			slider: {
				top: this.slider.offset().top,
				left: this.slider.offset().left,
			}
		};

		// these items are calculated based on the above
		this.geometry.element.right = this.geometry.element.left + this.geometry.element.width;
		this.geometry.element.bottom = this.geometry.element.top + this.geometry.element.height;

		// calculate available appendSpace based on orientation
		this.geometry.slider.appendSpace = this.options.orientation === 'horizontal' ?
			this.geometry.element.right - this.geometry.slider.left :
			this.geometry.element.bottom - this.geometry.slider.top;

		// calculate available prependSpace based on orientation
		this.geometry.slider.prependSpace = this.options.orientation === 'horizontal' ?
			this.geometry.element.left - this.geometry.slider.left :
			this.geometry.element.top - this.geometry.slider.top;

		// if 'delta' has been passed,
		// update appendSpace and prependSpace
		if ( typeof delta !== 'undefined' ) {

			if ( this.options.origin === 'center' ) {
				if ( this.options.orientation === 'horizontal' ) {
					//delta = ( this.items.first().data('outerWidth') / 2 )
				} else if ( this.options.orientation === 'vertical' ) {

				}
			}

			if ( delta > 0 ) {
				this.geometry.slider.appendSpace += delta;
			} else if ( delta < 0 ) {
				this.geometry.slider.prependSpace -= Math.abs(delta);
			}
		}

		if ( this.firstRun && this.options.origin === 'center' ) {

			if ( this.options.orientation === 'horizontal' ) {
				this.appendPos = ( this.geometry.inner.width / 2 ) - ( this.items.first().data('outerWidth') / 2 );
				this.prependPos += this.appendPos;
			} else if ( this.options.orientation === 'vertical' ) {
				this.appendPos = ( this.geometry.inner.height / 2 ) - ( this.items.first().data('outerHeight') / 2 )
				this.prependPos += this.appendPos;
			}

		}

	};

	// Append items
	// =========================================================================
	$.Procession.prototype._appendItems = function () {

		var items = [];
		
		while ( this.appendPos < this.geometry.slider.appendSpace ) {

			// clone ourselves an item to work with
			var item = this.items.eq( this.appendIndex ).clone(true);

			// position item
			this.options.orientation === 'horizontal' ?
				item.css( 'left', this.appendPos ) :
				item.css( 'top', this.appendPos );

			if ( this.options.align ) {
				this._align( item, this.options.align );
			}

			// add item to collection
			items.push(item);

			// update appendPos
			this.options.orientation === 'horizontal' ?
				this.appendPos += item.data('outerWidth') :
				this.appendPos += item.data('outerHeight');

			// update appendIndex
			this.appendIndex = this.appendIndex < this.items.length - 1 ?
				this.appendIndex + 1 : 0;
		}

		if ( this.firstRun ) {
			items[0].addClass('origin');
		}

		// append items to slider
		var items = $.map( items, function ( value ) {
			return value.get();
		} );

		this.slider.append( items );

	};

	// Prepend items
	// =========================================================================
	$.Procession.prototype._prependItems = function () {

		var items = [];

		while ( this.prependPos > this.geometry.slider.prependSpace ) {

			// clone ourselves an item to work with
			var item = this.items.eq( this.prependIndex ).clone(true);

			// update prependPos
			this.options.orientation === 'horizontal' ?
				this.prependPos -= item.data('outerWidth') :
				this.prependPos -= item.data('outerHeight');

			// position item
			this.options.orientation === 'horizontal' ?
				item.css( 'left', this.prependPos ) :
				item.css( 'top', this.prependPos );

			if ( this.options.align ) {
				this._align( item, this.options.align );
			}

			// add item to collection
			items.push(item);

			// update prependIndex
			this.prependIndex = this.prependIndex > 0 ?
				this.prependIndex - 1 : this.items.length - 1;
		}

		// we'll be adding them in reverse order
		items.reverse();



		items = $.map( items, function ( value ) {
			return value.get();
		} );
		this.slider.prepend( items );

	};

	// Save item dimensions
	// =========================================================================
	$.Procession.prototype._saveItemDimensions = function () {
		
		var _this = this;
		this.items.each( function () {

			var outerWidth = $(this).outerWidth(true),
					outerHeight = $(this).outerHeight(true);

			// if width or height is zero, exit
			if ( outerWidth < 1 || outerHeight < 1 ) {
				console.log('items must have dimensions');
				return;
			}

			$(this).data({
				'outerWidth': outerWidth,
				'outerHeight': outerHeight
			});

		} );

	};

	// Align items
	// =========================================================================
	$.Procession.prototype._align = function ( item, align ) {
		
		var pos;

		if ( this.options.orientation === 'horizontal' ) {

			switch ( align ) {
				case 'center':
					pos = ( this.geometry.inner.height / 2 ) - ( item.data('outerHeight') / 2 );
					break;
				case 'bottom':
					pos = this.geometry.inner.height - item.data('outerHeight');
					break;
				default:
					pos = 0;
					break;
			}

			item.css( 'top', pos );

		} else if ( this.options.orientation === 'vertical' ) {

			switch ( align ) {
				case 'center':
					pos = ( this.geometry.inner.width / 2 ) - ( item.data('outerWidth') / 2 );
					break;
				case 'right':
					pos = this.geometry.inner.width - item.data('outerWidth');
					break;
				default:
					pos = 0;
					break;
			}

			item.css( 'left', pos );

		}

	};

	// Add vendor-prefixed transition styles to settings
	// =========================================================================
	$.Procession.prototype._addTransitionStyles = function () {
		var transitionDuration = this.options.transitionDuration / 1000 + 's';

		var _this = this;
		$.each( prefixes, function () {
			var prefix = this.toLowerCase();
			_this.options.sliderStyles['-' + prefix + '-transition-duration'] = transitionDuration;
			_this.options.sliderStyles['-' + prefix + '-transition-property'] = '-' + prefix + '-transform';
			_this.options.sliderStyles['-' + prefix + '-transition-timing-function'] = _this.options.easing;
		} );

		this.options.sliderStyles['transition-duration'] = transitionDuration;
			this.options.sliderStyles['transition-property'] = 'transform';
			this.options.sliderStyles['transition-timing-function'] = _this.options.easing;
	};

	// Select all current items
	// =========================================================================
	$.Procession.prototype._getItems = function ( filter ) {
		var selector = this.options.itemSelector;
		var items = !selector ? this.element.children() : this.element.find(selector);
		return arguments.length ? items.filter(filter) : items;
	};

	// Select inner element
	// =========================================================================
	$.Procession.prototype._getInner = function () {
		var selector = this.options.innerSelector;
		return !selector ? undefined : this.element.find(selector);
	};

	// Select slider element
	// =========================================================================
	$.Procession.prototype._getSlider = function () {
		var selector = this.options.sliderSelector;
		return !selector ? undefined : this.element.find(selector);
	};

	// Plugin bridge
	// =========================================================================
	// Leverages data method to either create or return $.Procession constructor
	//
	// Pattern developed by desandro for Masonry
	//		https://github.com/desandro/masonry/blob/master/jquery.masonry.js
	// Based off of jQuery UI's widget factory
	//		https://github.com/jquery/jquery-ui/blob/master/ui/jquery.ui.widget.js
	// And a bit from jcarousel
	//		https://github.com/jsor/jcarousel/blob/master/lib/jquery.jcarousel.js

	$.Procession.prototype.option = function( key, value ){
		if ( $.isPlainObject( key ) ){
			this.options = $.extend(true, this.options, key);
		}
	};

	$.fn.procession = function( options ) {
		if ( typeof options === 'string' ) {
			// call method
			var args = Array.prototype.slice.call( arguments, 1 );

			this.each(function() {
				var instance = $.data( this, 'procession' );
				if ( !instance ) {
					console.log( 'error', "cannot call methods on procession prior to initialization; " +
						"attempted to call method '" + options + "'" );
					return;
				}
				if ( !$.isFunction( instance[options] ) || options.charAt(0) === "_" ) {
					console.log( 'error', "no such method '" + options + "' for procession instance" );
					return;
				}

				// apply method
				instance[ options ].apply( instance, args );
			});
		} else {
			this.each(function() {
				var instance = $.data( this, 'procession' );
				if ( instance ){
					// apply options & init
					instance.option( options || {} );
					instance._init();
				} else {
					// initialize new instance
					$.data( this, 'procession', new $.Procession( options, this ) );
				}
			});
		}
		return this;
	};

})(jQuery, window, document);