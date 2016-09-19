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
		this._init();
	};

	$.Procession.settings = {
		containerClass: 'procession-container',
		containerStyles: {
			position: 'relative',
			overflow: 'hidden'
		},
		innerClass: 'procession-inner',
		innerStyles: {
			position: 'relative',
			display: 'block'
		},
		sliderClass: 'procession-slider',
		sliderStyles: {
			position: 'absolute',
			width: '100%'
		},
		itemClass: 'procession-item',
		itemStyles: {
			position: 'absolute',
			visibility: 'visible',
			// might need these to prevent flickr on iOS
			'-webkit-backface-visibility': 'hidden',
			'-webkit-perspective': 1000
		},
		offset: 0,
		transitionDuration: 400,
		easing: 'ease',
		autoHeight: false,
		clickNav: true,
		keyNav: false,
		autoAdvance: {
			enable: false,
			delay: 3000,
			direction: 'forward',
			pauseOnClick: 'false',
			pauseOnHover: 'true'
		},
		verticalAlign: 'middle',
		orientation: 'vertical' // build orientations into plugin?
	};

	// Procession core
	$.Procession.prototype = {

		// Select all current items
		// =========================================================================
		_getItems: function ( filter ) {
			var selector = this.options.itemSelector;
			var items = !selector ? this.element.children() : this.element.find(selector);
			return arguments.length ? items.filter(filter) : items;
		},

		// Select inner element, the left side of which acts as the origin
		// for the slider
		// =========================================================================
		_getInner: function () {
			var selector = this.options.innerSelector;
			return !selector ? undefined : this.element.find(selector);
		},

		// Select slider element, which responds to user interactions and 
		// origin for item positions
		// =========================================================================
		_getSlider: function () {
			var selector = this.options.sliderSelector;
			return !selector ? undefined : this.element.find(selector);
		},

		// Runs once on first initialization of plugin
		// =========================================================================
		_create: function ( options ) {

			// set options
			this.options = $.extend(true, {}, $.Procession.settings, options);

			// first run flag
			this.firstLayout = true;

			// add transition styles
			this._addTransitionStyles();

			// select inner
			this.inner = this._getInner();

			// select items
			this.items = this._getItems();
			//this.originalItems = this.items.clone();

			// build DOM structure
			this._build();

			// initialize first boundary position marker
			this.bound1 = 0;

			// initialize second boundary position marker
			this.bound2 = 0;

			// initialize right item position marker
			this.rPos = 0;

			// initialize left item position marker
			this.lPos = 0;

			// keeps track of next right and left item indices
			this.rIndex = 0;
			this.lIndex = this.items.length - 1; // start on last item

			// position slider according to offset
			this.slider.css( 'left', this.options.offset );

			// initialize active item
			this.activeItem = $();

			// keep track of item dimensions
			this._saveItemDimensions();

			// detach items from DOM
			this.items = this.items.detach();

			// handle interactions
			this._interactions();

			// look for autoadvance module
			if ( this.options.autoAdvance.enable ) {
				this._autoAdvance();
			}
		},

		// Runs when instance is first created and every time plugin is initialized
		// =========================================================================
		_init: function () {

			this.layout();

			var _this = this;
			$(window).on( 'debouncedresize.procession', function () {
				_this.layout( false, true );
			} );

		},

		// Destroys widget, returns elements back to original styles
		// Reset DOM, unbind events
		// =========================================================================
		destroy: function () {

			var opts = this.options,
					items = this._getItems();

			// make this automatic
			this.originalContainerStyles = {
				position: 'relative',
				overflow: 'visible',
				height: 'auto'
			};

			this.originalItemStyles = {
				position: 'relative',
				top: 'auto',
				right: 'auto',
				bottom: 'auto',
				left: 'auto'
			};

			this.originaInnerStyles = {
				position: 'relative',
				display: 'block',
				height: 'auto'
			};

			// tear down container element
			this.element.css( this.originalContainerStyles );
			this.element.removeClass( this.options.containerClass );

			// tear down items
			this.items.css( this.originalItemStyles );
			this.items.removeClass( this.options.itemClass );

			this.inner.empty().append( this.items );

			//items.replaceWith( this.items );

			// tear down inner element
			if ( !this.options.innerSelector ) {
				this.inner.remove();
			} else {
				this.inner.css( this.originaInnerStyles );
			}

			// tear down slider
			this.slider.replaceWith( function () {
				return $(this).children();
			} );

			this.element.removeData('procession');

			// unbind all handlers
			$(window).off( '.procession' );
			this.element.off( '.procession' );

			// kill plugins
			clearInterval( this.autoAdvance );
		},

		// Set up DOM elements
		// =========================================================================
		_build: function () {

			this.element.addClass( this.options.containerClass );
			this.element.css( this.options.containerStyles );

			// set up items
			this.items.addClass( this.options.itemClass );
			this.items.css( this.options.itemStyles );

			// set up inner element
			if ( this.inner === undefined || !this.inner.length ) {
				this.inner = $('<div>');
				this.inner.appendTo(this.element);
			}

			if ( this.options.autoHeight ) {
				this._setHeight( this.inner, this.items );
			}

			this.inner.addClass( this.options.innerClass );
			this.inner.css( this.options.innerStyles );

			// set up slider
			// looks like this is appended after inner is appended to element
			// should all be appended at the same time
			this.slider = $('<div>');
			this.slider.addClass( this.options.sliderClass );
			this.slider.css( this.options.sliderStyles );
			this.slider.appendTo( this.inner );
		},

		// Add vendor-prefixed slider transition styles to settings
		// =========================================================================
		_addTransitionStyles: function () {

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
		},

		// Loops through items and saves outerWidth(true) as item.data('outerwidth')
		// for use later (run before detaching items)
		// =========================================================================
		_saveItemDimensions: function () {

			// save outer width of each item
			var _this = this;
			this.items.each( function () {

				var outerWidth = $(this).outerWidth(true),
						outerHeight = $(this).outerHeight(true);

				// if width is zero, exit with error
				if ( outerWidth < 1 ) {
					console.log('items must have width');
					return;
				}

				$(this).data({
					'outerwidth': outerWidth,
					'outerheight': outerHeight
				});
			});

		},

		// Set $el height to the max outerheight of $items
		// now that we are supporting vertical orientation, 
		// this needs to be generalized
		// =========================================================================
		_setHeight: function ( $el, $items ) {

			var height = 0;

			$items.each( function () {
				height = Math.max( height, $(this).outerHeight(true) );
			} );

			$el.height(height);
		},

		// layout()
		// argument 'delta' is the distance between current active item and new item
		// =========================================================================
		layout: function ( delta, resize ) {

			// calculate current geometry
			this._geometry(delta);

			this._addRightItems();
			this._addLeftItems();

			// exit if this is the first layout
			if ( this.firstLayout ) {
				this.firstLayout = false;
				return;
			}

			// remove outside items when transition is complete
			var _this = this;

			// To do: DRY these up
			this.slider.one( transitionEndEvent, function () {
				_this._removeOutsideItems();
			} );
			// animationend is triggered for animations on slider
			// for browsers that don't support csstransition
			this.slider.one( 'animationend', function () {
				_this._removeOutsideItems();
			} );

			// if 'resize' is true, remove outside items
			if ( resize ) {
				_this._removeOutsideItems();
			}

		},

		// Update geometry calculations
		// argument 'delta' is the distance between current active item and new item
		// =========================================================================
		_geometry: function ( delta ) {

			// container left edge position
			this.elLeftEdge = this.element.offset().left;

			// container width
			this.elWidth = this.element.width();

			// container right edge position
			this.elRightEdge = this.elLeftEdge + this.elWidth;

			// inner element height
			this.inHeight = this.inner.height();

			// slider left edge position
			this.sLeftEdge = this.slider.offset().left;

			// available space to the right
			this.rSpace = this.elRightEdge - this.sLeftEdge;

			// available space to the left
			this.lSpace = this.elLeftEdge - this.sLeftEdge;

			// if 'delta' has been passed, 
			// update rSpace 
			if ( typeof delta !== 'undefined' ) {

				if ( delta > 0 ) {
					this.rSpace += delta;
				} else if ( delta < 0 ) {
					this.lSpace -= Math.abs(delta);
				}
			}

		},

		// Position items to the right of origin within slider
		// =========================================================================
		_addRightItems: function () {

			var dfd = new $.Deferred(), 
					items = [];

			// add items left to right starting at current rPos, until rPos reaches rSpace
			while ( this.rPos <= this.rSpace ) {

				var item = this.items.eq( this.rIndex ).clone(true);

				// position item
				item.css( 'left', this.rPos );

				if ( this.options.verticalAlign ) {
					var vPos = this._verticalAlign( item, this.options.verticalAlign );
					item.css( 'top', vPos );
				}

				// add item to collector array
				items.push(item);

				// update rPos
				this.rPos += item.data('outerwidth');

				// udpate rIndex
				this.rIndex = this.rIndex < this.items.length - 1 ?
					this.rIndex + 1 : 0;
			}

			// first time around, add active and origin classes
			if ( this.firstLayout ) {
				this.activeItem = items[0];
				this.activeItem.addClass('active procession-origin');
			}

			// append items to slider
			var items = $.map( items, function ( value ) {
				return value.get();
			} );
			this.slider.append( items );

			return dfd.resolve();
		},

		// Position items to the left of origin within slider
		// =========================================================================
		_addLeftItems: function () {

			var dfd = new $.Deferred(),
					items = [];

			// add items right to left starting at current lPos, until lPos reaches lSpace
			while ( this.lPos >= this.lSpace ) {

				var item = this.items.eq( this.lIndex ).clone(true);

				// update lPos
				this.lPos -= item.data('outerwidth');

				// position
				item.css( 'left', this.lPos );

				if ( this.options.verticalAlign ) {
					var vPos = ( this.inHeight / 2 ) - ( item.data('outerheight') / 2 );
					item.css( 'top', vPos );
				}

				// add to collector object
				items.push(item);

				// update lIndex
				this.lIndex = this.lIndex > 0 ?
					this.lIndex - 1 : this.items.length - 1;
			}

			items.reverse();

			items = $.map( items, function ( value ) {
				return value.get();
			} );
			this.slider.prepend( items );

			return dfd.resolve();
		},

		// Remove items that lay outside the wrapping element
		// =========================================================================
		_removeOutsideItems: function () {

			var _this = this;
			this._getItems().each( function () {

				var item = $(this),
						iOffset = item.offset().left,
						iWidth = item.data('outerwidth');

				// test for left items
				if ( ( iOffset + iWidth ) < _this.elLeftEdge ) {

					// update lPos
					_this.lPos += iWidth;

					// update lIndex
					_this.lIndex = _this.lIndex < _this.items.length - 1 ?
						_this.lIndex + 1 : 0;

					item.remove();
				}

				// test for right items
				else if ( iOffset > ( _this.elLeftEdge + _this.elWidth ) ) {

					// update rPos
					_this.rPos -= iWidth;

					// update index
					_this.rIndex = _this.rIndex > 0 ?
						_this.rIndex - 1 : _this.items.length - 1;

					item.remove();
				}

			} );

		},

		// Vertical alignment formulas
		// =========================================================================
		_verticalAlign: function ( item, style ) {

			var vPos;

			switch (style) {
				case 'middle':
					vPos = ( this.inHeight / 2 ) - ( item.data('outerheight') / 2 );
					break;
				case 'bottom':
					vPos = this.inHeight - item.data('outerheight');
					break;
				default:
					vPos = 0;
					break;
			}

			return vPos;
		},

		// Set up click, key and touch interactions
		// =========================================================================
		_interactions: function () {

			var _this = this,
					key = []; // track active keys to prevent key holding

			// handle clicks
			if ( this.options.clickNav ) {
				this.slider.on( 'click', '.' + this.options.itemClass, function () {
					_this._activate( $(this) );
				} );
			}

			// handle keys
			if ( this.options.keyNav ) {
				$(document).keydown( function ( e ) {
					switch( e.which ) {
						case 39: // right arrow
							if ( !key['39'] ) {
								_this._activate( _this.activeItem.next() );
								key['39'] = true;
							}
							break;
						case 37: // left arrow
							if ( !key[37] ) {
								_this._activate( _this.activeItem.prev() );
								key['37'] = true;
							}
							break;
					}
				});

				$(document).keyup( function ( e ) {
					var keycode = e.keyCode ? e.keyCode : e.which;
					key[keycode] = false;
				} );
			}

		},

		// Activate a new item
		// =========================================================================
		_activate: function ( item ) {

			var activeItemOffset = this.activeItem.position().left,
					newItemOffset = item.position().left;

			// calculate difference in position between new and old item
			var delta = newItemOffset - activeItemOffset;

			if ( delta === 0 ) {
				return;
			}

			// update active class
			this.activeItem.removeClass('active');
			item.addClass('active');
			this.activeItem = item;

			// run layout
			this.layout(delta);

			// slide to item
			this._slideToItem(item);
		},

		// Move the slider to a specific item
		// =========================================================================
		_slideToItem: function ( item ) {
			this._slideTo( item.position().left );
		},

		// Move the slider to new position
		// =========================================================================

		_slideTo: function ( newPos ) {

			if ( Modernizr.csstransforms3d ) {

				this.slider.css( 'transform', 'translate3d(' + -newPos + 'px, 0, 0)' );

			} else if ( Modernizr.csstransforms ) {

				this.slider.css( 'transform', 'translateX(' + -newPos + 'px)' );

			} else {

				this.slider.animate( { left: -newPos }, this.options.transitionSpeed, function () {
					$(this).trigger('animationend');
				} );

			}
		},

	}; // $.Procession core

	// AutoAdvance module (not found message)
	// =========================================================================
	$.Procession.prototype._autoAdvance = function () {
		console.log('Please load jquery.procession.autoadvance.js after Procession to enable autoAdvance.');
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