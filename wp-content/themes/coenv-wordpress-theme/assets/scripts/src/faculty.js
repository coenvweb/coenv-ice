jQuery(function ($) {
	'use strict';

	// initialize CoEnvFaculty plugin
	// on faculty archive page
	$('#faculty-archive').coenvfaculty();

});

(function ($, window, document, undefined) {
	'use strict';

	/**
	 * Plugin definition
	 */
	$.CoEnvFaculty = function ( options, element ) {
		this.options = options;
		this.element = $(element);

		this._create( options );
	};

	/**
	 * Plugin settings
	 */
	$.CoEnvFaculty.settings = {};

	/**
	 * Create
	 */
	$.CoEnvFaculty.prototype._create = function ( options ) {

		// set options
		this.options = $.extend(true, {}, $.CoEnvFaculty.settings, options);

		// initialize
		this._init();

	};

	/**
	 * Initialize
	 */
	$.CoEnvFaculty.prototype._init = function () {

		// define jQuery objects
		this.$grid = this.element.find('.faculty-tiles');
		this.$tiles = this.$grid.find('.jsIsotopeItem');
		this.$toolbox = this.element.find('.faculty-toolbox');
		this.$roller = this.$toolbox.find('.faculty-toolbox-theme-roller');
		this.$feedback = this.$toolbox.find('.faculty-toolbox-feedback');
		this.$form = this.$toolbox.find('.faculty-toolbox-form form');
		this.$themeSelect = this.$form.find('#select-theme');
		this.$unitSelect = this.$form.find('#select-unit');
		this.$searchField = this.$form.find('#keyword-search');

		// initialize filterQueue
		this.filterQueue = this._resetFilterQueue();

		// set up observers
		this._setupObservers();

		// set up theme roller
		this._setupRoller();

		// kick off isotope
		this._initializeIsotope();
	};

	/**
	 * Set up observers
	 * Hook up methods that subscribe to published events
	 */
	$.CoEnvFaculty.prototype._setupObservers = function () {

		// this.$grid.on( 'triggerIsotope' )
		this._isotope();
		this._feedback();

		// this.$themeSelect.chosen().change()
		// this.$unitSelect.chosen().change()
		this._handleSelects();

		// handle search
		this._handleSearch();

		// handle toggling between roller and form
		this._toggleToolbox();

		// handle updating URL hash
		this._updateHash();
	};

	/**
	 * Get theme roller items
	 */
	$.CoEnvFaculty.prototype._getRollerItems = function () {
		return this.$roller.find('.faculty-toolbox-theme-roller-item');
	};

	/**
	 * Set up theme roller
	 */
	$.CoEnvFaculty.prototype._setupRoller = function () {

		var $interactionLayer = this.$roller.find('.faculty-toolbox-interaction-layer'),
				_this = this;

		// set up procession
		this._setupRollerProcession();

		// disable double click
		this.$roller.on( 'dblclick', function ( e ) {
		//	e.preventDefault();
		} );

		// handle clicking on items
		this.$roller.find('.faculty-toolbox-theme-roller-item').live( 'click', function () {

			var $item = $(this),
					data = {};

			console.log('click');

			data.filters = {
				theme: {
					name: $item.text(),
					slug: $item.attr('data-theme'),
					permalink: $item.attr('data-permalink')
				}
			};

			// trigger isotope
			_this.$grid.trigger( 'triggerIsotope', [ data ] );

			// trigger rollerchange
			_this.$roller.trigger( 'rollerChange', [ data ] );

		} );

		this.$toolbox.on( 'rollerView', function () {
			// reset roller
			_this.$roller.procession('destroy');
			_this._setupRollerProcession();

			_this._resetIsotope();
		} );

		function getCoordinates( $items ) {
			var coordinates = [];
			$items.each( function () {
				var posY = $(this).offset().top;
				coordinates.push({
					index: $(this).index(),
					rangeA: posY,
					rangeB: posY + $(this).outerHeight()
				});
			} );
			return coordinates;
		}

	};

	/**
	 * Set up Procession on theme roller
	 */
	$.CoEnvFaculty.prototype._setupRollerProcession = function () {
		this.$roller.procession({
			innerSelector: '.faculty-toolbox-theme-roller-inner',
			itemSelector: '.faculty-toolbox-theme-roller-item',
			orientation: 'vertical',
			origin: 'center',
			transitionDuration: 300,
		//	keyNav: true
		});
	};

	/**
	 * Handle hashes
	 */
	$.CoEnvFaculty.prototype._updateHash = function () {

		var _this = this,
				hashes,
				hash;

		this.$grid.on( 'triggerIsotope', function ( e, data ) {

			// build hash
			hash = $.map( _this.filterQueue, function ( value ) {
				return value.slug;
			} ).join('&');

			window.location.hash = hash;
		} );

	};

	/**
	 * Toggle form tools
	 */
	$.CoEnvFaculty.prototype._toggleToolbox = function () {

		var $showToolsButton = this.$toolbox.find('.faculty-toolbox-more-search-tools a'),
				$showRollerButton = this.$toolbox.find('.faculty-toolbox-header a'),
				_this = this;

		if ( window.location.hash ) {
			_this.$toolbox.trigger('formView');
			_this.$toolbox.addClass('show-tools');
		}

		$showToolsButton.on( 'click', function ( e ) {
			e.preventDefault();

			_this.$toolbox.trigger('formView');

			// show tools
			_this.$toolbox.addClass('show-tools');
		} );

		$showRollerButton.on( 'click', function ( e ) {
			e.preventDefault();

			_this.$toolbox.trigger('rollerView');

			// show roller
			_this.$toolbox.removeClass('show-tools');
		} );

	};

	/**
	 * Initial run of Isotope
	 * Sets up basic Isotope settings
	 *
	 * Published: this.$grid.trigger('triggerIsotope');
	 */
	$.CoEnvFaculty.prototype._initializeIsotope = function () {

		var data = {},
				_this = this;

		// helpful isotope definitions
		this.isoItemSelector = '.jsIsotopeItem';
		this.isoCornerStampSelector = '.faculty-toolbox';

		data.options = {
			itemSelector: this.isoItemSelector,
			resizable: false,
			masonry: {
				columnWidth: _this.$grid.width() / 8,
				cornerStampSelector: this.isoCornerStampSelector
			}
		};

		// if url hash exists, parse and add filters to data
		if ( window.location.hash ) {
			data.filters = this._parseHash();
		}

		data.callback = function () {
			_this.$tiles.find('.jsIsotopeItemInner').addClass('visible');
			_this._updateSelects();
		};

		this.$grid.trigger('triggerIsotope', [ data ]);

		// listen for window resize
		$(window).smartresize( function () {
			_this.$grid.trigger('triggerIsotope', [{
				options: {
					masonry: {
						columnWidth: _this.$grid.width() / 8
					}
				}
			}]);
		} );
	};

	/**
	 * Parse URL hash and return as filter argument
	 */
	$.CoEnvFaculty.prototype._parseHash = function () {

		var hashes,
				$themeOpt,
				$unitOpt,
				filters,
				_this = this;

		hashes = window.location.hash.replace('#', '').split('&');

		$.each( hashes, function () {

			// test for theme
			if ( this.indexOf('theme') !== -1 ) {
				$themeOpt = _this.$themeSelect.find('option[value="' + this + '"]');
			}

			// test for unit
			if ( this.indexOf('unit') !== -1 ) {
				$unitOpt = _this.$unitSelect.find('option[value="' + this + '"]');
			}

		} );

		if ( $themeOpt === undefined ) {
			$themeOpt = this.$themeSelect.find('option').first();
		}

		if ( $unitOpt === undefined ) {
			$unitOpt = this.$unitSelect.find('option').first();
		}

		filters = {
			theme: {
				name: $themeOpt.text(),
				slug: $themeOpt.attr('value'),
				permalink: $themeOpt.attr('data-permalink')
			},
			unit: {
				name: $unitOpt.text(),
				slug: $unitOpt.attr('value'),
				permalink: $unitOpt.attr('data-permalink')
			}
		};

		return filters;
	};

	/**
	 * Reset Isotope
	 */
	$.CoEnvFaculty.prototype._resetIsotope = function () {

		var data = {};

		// reset filterQueue
		this.filterQueue = this._resetFilterQueue();

		this.$grid.trigger( 'triggerIsotope', [ data ] );
	};

	/**
	 * Run Isotope
	 *
	 * Subscribed: this.$grid.on('triggerIsotope');
	 */
	$.CoEnvFaculty.prototype._isotope = function () {

		var _this = this,
				filters;

		this.$grid.on( 'triggerIsotope', function ( e, data ) {

			// data.options needs to be an object
			if ( data.options === undefined ) {
				data.options = {};
			}

			// check if filters were passed
			if ( data.filters !== undefined ) {

				// add filters to filter queue
				for ( var prop in data.filters ) {
					_this.filterQueue[ prop ] = data.filters[ prop ];
				}

			}

			filters = $.map( _this.filterQueue, function ( value ) {
				return '.' + value.slug;
			} ).join('');

			// check if search was passed (overrides filters)
			if ( data.search !== undefined ) {

				// add search ids to filters
				filters = $.map( data.search.ids, function ( value ) {
					return '.' + value;
				} ).join(', ');
			}

			// ensure toolbox is not filtered out
			data.options.filter = filters + ', .faculty-toolbox';

			// run isotope
			_this.$grid.isotope( data.options, data.callback );

		} );

	};

	/**
	 * Feedback messages
	 *
	 * Subscribed: this.$grid.on('triggerIsotope')
	 */
	$.CoEnvFaculty.prototype._feedback = function () {

		var $message = this.$feedback.find('.feedback-message'),
				$number = this.$feedback.find('.feedback-number'),
				_this = this,
				singularPlural,
				message,
				inclusiveMessage,
				themeLink,
				unitLink,
				number;

		this.$grid.on( 'triggerIsotope', function ( e, data ) {
			doFeedback( data );
		} );

		function doFeedback( data ) {

			if ( data === undefined ) {
				data = {};
			}

			// if no filters, reset filters
			if ( data.filters === undefined ) {
				data.filters = _this._resetFilterQueue();
			}

			themeLink = '<a href="' + _this.filterQueue.theme.permalink + '">' + _this.filterQueue.theme.name + '</a>';
			unitLink = '<a href="' + _this.filterQueue.unit.permalink + '">' + _this.filterQueue.unit.name + '</a>';

			// get filtered number from isotope
			// subtract 1 to account for toolbox (an isotope item)
			number = _this.$grid.data('isotope').$filteredAtoms.length - 1;

			singularPlural = number === 1 ? 'member is' : 'are';
			message = 'Faculty ' + singularPlural + ' working';
			inclusiveMessage = 'College of the Environment Faculty Profiles';

			// is 'all themes' selected?
			if ( _this.filterQueue.theme.slug === 'theme-all' ) {

				// are we in the form view?
				if ( _this.$toolbox.hasClass('show-tools') ) {

					// is 'all units' selected?
					if ( _this.filterQueue.unit.slug === 'unit-all' ) {

						message = inclusiveMessage;

					} else {

						// only show unit message
						message += ' in ' + unitLink;
					}

				} else {

					// theme roller view
					message = inclusiveMessage;
				}

			} else {

				// single theme is selected
				message += ' on ' + themeLink;

				// are we in the form view?
				if ( _this.$toolbox.hasClass('show-tools') ) {

					// is a single unit selected ('all units' not selected)?
					if ( _this.filterQueue.unit.slug !== 'unit-all' ) {

						// add unit message
						message += ' in ' + unitLink;
					}

				}
			}

			// search message overrides anything else
			if ( data.search !== undefined ) {

				singularPlural = number === 1 ? 'Faculty member was' : 'Faculty were';

				message = singularPlural + ' found matching "<em>' + data.search.keywords + '</em>"';
			}

			$number.text( number );
			$message.html( message );

		}

	};

	/**
	 * Respond to form select changes
	 *
	 * Subscribed: 
	 * $themeSelect.chosen().change();
	 * $unitSelect.chosen().change();
	 */
	$.CoEnvFaculty.prototype._handleSelects = function () {

		var _this = this,
				$option,
				data = {},
				chosenArgs,
				name;

		chosenArgs = {
			disable_search: true,
			allow_single_deselect: true
		};

		this.$themeSelect.chosen( chosenArgs ).change( function ( e, obj ) {

			// exit if toolbox is not showing form tools
			if ( !_this.$toolbox.hasClass('show-tools') ) {
				return false;
			}

			if ( obj === undefined ) {
				$option = _this.$themeSelect.find('option').first();
				name = _this.$themeSelect.attr('data-placeholder');
			} else {
				$option = _this.$themeSelect.find( 'option[value="' + obj.selected + '"]' );
				name = $option.text();
			}

			// 'x' was clicked to reset select
			data.filters = {
				theme: {
					name: name,
					slug: $option.attr('value'),
					permalink: $option.attr('data-permalink')
				}
			};

			_this._resetSearch();
			_this.$grid.trigger( 'triggerIsotope', [ data ] );
		} );

		this.$unitSelect.chosen( chosenArgs ).change( function ( e, obj ) {

			if ( obj === undefined ) {
				$option = _this.$unitSelect.find('option').first();
				name = _this.$unitSelect.attr('data-placeholder');
			} else {
				$option = _this.$unitSelect.find( 'option[value="' + obj.selected + '"]' );
				name = $option.text();
			}

			// 'x' was clicked to reset select
			data.filters = {
				unit: {
					name: name,
					slug: $option.attr('value'),
					permalink: $option.attr('data-permalink')
				}
			};

			_this._resetSearch();
			_this.$grid.trigger( 'triggerIsotope', [ data ] );

		} );

		this.$toolbox.on( 'rollerView', function ( e, data ) {
			_this._resetSelects();
		} );

		// update theme select when an item is selected in roller
		this.$roller.on( 'rollerChange', function ( e, data ) {
			_this.$themeSelect.find('options[value="selected"]').removeAttr('selected');
			_this.$themeSelect.find('option[value="' + data.filters.theme.slug + '"]').attr('selected', 'selected');
			_this.$themeSelect.trigger('liszt:updated');
		} );

	};

	/**
	 * Reset selects
	 */
	$.CoEnvFaculty.prototype._resetSelects = function () {
		this.$themeSelect.find('option[value="selected"]').removeAttr('selected');
		this.$themeSelect.find('option').first().attr( 'selected', 'selected' );
		this.$themeSelect.trigger('liszt:updated');

		this.$unitSelect.find('option[value="selected"]').removeAttr('selected');
		this.$unitSelect.find('option').first().attr( 'selected', 'selected' );
		this.$unitSelect.trigger('liszt:updated');
	};

	/**
	 * Update selects
	 */
	$.CoEnvFaculty.prototype._updateSelects = function () {
		this.$themeSelect.find('option[value="selected"]').removeAttr('selected');
		this.$themeSelect.find('option[value="' + this.filterQueue.theme.slug + '"]').attr( 'selected', 'selected' );
		this.$themeSelect.trigger('liszt:updated');

		this.$unitSelect.find('option[value="selected"]').removeAttr('selected');
		this.$unitSelect.find('option[value="' + this.filterQueue.unit.slug + '"]').attr( 'selected', 'selected' );
		this.$unitSelect.trigger('liszt:updated');
	};

	/**
	 * Handle search input
	 */
	$.CoEnvFaculty.prototype._handleSearch = function () {
		var _this = this,
				searchData,
				searchResponse,
				memberIDs,
				data;

		this.$form.on( 'submit', function ( e ) {
			e.preventDefault();

			// reset selects
			_this._resetSelects();

			// reset filterQueue
			_this.filterQueue = _this._resetFilterQueue();

			searchData = {
				action: 'coenv_member_api_search',
				data: _this.$searchField.val()
			};

			$.post( themeVars.ajaxurl, searchData, function ( response ) {
				searchResponse = $.parseJSON( response );

				if ( !searchResponse.results.length ) {

					memberIDs = [ 'search-*' ];
				} else {

					// map memberIDs
					memberIDs = $.map( searchResponse.results, function ( value ) {
						return 'search-' + value.ID;
					} );
				}

				// run isotope, filter by member id
				data = {
					search: {
						ids: memberIDs,
						keywords: searchResponse.message
					}
				};

				_this.$grid.trigger( 'triggerIsotope', [ data ] );

			} );
		} );

	};

	/**
	 * Reset search field
	 */
	$.CoEnvFaculty.prototype._resetSearch = function () {
		this.$searchField.val('');
	};

	/**
	 * Reset filterQueue
	 * Keeps track of current active theme and unit filters
	 */
	$.CoEnvFaculty.prototype._resetFilterQueue = function ( hash ) {

		var hashes,
				$themeOption,
				$unitOption;

		$themeOption = this.$themeSelect.find('option').first();
		$unitOption = this.$unitSelect.find('option').first();

		return {
			theme: {
				name: $themeOption.text(),
				slug: $themeOption.attr('value'),
				permalink: '#'
			},
			unit: {
				name: $unitOption.text(),
				slug: $unitOption.attr('value'),
				permalink: '#'
			}
		};
	};

	/**
	 * Modify Isotope's masonry layout mode to use getSize() for more precise measurements
	 */
	$.CoEnvFaculty.prototype._isotopeModifyMasonry = function () {

		$.Isotope.prototype._masonryLayout = function ( $elems ) {
			var instance = this,
					props = instance.masonry;

			$elems.each(function(){
				var $this  = $(this),
						brickSize = getSize( $this[0] ),
						//how many columns does this brick span
						colSpan = Math.ceil( brickSize.width / props.columnWidth );

				colSpan = Math.min( colSpan, props.cols );

				if ( colSpan === 1 ) {
					// if brick spans only one column, just like singleMode
					instance._masonryPlaceBrick( $this, props.colYs );
				} else {
					// brick spans more than one column
					// how many different places could this brick fit horizontally
					var groupCount = props.cols + 1 - colSpan,
							groupY = [],
							groupColY,
							i;

					// for each group potential horizontal position
					for ( i=0; i < groupCount; i++ ) {
						// make an array of colY values for that one group
						groupColY = props.colYs.slice( i, i+colSpan );
						// and get the max value of the array
						groupY[i] = Math.max.apply( Math, groupColY );
					}

					instance._masonryPlaceBrick( $this, groupY );
				}
			});
		};

		$.Isotope.prototype._masonryPlaceBrick = function ( $brick, setY ) {
			// get the minimum Y value from the columns
			var minimumY = Math.min.apply( Math, setY ),
					brickSize = getSize( $brick[0] ),
					shortCol = 0;

			// Find index of short column, the first from the left
			for (var i=0, len = setY.length; i < len; i++) {
				if ( setY[i] === minimumY ) {
					shortCol = i;
					break;
				}
			}

			// position the brick
			var x = this.masonry.columnWidth * shortCol,
					y = minimumY;
			this._pushPosition( $brick, x, y );

			// apply setHeight to necessary columns
			var setHeight = minimumY + brickSize.height,
					setSpan = this.masonry.cols + 1 - len;
			for ( i=0; i < setSpan; i++ ) {
				this.masonry.colYs[ shortCol + i ] = setHeight;
			}
		};

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

	$.CoEnvFaculty.prototype.option = function( key ){
		if ( $.isPlainObject( key ) ){
			this.options = $.extend(true, this.options, key);
		}
	};

	$.fn.coenvfaculty = function( options ) {
		if ( typeof options === 'string' ) {
			// call method
			var args = Array.prototype.slice.call( arguments, 1 );

			this.each(function() {
				var instance = $.data( this, 'coenvfaculty' );
				if ( !instance ) {
					console.log( 'error', 'cannot call methods on coenvfaculty prior to initialization; ' +
						'attempted to call method "' + options + '"' );
					return;
				}
				if ( !$.isFunction( instance[options] ) || options.charAt(0) === '_' ) {
					console.log( 'error', 'no such method "' + options + '" for coenvfaculty instance' );
					return;
				}

				// apply method
				instance[ options ].apply( instance, args );
			});
		} else {
			this.each(function() {
				var instance = $.data( this, 'coenvfaculty' );
				if ( instance ){
					// apply options & init
					instance.option( options || {} );
					instance._init();
				} else {
					// initialize new instance
					$.data( this, 'coenvfaculty', new $.CoEnvFaculty( options, this ) );
				}
			});
		}
		return this;
	};

})(jQuery, window, document);