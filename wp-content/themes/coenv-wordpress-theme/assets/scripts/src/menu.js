jQuery(function ($) {
	'use strict';

	// initialize CoEnvMenu plugin
	// IE9+ only
	$('html').not('.lt-ie9').coenvmenu();

});

(function ($, window, document, undefined) {
	'use strict';

	// Plugin definition
	// =========================================================================
	$.CoEnvMenu = function ( options, element ) {
		this.options = options;
		this.element = $(element);

		this._create( options );
	};

	$.CoEnvMenu.settings = {
		outerSelector: '#outer',
		wrapperSelector: '#wrapper',
		menuSelector: '.main-menu',
		submenuClass: 'children',
		topMenuSelector: '.top-menu',
		menuButtonSelector: '#show-menu button',
		topLevelItemSelector: '.page-depth-0',
		mobileMenuClass: 'mobile-menu',
		normalMenuClass: 'normal-menu',
		normalTopMenuClass: 'normal-top-menu',
		mobileTopMenuClass: 'mobile-top-menu',
		mobileMenuActiveClass: 'mobile-menu-active',
		mobileTopMenuActiveClass: 'mobile-top-menu-active',
		menuItemActiveClass: 'menu-item-active'
	};

	// Create
	// =========================================================================
	$.CoEnvMenu.prototype._create = function ( options ) {

		// set options
		this.options = $.extend(true, {}, $.CoEnvMenu.settings, options);

		// initialize
		this._init();

	};

	// Initialize
	// =========================================================================
	$.CoEnvMenu.prototype._init = function () {

		this.$outer = $(this.options.outerSelector);
		this.$wrapper = $(this.options.wrapperSelector);
		this.$menu = $(this.options.menuSelector);
		this.$topMenu = $(this.options.topMenuSelector);
		this.$menuButton = $(this.options.menuButtonSelector);

		// set up menu DOM structure
		this._buildMenu();

		// handle main menu dropdowns
		this._dropdowns();

		// set up mobile menu
		this._setupMobileMenu();

		// add arrow icons before nav items
		this._addArrowIcons();

		// set up top menu
		this._setupTopMenu();

		// handle menu button interactions
		this._handleMenuButton();

		// handle clicking on this.$wrapper when mobile menu is active
		this._handleWrapperClicking();

		// handle interacting on top level items in mobile menu
		this._handleMenuInteractions();

		// handle window resize with mobile menu still visible
		this._handleWindowResize();

	};

	/**
	 * Build menu DOM structure
	 */
	$.CoEnvMenu.prototype._buildMenu = function () {

		var _this = this;

		// select top level menu items
		var $navItem = this.$menu.find( this.options.topLevelItemSelector );

		// select all submenus in the menu
		var $submenu = $navItem.find('> .' + this.options.submenuClass);

		var $submenuContainer = $('<div class="submenu-container"></div>');

		// divide each submenu's items into two columns
		$submenu.each( function () {

			var $items = $(this).find('> li'),
					itemsLength = $items.length,
					colBreak = Math.round( itemsLength / 2 );

			// wrap items in left and right columns
			$items.slice( 0, colBreak ).wrapAll('<li class="col-left"><ul></ul></li>');
			$items.slice( colBreak, itemsLength ).wrapAll('<li class="col-right"><ul></ul></li>');
		} );

		// for each top level nav item
		$navItem.each( function () {

			var $link = $(this).find('> span > a'),
					linkTitle = $link.attr('title'),
					title,
					url;

			title = linkTitle !== undefined && linkTitle !== false ? linkTitle : $link.text();
			url = $link.attr('href');

			// add submenu if it does not exist
			if ( !$(this).find('> .' + _this.options.submenuClass).length ) {
				$(this).append('<ul class="' + _this.options.submenuClass + '"></ul>');
			}

			$(this).find('> .' + _this.options.submenuClass).prepend('<li class="pagenav"><a href="' + url + '">' + title + '</a></li>');
		} );

		// wrap first sub menu in .submenu-container
		this.$menu.find( this.options.topLevelItemSelector + ' > .' + this.options.submenuClass ).wrap( $submenuContainer );

	};

	// Handle dropdowns
	// =========================================================================
	$.CoEnvMenu.prototype._dropdowns = function () {
		var _this = this;

		// use hover intent to apply active class to top level nav items
		this.$menu.find( this.options.topLevelItemSelector ).has('.' + this.options.submenuClass).hoverIntent( function () {
			$(this).toggleClass( _this.options.menuItemActiveClass );
		} );
	};

	// Set up mobile menu
	// =========================================================================
	$.CoEnvMenu.prototype._setupMobileMenu = function () {
		// make a copy of this.$menu to place behind this.$wrapper within this.$outer
		this.$mobileMenu = this.$menu.clone();
		this.$mobileMenu.removeClass( this.options.normalMenuClass ).addClass( this.options.mobileMenuClass );

		// add a "home" link to the top of the mobile menu
		//this.$mobileMenu.find('ul').prepend('<li><a href="/">Home</a></li>');

		this.$mobileMenu.appendTo( this.$outer ).show();
	};

	// Add arrow icons to menu items
	// =========================================================================
	$.CoEnvMenu.prototype._addArrowIcons = function () {
		this.$mobileMenu.find( this.options.topLevelItemSelector ).has('.' + this.options.submenuClass).each(function () {
			$(this).find('a').first().prepend('<i></i>');
		});
	};

	// Set up top links menu
	// =========================================================================
	$.CoEnvMenu.prototype._setupTopMenu = function () {
		// make a copy of this.$topMenu to place within this.$mobileMenu
		this.$mobileTopMenu = this.$topMenu.clone();
		this.$mobileTopMenu.removeClass( this.options.normalTopMenuClass ).addClass( this.options.mobileTopMenuClass );
		this.$mobileTopMenu.appendTo( this.$mobileMenu ).show();
	};

	// Handle menu button interactions
	// =========================================================================
	$.CoEnvMenu.prototype._handleMenuButton = function () {
		var _this = this;

		// handle clicking on menu button (using fastClick)
		this.$menuButton.fastClick( function (e) {
			e.preventDefault();
			_this._toggleMobileMenu();
		} );
	};

	// Show/hide mobile menu
	// =========================================================================
	$.CoEnvMenu.prototype._toggleMobileMenu = function () {
		$('html').toggleClass( this.options.mobileMenuActiveClass );
	};

	// Handle window resize when mobile menu is still visible
	// =========================================================================
	$.CoEnvMenu.prototype._handleWindowResize = function () {
		var _this = this;
		$(window).on( 'resize', $.debounce( 100, function () {
			if ( ( $(window).width() >= 768 ) && $('html').hasClass( _this.options.mobileMenuActiveClass ) ) {
				_this._toggleMobileMenu();
			}
		} ) );
	};

	// Handle clicking on this.$wrapper when mobile menu is active
	// =========================================================================
	$.CoEnvMenu.prototype._handleWrapperClicking = function () {
		var _this = this;

		this.$wrapper.on( 'click', function (e) {

			if ( $('html').hasClass( _this.options.mobileMenuActiveClass ) ) {
				e.preventDefault();
				_this._toggleMobileMenu();
			}

		} );
	};

	// Handle menu interactions
	// =========================================================================
	$.CoEnvMenu.prototype._handleMenuInteractions = function () {

		var _this = this,
				$items = this.$mobileMenu.find( this.options.topLevelItemSelector ),
				$itemLinks = $items.find('> span > a');

		// on page load, make sure appropriate item has the expanded class
		$items.filter('.menu-item-active, .current_page_item, .current-page-ancestor').each(function () {
			$(this).addClass('expanded');
			//$(this).find('> span > a > i').css('outline', '1px solid red');
		});

		$itemLinks.fastClick( function (e) {
			e.preventDefault();

			var $item = $(this).parents( _this.options.topLevelItemSelector ),
					$icon = $(this).find('i'),
					$subMenu = $item.find('.' + _this.options.submenuClass);

			if ( $item.hasClass('expanded') ) {
				$subMenu.slideUp( 200 );
				$item.removeClass('expanded');
			} else {
				$subMenu.slideDown( 200 );
				$item.addClass('expanded');
			}
		} );

	};

	// Plugin bridge
	// =========================================================================
	// Leverages data method to either create or return plugin constructor
	//
	// Pattern developed by desandro for Masonry
	//		https://github.com/desandro/masonry/blob/master/jquery.masonry.js
	// Based off of jQuery UI's widget factory
	//		https://github.com/jquery/jquery-ui/blob/master/ui/jquery.ui.widget.js
	// And a bit from jcarousel
	//		https://github.com/jsor/jcarousel/blob/master/lib/jquery.jcarousel.js

	$.CoEnvMenu.prototype.option = function( key ){
		if ( $.isPlainObject( key ) ){
			this.options = $.extend(true, this.options, key);
		}
	};

	$.fn.coenvmenu = function( options ) {
		if ( typeof options === 'string' ) {
			// call method
			var args = Array.prototype.slice.call( arguments, 1 );

			this.each(function() {
				var instance = $.data( this, 'coenvmenu' );
				if ( !instance ) {
					console.log( 'error', 'cannot call methods on coenvmenu prior to initialization; ' +
						'attempted to call method "' + options + '"' );
					return;
				}
				if ( !$.isFunction( instance[options] ) || options.charAt(0) === '_' ) {
					console.log( 'error', 'no such method "' + options + '" for coenvmenu instance' );
					return;
				}

				// apply method
				instance[ options ].apply( instance, args );
			});
		} else {
			this.each(function() {
				var instance = $.data( this, 'coenvmenu' );
				if ( instance ){
					// apply options & init
					instance.option( options || {} );
					instance._init();
				} else {
					// initialize new instance
					$.data( this, 'coenvmenu', new $.CoEnvMenu( options, this ) );
				}
			});
		}
		return this;
	};

})(jQuery, window, document);