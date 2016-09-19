/**
 * Share modals
 * for blog posts
 */
(function ($, window, document, undefined) {

	'use strict';

	// Plugin definition
	// =========================================================================
	$.CoEnvShare = function ( options, element ) {
		this.options = options;
		this.element = $(element);

		this._create( options );
	};

	$.CoEnvShare.settings = {};

	// Create
	// =========================================================================
	$.CoEnvShare.prototype._create = function ( options ) {

		// set options
		this.options = $.extend(true, {}, $.CoEnvShare.settings, options);

		// initialize
		this._init();

	};

	// Initialize
	// =========================================================================
	$.CoEnvShare.prototype._init = function () {

		this.articleID = this.element.attr('data-article-id');
		this.articleTitle = this.element.attr('data-article-title');
		this.articleShortLink = this.element.attr('data-article-shortlink');
		this.articlePermalink = this.element.attr('data-article-permalink');

		// show share button
		// must have data-article-id set
		if (
			typeof( this.articleID ) !== 'undefined' &&
			typeof( this.articleTitle ) !== 'undefined' &&
			typeof( this.articleShortLink ) !== 'undefined' &&
			typeof( this.articlePermalink ) !== 'undefined' ) {
			this.element.addClass('active');
		} else {
			return;
		}

		// build modal
		this._buildModal();

		// handle interactions
		this._interactions();
	};

	/**
	 * Build modal
	 * Building DOM elements here to keep things quick and simple, but this should really be in a template
	 */
	$.CoEnvShare.prototype._buildModal = function () {

		var services = [
			{
				name: 'Twitter',
				className: 'twitter',
				url: 'http://twitter.com/home?status=' + this.articleTitle + '-' + this.articleShortLink
			},
			{
				name: 'Facebook',
				className: 'facebook',
				url: 'http://www.facebook.com/sharer/sharer.php?s=100&p[url]=' + this.articleShortLink + '&p[images][0]=&p[title]=' + this.articleTitle
			}
		];

		this.$modal = $('<div class="share-modal" role="dialog" aria-labelledby="shareModal" aria-hidden="true"></div>');
		this.$modal.append('<div class="share-modal-inner"><div class="share-modal-content"></div></div>');

		for ( var i = 0, len = services.length; i < len; i++ ) {
			this.$modal.find('.share-modal-content').append('<a class="share-' + services[ i ].className + '" href="' + services[ i ].url + '"><span>Share on ' + services[ i ].name + '</span></a>');
		}
	};

	/**
	 * Handle interactions
	 */
	$.CoEnvShare.prototype._interactions = function () {
		var _this = this;

		// clicking on link
		this.element.on( 'click', function ( ev ) {
			ev.preventDefault();
			ev.stopPropagation();
			_this._launchModal();
		} );

		// clicking outside of modal
		$('body').on( 'click', function ( ev ) {
			if ( _this.modalIsActive === true ) {

				if ( !$(ev.target).is('.share-modal-content') ) {
					_this._hideModal();
				}
			}
		});

	};

	/**
	 * Launch share modal
	 */
	$.CoEnvShare.prototype._launchModal = function () {
		var _this = this;

		// append modal
		$('body').append( this.$modal );

		// show modal
		this.$modal.addClass('active');

		this.modalIsActive = true;

		setTimeout( function () {
			_this.$modal.addClass('active-visible');
		}, 10 );
	};

	/**
	 * Hide modal
	 */
	$.CoEnvShare.prototype._hideModal = function () {
		var _this = this;

		this.$modal.removeClass('active-visible');

		//setTimeout( function () {
		_this.$modal.removeClass('active');
		_this.$modal.remove();
		_this.modalIsActive = false;
		//}, 10 );

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

	$.CoEnvShare.prototype.option = function( key ){
		if ( $.isPlainObject( key ) ){
			this.options = $.extend(true, this.options, key);
		}
	};

	$.fn.coenvshare = function( options ) {
		if ( typeof options === 'string' ) {
			// call method
			var args = Array.prototype.slice.call( arguments, 1 );

			this.each(function() {
				var instance = $.data( this, 'coenvshare' );
				if ( !instance ) {
					console.log( 'error', 'cannot call methods on coenvshare prior to initialization; ' +
						'attempted to call method "' + options + '"' );
					return;
				}
				if ( !$.isFunction( instance[options] ) || options.charAt(0) === '_' ) {
					console.log( 'error', 'no such method "' + options + '" for coenvshare instance' );
					return;
				}

				// apply method
				instance[ options ].apply( instance, args );
			});
		} else {
			this.each(function() {
				var instance = $.data( this, 'coenvshare' );
				if ( instance ){
					// apply options & init
					instance.option( options || {} );
					instance._init();
				} else {
					// initialize new instance
					$.data( this, 'coenvshare', new $.CoEnvShare( options, this ) );
				}
			});
		}
		return this;
	};

})(jQuery, window, document);