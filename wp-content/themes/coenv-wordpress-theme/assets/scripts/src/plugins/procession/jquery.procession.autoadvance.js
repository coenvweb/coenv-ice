// AutoAdvance module
// =========================================================================
$.Procession.prototype._autoAdvance = function () {

	if ( !this.options.autoAdvance.enable ) {
		return;
	}

	// convert frames per second to milliseconds
	//this.options.autoAdvance.fps = 1000 / this.options.autoAdvance.fps;

	var _this = this,
			direction = this.options.autoAdvance.direction;

	function animate() {
		if ( direction === 'forward' ) {
			_this._activate( _this.activeItem.next() );
		} else if ( direction === 'reverse' ) {
			_this._activate( _this.activeItem.prev() );
		}
	}

	this.autoAdvance = setInterval( animate, this.options.autoAdvance.delay );

	if ( this.options.autoAdvance.pauseOnClick ) {
		this.slider.on( 'click', function () {
			clearInterval( _this.autoAdvance );
		} );
	}

	if ( this.options.autoAdvance.pauseOnHover ) {
		this.slider.on( 'mouseenter', function () {
			clearInterval( _this.autoAdvance );
		} );
		this.slider.on( 'mouseleave', function () {
			_this.autoAdvance = setInterval( animate, _this.options.autoAdvance.delay );
		} );
	}
};