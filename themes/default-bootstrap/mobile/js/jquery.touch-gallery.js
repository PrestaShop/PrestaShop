/*!
 * NETEYE Activity Indicator jQuery Plugin
 *
 * Copyright (c) 2010 NETEYE GmbH
 * Licensed under the MIT license
 *
 * Author: Felix Gnass [fgnass at neteye dot de]
 * Version: 1.0.0
 */
 
/**
 * Plugin that renders a customisable activity indicator (spinner) using SVG or VML.
 */
(function($) {

	$.fn.activity = function(opts) {
		this.each(function() {
			var $this = $(this);
			var el = $this.data('activity');
			if (el) {
				clearInterval(el.data('interval'));
				el.remove();
				$this.removeData('activity');
			}
			if (opts !== false) {
				opts = $.extend({color: $this.css('color')}, $.fn.activity.defaults, opts);
				
				el = render($this, opts).css('position', 'absolute').prependTo(opts.outside ? 'body' : $this);
				var h = $this.outerHeight() - el.height();
				var w = $this.outerWidth() - el.width();
				var margin = {
					top: opts.valign == 'top' ? opts.padding : opts.valign == 'bottom' ? h - opts.padding : Math.floor(h / 2),
					left: opts.align == 'left' ? opts.padding : opts.align == 'right' ? w - opts.padding : Math.floor(w / 2)
				};
				var offset = $this.offset();
				if (opts.outside) {
					el.css({top: offset.top + 'px', left: offset.left + 'px'});
				}
				else {
					margin.top -= el.offset().top - offset.top;
					margin.left -= el.offset().left - offset.left;
				}
				el.css({marginTop: margin.top + 'px', marginLeft: margin.left + 'px'});
				animate(el, opts.segments, Math.round(10 / opts.speed) / 10);
				$this.data('activity', el);
			}
		});
		return this;
	};
	
	$.fn.activity.defaults = {
		segments: 12,
		space: 3,
		length: 7,
		width: 4,
		speed: 1.2,
		align: 'center',
		valign: 'center',
		padding: 4
	};
	
	$.fn.activity.getOpacity = function(opts, i) {
		var steps = opts.steps || opts.segments-1;
		var end = opts.opacity !== undefined ? opts.opacity : 1/steps;
		return 1 - Math.min(i, steps) * (1 - end) / steps;
	};
	
	/**
	 * Default rendering strategy. If neither SVG nor VML is available, a div with class-name 'busy' 
	 * is inserted, that can be styled with CSS to display an animated gif as fallback.
	 */
	var render = function() {
		return $('<div>').addClass('busy');
	};
	
	/**
	 * The default animation strategy does nothing as we expect an animated gif as fallback.
	 */
	var animate = function() {
	};
	
	/**
	 * Utility function to create elements in the SVG namespace.
	 */
	function svg(tag, attr) {
		var el = document.createElementNS("http://www.w3.org/2000/svg", tag || 'svg');
		if (attr) {
			$.each(attr, function(k, v) {
				el.setAttributeNS(null, k, v);
			});
		}
		return $(el);
	}
	
	if (document.createElementNS && document.createElementNS( "http://www.w3.org/2000/svg", "svg").createSVGRect) {
	
		// =======================================================================================
		// SVG Rendering
		// =======================================================================================
		
		/**
		 * Rendering strategy that creates a SVG tree.
		 */
		render = function(target, d) {
			var innerRadius = d.width*2 + d.space;
			var r = (innerRadius + d.length + Math.ceil(d.width / 2) + 1);
			
			var el = svg().width(r*2).height(r*2);
			
			var g = svg('g', {
				'stroke-width': d.width, 
				'stroke-linecap': 'round', 
				stroke: d.color
			}).appendTo(svg('g', {transform: 'translate('+ r +','+ r +')'}).appendTo(el));
			
			for (var i = 0; i < d.segments; i++) {
				g.append(svg('line', {
					x1: 0, 
					y1: innerRadius, 
					x2: 0, 
					y2: innerRadius + d.length, 
					transform: 'rotate(' + (360 / d.segments * i) + ', 0, 0)',
					opacity: $.fn.activity.getOpacity(d, i)
				}));
			}
			return $('<div>').append(el).width(2*r).height(2*r);
		};
				
		// Check if Webkit CSS animations are available, as they work much better on the iPad
		// than setTimeout() based animations.
		
		if (document.createElement('div').style.WebkitAnimationName !== undefined) {

			var animations = {};
		
			/**
			 * Animation strategy that uses dynamically created CSS animation rules.
			 */
			animate = function(el, steps, duration) {
				if (!animations[steps]) {
					var name = 'spin' + steps;
					var rule = '@-webkit-keyframes '+ name +' {';
					for (var i=0; i < steps; i++) {
						var p1 = Math.round(100000 / steps * i) / 1000;
						var p2 = Math.round(100000 / steps * (i+1) - 1) / 1000;
						var value = '% { -webkit-transform:rotate(' + Math.round(360 / steps * i) + 'deg); }\n';
						rule += p1 + value + p2 + value; 
					}
					rule += '100% { -webkit-transform:rotate(100deg); }\n}';
					document.styleSheets[0].insertRule(rule);
					animations[steps] = name;
				}
				el.css('-webkit-animation', animations[steps] + ' ' + duration +'s linear infinite');
			};
		}
		else {
		
			/**
			 * Animation strategy that transforms a SVG element using setInterval().
			 */
			animate = function(el, steps, duration) {
				var rotation = 0;
				var g = el.find('g g').get(0);
				el.data('interval', setInterval(function() {
					g.setAttributeNS(null, 'transform', 'rotate(' + (++rotation % steps * (360 / steps)) + ')');
				},  duration * 1000 / steps));
			};
		}
		
	}
	else {
		
		// =======================================================================================
		// VML Rendering
		// =======================================================================================
		
		var s = $('<shape>').css('behavior', 'url(#default#VML)').appendTo('body');
			
		if (s.get(0).adj) {
		
			// VML support detected. Insert CSS rules for group, shape and stroke.
			var sheet = document.createStyleSheet();
			$.each(['group', 'shape', 'stroke'], function() {
				sheet.addRule(this, "behavior:url(#default#VML);");
			});
			
			/**
			 * Rendering strategy that creates a VML tree. 
			 */
			render = function(target, d) {
			
				var innerRadius = d.width*2 + d.space;
				var r = (innerRadius + d.length + Math.ceil(d.width / 2) + 1);
				var s = r*2;
				var o = -Math.ceil(s/2);
				
				var el = $('<group>', {coordsize: s + ' ' + s, coordorigin: o + ' ' + o}).css({top: o, left: o, width: s, height: s});
				for (var i = 0; i < d.segments; i++) {
					el.append($('<shape>', {path: 'm ' + innerRadius + ',0  l ' + (innerRadius + d.length) + ',0'}).css({
						width: s,
						height: s,
						rotation: (360 / d.segments * i) + 'deg'
					}).append($('<stroke>', {color: d.color, weight: d.width + 'px', endcap: 'round', opacity: $.fn.activity.getOpacity(d, i)})));
				}
				return $('<group>', {coordsize: s + ' ' + s}).css({width: s, height: s, overflow: 'hidden'}).append(el);
			};
		
			/**
		     * Animation strategy that modifies the VML rotation property using setInterval().
		     */
			animate = function(el, steps, duration) {
				var rotation = 0;
				var g = el.get(0);
				el.data('interval', setInterval(function() {
					g.style.rotation = ++rotation % steps * (360 / steps);
				},  duration * 1000 / steps));
			};
		}
		$(s).remove();
	}

})(jQuery);
/*!
 * NETEYE Activity Indicator jQuery Plugin
 *
 * Copyright (c) 2010 NETEYE GmbH
 * Licensed under the MIT license
 *
 * Author: Felix Gnass [fgnass at neteye dot de]
 * Version: 1.0.0
 */
 
/**
 * Plugin that renders a customisable activity indicator (spinner) using SVG or VML.
 */
(function($) {

	$.fn.activity = function(opts) {
		this.each(function() {
			var $this = $(this);
			var el = $this.data('activity');
			if (el) {
				clearInterval(el.data('interval'));
				el.remove();
				$this.removeData('activity');
			}
			if (opts !== false) {
				opts = $.extend({color: $this.css('color')}, $.fn.activity.defaults, opts);
				
				el = render($this, opts).css('position', 'absolute').prependTo(opts.outside ? 'body' : $this);
				var margin = {
					top: Math.floor(($this.outerHeight() - el.height()) / 2),
					left: Math.floor(($this.outerWidth() - el.width()) / 2)
				};
				var offset = $this.offset();
				if (opts.outside) {
					el.css({top: offset.top + 'px', left: offset.left + 'px'});
				}
				else {
					margin.top -= el.offset().top - offset.top;
					margin.left -= el.offset().left - offset.left;
				}
				el.css({marginTop: margin.top + 'px', marginLeft: margin.left + 'px'});
				animate(el, opts.segments, Math.round(10 / opts.speed) / 10);
				$this.data('activity', el);
			}
		});
		return this;
	};
	
	$.fn.activity.defaults = {
		segments: 12,
		space: 3,
		length: 7,
		width: 4,
		speed: 1.2
	};
	
	$.fn.activity.getOpacity = function(opts, i) {
		var steps = opts.steps || opts.segments-1;
		var end = opts.opacity !== undefined ? opts.opacity : 1/steps;
		return 1 - Math.min(i, steps) * (1 - end) / steps;
	};
	
	/**
	 * Default rendering strategy. If neither SVG nor VML is available, a div with class-name 'busy' 
	 * is inserted, that can be styled with CSS to display an animated gif as fallback.
	 */
	var render = function() {
		return $('<div>').addClass('busy');
	};
	
	/**
	 * The default animation strategy does nothing as we expect an animated gif as fallback.
	 */
	var animate = function() {
	};
	
	/**
	 * Utility function to create elements in the SVG namespace.
	 */
	function svg(tag, attr) {
		var el = document.createElementNS("http://www.w3.org/2000/svg", tag || 'svg');
		if (attr) {
			$.each(attr, function(k, v) {
				el.setAttributeNS(null, k, v);
			});
		}
		return $(el);
	}
	
	if (document.createElementNS && document.createElementNS( "http://www.w3.org/2000/svg", "svg").createSVGRect) {
	
		// =======================================================================================
		// SVG Rendering
		// =======================================================================================
		
		/**
		 * Rendering strategy that creates a SVG tree.
		 */
		render = function(target, d) {
			var innerRadius = d.width*2 + d.space;
			var r = (innerRadius + d.length + Math.ceil(d.width / 2) + 1);
			
			var el = svg().width(r*2).height(r*2);
			
			var g = svg('g', {
				'stroke-width': d.width, 
				'stroke-linecap': 'round', 
				stroke: d.color
			}).appendTo(svg('g', {transform: 'translate('+ r +','+ r +')'}).appendTo(el));
			
			for (var i = 0; i < d.segments; i++) {
				g.append(svg('line', {
					x1: 0, 
					y1: innerRadius, 
					x2: 0, 
					y2: innerRadius + d.length, 
					transform: 'rotate(' + (360 / d.segments * i) + ', 0, 0)',
					opacity: $.fn.activity.getOpacity(d, i)
				}));
			}
			return $('<div>').append(el).width(2*r).height(2*r);
		};
				
		// Check if Webkit CSS animations are available, as they work much better on the iPad
		// than setTimeout() based animations.
		
		if (document.createElement('div').style.WebkitAnimationName !== undefined) {

			var animations = {};
		
			/**
			 * Animation strategy that uses dynamically created CSS animation rules.
			 */
			animate = function(el, steps, duration) {
				if (!animations[steps]) {
					var name = 'spin' + steps;
					var rule = '@-webkit-keyframes '+ name +' {';
					for (var i=0; i < steps; i++) {
						var p1 = Math.round(100000 / steps * i) / 1000;
						var p2 = Math.round(100000 / steps * (i+1) - 1) / 1000;
						var value = '% { -webkit-transform:rotate(' + Math.round(360 / steps * i) + 'deg); }\n';
						rule += p1 + value + p2 + value; 
					}
					rule += '100% { -webkit-transform:rotate(100deg); }\n}';
					document.styleSheets[0].insertRule(rule);
					animations[steps] = name;
				}
				el.css('-webkit-animation', animations[steps] + ' ' + duration +'s linear infinite');
			};
		}
		else {
		
			/**
			 * Animation strategy that transforms a SVG element using setInterval().
			 */
			animate = function(el, steps, duration) {
				var rotation = 0;
				var g = el.find('g g').get(0);
				el.data('interval', setInterval(function() {
					g.setAttributeNS(null, 'transform', 'rotate(' + (++rotation % steps * (360 / steps)) + ')');
				},  duration * 1000 / steps));
			};
		}
		
	}
	else {
		
		// =======================================================================================
		// VML Rendering
		// =======================================================================================
		
		var s = $('<shape>').css('behavior', 'url(#default#VML)').appendTo('body');
			
		if (s.get(0).adj) {
		
			// VML support detected. Insert CSS rules for group, shape and stroke.
			var sheet = document.createStyleSheet();
			$.each(['group', 'shape', 'stroke'], function() {
				sheet.addRule(this, "behavior:url(#default#VML);");
			});
			
			/**
			 * Rendering strategy that creates a VML tree. 
			 */
			render = function(target, d) {
			
				var innerRadius = d.width*2 + d.space;
				var r = (innerRadius + d.length + Math.ceil(d.width / 2) + 1);
				var s = r*2;
				var o = -Math.ceil(s/2);
				
				var el = $('<group>', {coordsize: s + ' ' + s, coordorigin: o + ' ' + o}).css({top: o, left: o, width: s, height: s});
				for (var i = 0; i < d.segments; i++) {
					el.append($('<shape>', {path: 'm ' + innerRadius + ',0  l ' + (innerRadius + d.length) + ',0'}).css({
						width: s,
						height: s,
						rotation: (360 / d.segments * i) + 'deg'
					}).append($('<stroke>', {color: d.color, weight: d.width + 'px', endcap: 'round', opacity: $.fn.activity.getOpacity(d, i)})));
				}
				return $('<group>', {coordsize: s + ' ' + s}).css({width: s, height: s, overflow: 'hidden'}).append(el);
			};
		
			/**
		     * Animation strategy that modifies the VML rotation property using setInterval().
		     */
			animate = function(el, steps, duration) {
				var rotation = 0;
				var g = el.get(0);
				el.data('interval', setInterval(function() {
					g.style.rotation = ++rotation % steps * (360 / steps);
				},  duration * 1000 / steps));
			};
		}
		$(s).remove();
	}

})(jQuery);
/*!
 * NETEYE Transform & Transition Plugin
 *
 * Copyright (c) 2010 NETEYE GmbH
 * Licensed under the MIT license
 *
 * Author: Felix Gnass [fgnass at neteye dot de]
 * Version: 1.0.0
 */
(function($) {
	
	// ==========================================================================================
	// Private functions
	// ==========================================================================================
	
	var props = (function() {
	
		var prefixes = ['Webkit', 'Moz', 'O'];
		
		var style = document.createElement('div').style;
			  
		function findProp(name) {
			var result = '';
			if (style[name] !== undefined) {
				return name;
			}
			$.each(prefixes, function() {
				var p = this + name.charAt(0).toUpperCase() + name.substring(1);
				if (style[p] !== undefined) {
					result = p;
					return false;
				}
			});
			return result;
		}
		
		var result = {};
		$.each(['transitionDuration', 'transitionProperty', 'transform', 'transformOrigin'], function() {
			result[this] = findProp(this);
		});
		return result;
		
	})();
	
	var supports3d = (function() {
		var s = document.createElement('div').style;
		try {
			s[props.transform] = 'translate3d(0,0,0)';
			return s[props.transform].length > 0;
		}
		catch (ex) {
			return false;
		}
	})();
	
	
	function transform(el, commands) {
		var t = el.data('transform');
		if (!t) {
			t = new Transformation();
			el.data('transform', t);
		}
		if (commands !== undefined) {
			if (commands === false || commands.reset) {
				t.reset();
			}
			else {
				t.exec(commands);
			}
		}
		return t;
	}
	
	/**
	 * Class that keeps track of numeric values and converts them into a string representation
	 * that can be used as value for the -webkit-transform property. TransformFunctions are used
	 * internally by the Transformation class.
	 *
	 * // Example:
	 *
	 * var t = new TransformFunction('translate3d({x}px,{y}px,{z}px)', {x:0, y:0, z:0});
	 * t.x = 23;
	 * console.assert(t.format() == 'translate3d(23px,0px,0px)')
	 */
	function TransformFunction(pattern, defaults) {
		function fillIn(pattern, data) {
			return pattern.replace(/\{(\w+)\}/g, function(s, p1) { return data[p1]; });
		}
		this.reset = function() {
			$.extend(this, defaults);
		};
		this.format = function() {
			return fillIn(pattern, this);
		};
		this.reset();
	}
	
	/**
	 * Class that encapsulates the state of multiple TransformFunctions. The state can be modified
	 * using commands and converted into a string representation that can be used as CSS value.
	 * The class is used internally by the transform plugin.
	 */
	function Transformation() {
		var fn = {
			translate: new TransformFunction('translate({x}px,{y}px)', {x:0, y:0}),
			scale: new TransformFunction('scale({x},{y})', {x:1, y:1}),
			rotate: new TransformFunction('rotate({deg}deg)', {deg:0})
		};
		
		if (supports3d) {
			// Use 3D transforms for better performance
			fn.translate = new TransformFunction('translate3d({x}px,{y}px,0px)', {x:0, y:0});
			fn.scale = new TransformFunction('scale3d({x},{y},1)', {x:1, y:1});
		}	
		
		var commands = {
			rotate: function(deg) {
				fn.rotate.deg = deg;
			},
			rotateBy: function(deg) {
				fn.rotate.deg += deg;
			},
			scale: function(s) {
				if (typeof s == 'number') {
					s = {x: s, y: s};
				}
				fn.scale.x = s.x;
				fn.scale.y = s.y;
			},
			scaleBy: function(s) {
				if (typeof s == 'number') {
					s = {x: s, y: s};
				}
				fn.scale.x *= s.x;
				fn.scale.y *= s.y;
			},
			translate: function(s) {
				var t = fn.translate;
				if (!s) {
					s = {x: 0, y: 0};
				}
				t.x = (s.x !== undefined) ? parseInt(s.x, 10) : t.x;
				t.y = (s.y !== undefined) ? parseInt(s.y, 10) : t.y;
			},
			translateBy: function(s) {
				var t = fn.translate;
				t.x += parseInt(s.x, 10) || 0;
				t.y += parseInt(s.y, 10) || 0;
			}
		};
		this.fn = fn;
		this.exec = function(cmd) {
			for (var n in cmd) {
				if (commands[n]) {
					commands[n](cmd[n]);
				}
			}
		};
		this.reset = function() {
			$.each(fn, function() {
				this.reset();
			});
		};
		this.format = function() {
			var s = '';
			$.each(fn, function(k, v) {
				s += v.format() + ' ';
			});
			return s;
		};
	}
	
	// ==========================================================================================
	// Public API
	// ==========================================================================================
	
	$.fn.transform = function(opts) {
		var result = this;
		if ($.fn.transform.supported) {
			this.each(function() {
				var $this = $(this);
				var t = transform($this, opts);
				if (opts === undefined) {
					result = t.fn;
					return false;
				}
				var origin = opts && opts.origin ? opts.origin : '0 0';
				$this.css(props.transitionDuration, '0s')
					.css(props.transformOrigin, origin)
					.css(props.transform, t.format());
			});
		}
		return result;
	};
	
	$.fn.transform.supported = !!props.transform;
	
	$.fn.transition = function(css, opts) {
	
		opts = $.extend({
			delay: 0,
			duration: 0.4
		}, opts);
		
		var property = '';
		$.each(css, function(k, v) {
			property += k + ',';
		});

		this.each(function() {
			var $this = $(this);
			
			if (!$.fn.transition.supported) {
				$this.css(css);
				if (opts.onFinish) {
					$.proxy(opts.onFinish, $this)();
				}
				return;
			}
			
			var _duration = $this.css(props.transitionDuration);		
			
			function apply() {
				$this.css(props.transitionProperty, property).css(props.transitionDuration, opts.duration + 's');
				
				$this.css(css);
				if (opts.duration > 0) {
					$this.one('webkitTransitionEnd oTransitionEnd transitionend', afterCompletion);
				}
				else {
					setTimeout(afterCompletion, 1);					
				}
			}
			
			function afterCompletion() {
				$this.css(props.transitionDuration, _duration);
					
				if (opts.onFinish) {
					$.proxy(opts.onFinish, $this)();
				}
			}
			
			if (opts.delay > 0) {
				setTimeout(apply, opts.delay);
			}
			else {
				apply();
			}
		});
		return this;
	};
	
	$.fn.transition.supported = !!props.transitionProperty;
	
	$.fn.transformTransition = function(opts) {
		opts = $.extend({
			origin: '0 0',
			css: {}
		}, opts);
		var css = opts.css;
		if ($.fn.transform.supported) {
			css[props.transform] = transform(this, opts).format();
			this.css(props.transformOrigin, opts.origin);
		}
		return this.transition(css, opts);
	};
	
})(jQuery);
/*!
 * NETEYE Touch-Gallery jQuery Plugin
 *
 * Copyright (c) 2010 NETEYE GmbH
 * Licensed under the MIT license
 *
 * Author: Felix Gnass [fgnass at neteye dot de]
 * Version: 1.0.0
 */
(function($) {
	
	var mobileSafari = /Mobile.*Safari/.test(navigator.userAgent);
	
	$.fn.touchGallery = function(opts) {
		opts = $.extend({}, $.fn.touchGallery.defaults, opts);
		var thumbs = this;
		this.live('click', function(ev) {
			ev.preventDefault();
			var clickedThumb = $(this);
			if (!clickedThumb.is('.open')) {
				thumbs.addClass('open');
				openGallery(thumbs, clickedThumb, opts);
			}
		});
		return this;
	};
	
	/**
	 * Default options.
	 */
	$.fn.touchGallery.defaults = {
		getSource: function() {
			return this.href;
		}
	};
	
	// ==========================================================================================
	// Private functions
	// ==========================================================================================
		
	/**
	 * Opens the gallery. A spining activity indicator is displayed until the clicked image has
	 * been loaded. When ready, showGallery() is called.
	 */
	function openGallery(thumbs, clickedThumb, opts) {
		clickedThumb.activity();
		var img = new Image();
		img.onload = function() {
			clickedThumb.activity(false);
			showGallery(thumbs, thumbs.index(clickedThumb), this, opts.getSource);
		};
		img.src = $.proxy(opts.getSource, clickedThumb.get(0))();
	}
	
	/**
	 * Creates DOM elements to actually show the gallery.
	 */
	function showGallery(thumbs, index, clickedImage, getSrcCallback) {
		var viewport = fitToView(preventTouch($('<div id="galleryViewport">').css({
			position: 'fixed',
			top: 0,
			left: 0,
			overflow: 'hidden'
		}).transform(false).appendTo('body')));
		
		var stripe = $('<div id="galleryStripe">').css({
			position: 'absolute',
			height: '100%',
			top: 0,
			left: (-index * getInnerWidth()) + 'px'
		}).width(thumbs.length * getInnerWidth()).transform(false).appendTo(viewport);
		
		setupEventListeners(stripe, getInnerWidth(), index, thumbs.length-1);
		
		$(window).bind('orientationchange.gallery', function() {
			fitToView(viewport);
			stripe.find('img').each(centerImage);
		});
		
		thumbs.each(function(i) {
			var page = $('<div>').addClass('galleryPage').css({
				display: 'block',
				position: 'absolute',
				left: i * getInnerWidth() + 'px',
				overflow: 'hidden',
				height: '100%'
			}).width(getInnerWidth()).data('thumbs', thumbs).data('thumb', $(this)).transform(false).appendTo(stripe);
			
			if (i == index) {
				var $img = $(clickedImage).css({position: 'absolute', display: 'block'}).transform(false);
				makeInvisible(centerImage(index, clickedImage, $img)).appendTo(page);
				zoomIn($(this), $img, function() {
					stripe.addClass('ready');
					loadSurroundingImages(index);
				});
				insertShade(viewport);
			}
			else {
				page.activity({color: '#fff'});
				var img = new Image();
				var src = $.proxy(getSrcCallback, this)();
				page.one('loadImage', function() {
					img.src = src;
				});
				img.onload = function() {
					var $this = $(this).css({position: 'absolute', display: 'block'}).transform(false);
					centerImage(i, this, $this).appendTo(page.activity(false));
					page.trigger('loaded');
				};
			}
		});
	}
	
	function hideGallery(stripe) {
		if (stripe.is('.ready') && !stripe.is('.panning')) {
			$('#galleryShade').remove();
			var page = stripe.find('.galleryPage').eq(stripe.data('galleryIndex'));
			page.data('thumbs').removeClass('open');
			var thumb = page.data('thumb');
			stripe.add(window).add(document).unbind('.gallery');
			zoomOut(page.find('img'), thumb, function() {
				makeVisible(thumb).transform(false);
				$('#galleryViewport').remove();
			});
		}
	}
	
	/**
	 * Inserts a black DIV before the given target element and performs an opacity 
	 * transition form 0 to 1.
	 */
	function insertShade(target, onFinish) {
		var el = $('<div id="galleryShade">').css({
			top: 0, left: 0, background: '#000', opacity: 0
		});
		if (mobileSafari) {
			// Make the shade bigger so that it shadows the surface upon rotation
			var l = Math.max(screen.width, screen.height) * (window.devicePixelRatio || 1) + Math.max(getScrollLeft(), getScrollTop()) + 100;
			el.css({position: 'absolute'}).width(l).height(l);
		}
		else {
			el.css({position: 'fixed', width: '100%', height: '100%'});
		}
		el.insertBefore(target)
		.transform(false)
		.transition({opacity: 1}, {delay: 200, duration: 0.8, onFinish: onFinish});
	}
	
	/**
	 * Scales and centers an element according to the dimensions of the given image.
	 * The first argument is ignored, it's just there so that the function can be used with .each()
	 */
	function centerImage(i, img, el) {
		el = el || $(img);
		if (!img.naturalWidth) {
			//Work-around for Opera which doesn't support naturalWidth/Height. This works because
			//the function is invoked once for each image before it is scaled.
			img.naturalWidth = img.width;
			img.naturalHeight = img.height;
		}
		var s = Math.min(getViewportScale(), Math.min(getInnerHeight()/img.naturalHeight, getInnerWidth()/img.naturalWidth));
		el.css({
			top: Math.round((getInnerHeight() - img.naturalHeight * s) / 2) +  'px',
			left: Math.round((getInnerWidth() - img.naturalWidth * s) / 2) +  'px'
		}).width(Math.round(img.naturalWidth * s));
		return el;
	}
	
	/**
	 * Performs a zoom animation from the small to the large element. The large element is scaled 
	 * down and centered over the small element. Then a transition is performed that 
	 * resets the transformation.
	 */
	function zoomIn(small, large, onFinish) {
		var b = bounds(large);
		var t = bounds(small);
		var s = Math.max(t.width / large.width(), t.height / large.height());
		var ox = mobileSafari ? 0 : getScrollLeft();
		var oy = mobileSafari ? 0 : getScrollTop();
		large.transform({
			translate: {
				x: t.left - b.left - ox - Math.round((b.width * s - t.width) / 2), 
				y: t.top - b.top - oy - Math.round((b.height * s - t.height) / 2)
			}, 
			scale: s
		});
		setTimeout(function() {
			makeVisible(large);
			makeInvisible(small);
			large.transformTransition({reset: true, onFinish: onFinish});
		}, 1);
	}
	
	/**
	 * Performs a zoom animation from the large to the small element. Since the small version
	 * may have a different aspect ratio, the large element is wrapped inside a div and clipped
	 * to match the aspect of the small version. The wrapper div is appended to the body, as 
	 * leaving it in place causes strange z-index/flickering issues.
	 */
	function zoomOut(large, small, onFinish) {
		if (large.length === 0 || !$.fn.transition.supported) {
			if (onFinish) {
				onFinish();
			}
			return;
		}
		var b = bounds(large);
		var t = bounds(small);
		
		var w = Math.min(b.height * t.width / t.height, b.width);
		var h = Math.min(b.width * t.height / t.width, b.height);
		
		var s = Math.max(t.width / w, t.height / h);
		
		var div = $('<div>').css({
			overflow: 'hidden',
			position: 'absolute',
			width: w + 'px',
			height: h + 'px',
			top: getScrollTop() + Math.round((getInnerHeight()-h) / 2) + 'px', 
			left: getScrollLeft() + Math.round((getInnerWidth()-w) / 2) + 'px'
		})
		.appendTo('body').append(large.css({
			top: 1-Math.floor((b.height-h) / 2) + 'px', // -1px offset to match Flickr's square crops
			left: -Math.floor((b.width-w) / 2) + 'px'
		}))
		.transform(false);
		
		b = bounds(div);
		
		div.transformTransition({
			translate: {
				x: t.left - b.left - Math.round((w * s - t.width) / 2), 
				y: t.top - b.top - Math.round((h * s - t.height) / 2)
			}, 
			scale: s,
			onFinish: function() {
				onFinish();
				div.remove();
			}
		});
	}
	
	function getPage(i) {
		return $('#galleryStripe .galleryPage').eq(i);
	}
	
	function getThumb(i) {
		return getPage(i).data('thumb');
	}
	
	function loadSurroundingImages(i) {
		var page = getPage(i);
		function triggerLoad() {
			getPage(i-1).add(getPage(i+1)).trigger('loadImage');
		}
		if (page.find('img').length > 0) {
			triggerLoad();
		}
		else {
			page.one('loaded', triggerLoad);
		}
	}
	
	/**
	 * Registers event listeners to enable flicking through the images.
	 */
	function setupEventListeners(el, pageWidth, currentIndex, max) {
		var scale = getViewportScale();
		var xOffset = parseInt(el.css('left'), 10);
		el.data('galleryIndex', currentIndex);
		
		function flick(dir) {
			var i = el.data('galleryIndex');
			makeVisible(getThumb(i));
			i = Math.max(0, Math.min(i + dir, max));
			el.data('galleryIndex', i);
			makeInvisible(getThumb(i));
			
			loadSurroundingImages(i);
			
			if ($.fn.transform.supported) {
				var x = -i * pageWidth - xOffset;
				if (x != el.transform().translate.x) {
					el.addClass('panning').transformTransition({translate: {x: x}, onFinish: function() { this.removeClass('panning'); }});
				}
			}
			else {
				el.css('left', -i * pageWidth + 'px');
			}
		}
		
		$(document).bind('keydown.gallery', function(event) {
			if (event.keyCode == 37) {
				el.trigger('prev');
			}
			else if (event.keyCode == 39) {
				el.trigger('next');
			}
			if (event.keyCode == 27 || event.keyCode == 32) {
				el.trigger('close');
			}
			return false;
		});
		
		el.bind('touchstart', function() {
			$(this).data('pan', {
				startX: event.targetTouches[0].screenX,
				lastX:event.targetTouches[0].screenX,
				startTime: new Date().getTime(),
				startOffset: $(this).transform().translate.x,
				distance: function() {
					return Math.round(scale * (this.startX - this.lastX));
				},
				delta: function() {
					var x = event.targetTouches[0].screenX;
					this.dir = this.lastX > x ? 1 : -1;
					var delta = Math.round(scale * (this.lastX - x));
					this.lastX = x;
					return delta;
				},
				duration: function() {
					return new Date().getTime() - this.startTime;
				}
			});
			return false;
		})
		.bind('touchmove', function() {
			var pan = $(this).data('pan');
			$(this).transform({translateBy: {x: -pan.delta()}});
			return false;
		})
		.bind('touchend', function() {
			var pan = $(this).data('pan');
			if (pan.distance() === 0 && pan.duration() < 500) {
				$(event.target).trigger('click');
			}
			else {
				flick(pan.dir);
			}
			return false;
		})
		.bind('prev', function() {
			flick(-1);
		})
		.bind('next', function() {
			flick(1);
		})
		.bind('click close', function() {
			hideGallery(el);
		});
	}
	
	/**
	 * Sets position and size of the given jQuery object to match the current viewport dimensions.
	 */
	function fitToView(el) {
		if (mobileSafari) {
			el.css({top: getScrollTop() + 'px', left: getScrollLeft() + 'px'});
		}
		return el.width(getInnerWidth()).height(getInnerHeight());
	}
	
	/**
	 * Returns the reciprocal of the current zoom-factor.
	 * @REVISIT Use screen.width / screen.availWidth instead?
	 */
	function getViewportScale() {
		return getInnerWidth() / document.documentElement.clientWidth;
	}
	
	/**
	 * Returns a window property with fallback to a property on the 
	 * documentElement in Internet Explorer.
	 */
	function getWindowProp(name, ie) {
		if (window[name] !== undefined) {
			return window[name];
		}
		var d = document.documentElement;
		if (d && d[ie]) {
			return d[ie];
		}
		return document.body[ie];
	}
	
	function getScrollTop() {
		return getWindowProp('pageYOffset', 'scrollTop');
	}
	
	function getScrollLeft() {
		return getWindowProp('pageXOffset', 'scrollLeft');
	}
	
	function getInnerWidth() {
		return getWindowProp('innerWidth', 'clientWidth');
	}
	
	function getInnerHeight() {
		return getWindowProp('innerHeight', 'clientHeight');
	}
	
	function makeVisible(el) {
		return el.css('visibility', 'visible');
	}
	
	function makeInvisible(el) {
		return el.css('visibility', 'hidden');
	}
	
	function bounds(el) {
		var e = el.get(0);
		if (e && e.getBoundingClientRect) {
			return e.getBoundingClientRect();
		}
		return $.extend({width: el.width(), height: el.height()}, el.offset());
	}
	
	function preventTouch(el) {
		return el.bind('touchstart', function() { return false; });
	}

})(jQuery);
