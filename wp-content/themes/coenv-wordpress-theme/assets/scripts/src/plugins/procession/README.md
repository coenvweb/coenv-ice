# jQuery Procession
A jQuery plugin for sequential storytelling.

## Intro
Procession is a sequential layout plugin for jQuery. Essentially, it takes a collection of items of varying size, and loops them in a horizontal or vertical strip.

## Features
* **Flexible**: supports items of varying size and shape.
* **Responsive**: responds to changes in viewport size.
* **Progressively enhanced**: smooth animations fallback to jQuery animations for older browsers.

## Basic instructions
Requires jQuery 1.8 or above.

### Markup
Create a containing element with a group of similar child items. You may use your own class names.
	
	<div class="container">
		<div class="item"></div>
		<div class="item"></div>
		<div class="item"></div>
	</div>

Add jQuery and the Procession script, before your closing ``</body>`` tag.
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="/path/to/jquery.procession.min.js"></script>

### CSS
Specify item dimensions in your stylesheet. If you're depending on images or other loaded content to size your items, you'll need to fire .procession() after loading those items. [imagesLoaded()](https://github.com/desandro/imagesloaded) is a great plugin for checking that images are loaded.

	.item {
		margin: 10px;
		width: 100px;
		height: 100px;
		background: orange;
	}
	

### JavaScript
Just call procession on the containing element once the document is ready.
	
	$(function() {
		$('.container').procession();
	});

## Options
You can set options like so:

	$('.container').procession({
		itemSelector: '.item',
		autoHeight: false,
		keyNav: true
	});

##### itemSelector
Explicitly select items for Procession to act on. This is required if ``innerSelector`` is set.

##### innerSelector
Set this if you've added your own inner element *within* the containing element, wrapping your items. If you set this, you must also select your items with ``itemSelector``.

##### containerClass
Overrides the default class applied to the containing element.

	containerClass: 'procession-wrapper'

##### containerStyles
Overrides the default styles applied to the containing element.

	containerStyles: {
		position: 'relative',
		overflow: 'hidden'	
	}

##### innerClass
Overrides the default class applied to the inner element. If you've set ``innerSelector`` (see above), there is no need to change this.

	innerClass: 'procession-inner'

##### innerStyles
Overrides the default styles applied to the inner element.

	innerStyles {
		position: 'relative',
		display: 'block'
	}

##### sliderClass
Overrides the default class applied to the slider element. There is usually no need to change this.

	sliderClass: 'procession-slider

##### sliderStyles
Overrides the default styles applied to the slider element.

	sliderStyles: {
		position: 'absolute',
		width: '100%'
	}

##### itemClass
Overrides the default class applied to individual items.

	itemClass: 'procession-item'

##### itemStyle
Overrides the default styles applied to individual items.

	itemStyles: {
		position: 'absolute',
		visibility	: 'visible'
	}

##### transitionDuration
The animation time in milliseconds.

	transitionDuration: 400

##### easing
The easing style. Include the jQuery Easing plugin for more options.

	easing: 'ease'

##### autoHeight
If set to true (default), the containing element will be resized to fit the tallest item.

	autoHeight: true

##### keyNav
If set to true, Procession will respond to right and left keys.

	keyNav: false

##### clickNav
If set to true (default), Procession will respond to item clicks.

	clickNav: true

##### autoAdvance
If enabled, items will auto-advance at the specified framerate.

	autoAdvance: {
		enable: false,
		fps: 0.25,
		direction: 'forward',
		pauseOnHover: true,
		pauseOnClick: false
	}

##### verticalAlign
If set to 'middle': (default) items will be vertically aligned within the inner element. If set to 'bottom': items will be vertically aligned to the bottom of the inner element.

	verticalAlign: 'middle'

## Useful techniques
### Layout relative to centered elements
By default, items will be arranged so that they span the width of the containing element, with the first item set against the far left side. It may be desirable to position the items relative to a centered element within your container.

#### Markup
Here we've wrapped all items in an element with the class ``.inner``.

	<div class="container">
		<div class="inner">
			<div class="item"></div>
			<div class="item"></div>
			<div class="item"></div>
		</div>
	</div>

#### Styles
We center the ``.inner`` element within ``.container``, and adjust its width.

	.inner {
		margin: 0 auto;
		width: 50%;	
	}

Now, your elements will be oriented relative to the ``.inner`` element, even when you resize the viewport.

### Perfectly centered layout
...


















