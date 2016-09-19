jQuery(document).ready(function () {
	'use strict';

	new jQuery.CoEnvAdmin();
});

(function ($, window, document, undefined) {

	'use strict';

	// Plugin definition
	// =========================================================================
	$.CoEnvAdmin = function ( options, element ) {
		this.options = options;
		this.element = $(element);

		this._create( options );
	};

	$.CoEnvAdmin.settings = {};

	// Create
	// =========================================================================
	$.CoEnvAdmin.prototype._create = function ( options ) {

		// set options
		this.options = $.extend(true, {}, $.CoEnvAdmin.settings, options);

		// initialize checked items
		this.checked = [];

		// initialize
		this._init();

	};

	// Initialize
	// =========================================================================
	$.CoEnvAdmin.prototype._init = function () {

		// add "subhead" checkbox to wp custom nav menu (second level items only)
		this._addCheckboxes();

	};

	/**
	 * Add subhead checkboxes to custom nav menus
	 * (second level items only)
	 */
	$.CoEnvAdmin.prototype._addCheckboxes = function () {

		var $form = $('#update-nav-menu'),
				$secondLevelItems = $form.find('.menu-item-depth-1'),
				secondLevelItemIds = [],
				_this = this;

		// collect second level item ids
		$.each( $secondLevelItems, function () {
			secondLevelItemIds.push( $(this).attr('id').split('-').pop() );
		} );

		// run ajax action to get checked items
		this._getChecked( secondLevelItemIds ).then( function () {
			addCheckboxes();
		} );

		// need to remove items when they're unchecked


		function addCheckboxes() {

			$secondLevelItems.each( function () {

				var $item = $(this),
						$checkbox = $('<p class="field-subheader description"></p>'),
						itemID = $item.attr('id').split('-').pop();

				$checkbox.append('<label for="edit-menu-item-subheader-' + itemID + '"></label>');
				$checkbox.find('label').append('<input type="checkbox" id="edit-menu-item-subheader-' + itemID + '" value="subheader" name="menu-item-subheader[' + itemID + ']"> Show child items</input>');

				//console.log( itemID, _this.checked );

				if ( $.inArray( itemID, _this.checked ) > -1 ) {
					console.log( itemID );
					$checkbox.find('input').attr( 'checked', 'checked' );
				} else {
					$checkbox.find('input').removeAttr( 'checked' );
				}

				$item.find('p.field-link-target').after( $checkbox );

			} );

		}

	};

	/**
	 * Get checked items
	 * Ajax action that talks to 'coenv_ajax_get_menu_status' in functions.php
	 */
	$.CoEnvAdmin.prototype._getChecked = function ( ids ) {

		var dfd = new $.Deferred(),
				_this = this,
				data;

		data = {
			action: 'coenv_ajax_get_menu_status',
			ids: ids
		};

		$.post( window.ajaxurl, data, function ( response ) {
			_this.checked = $.parseJSON(response);
			dfd.resolve();
		} );

		return dfd.promise();
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

	$.CoEnvAdmin.prototype.option = function( key ){
		if ( $.isPlainObject( key ) ){
			this.options = $.extend(true, this.options, key);
		}
	};

	$.fn.coenvadmin = function( options ) {
		if ( typeof options === 'string' ) {
			// call method
			var args = Array.prototype.slice.call( arguments, 1 );

			this.each(function() {
				var instance = $.data( this, 'coenvadmin' );
				if ( !instance ) {
					console.log( 'error', 'cannot call methods on coenvadmin prior to initialization; ' +
						'attempted to call method "' + options + '"' );
					return;
				}
				if ( !$.isFunction( instance[options] ) || options.charAt(0) === '_' ) {
					console.log( 'error', 'no such method "' + options + '" for coenvadmin instance' );
					return;
				}

				// apply method
				instance[ options ].apply( instance, args );
			});
		} else {
			this.each(function() {
				var instance = $.data( this, 'coenvadmin' );
				if ( instance ){
					// apply options & init
					instance.option( options || {} );
					instance._init();
				} else {
					// initialize new instance
					$.data( this, 'coenvadmin', new $.CoEnvAdmin( options, this ) );
				}
			});
		}
		return this;
	};

})(jQuery, window, document);