(function($){
	if($ === undefined) throw 'jQuery is not installed. This is required.';

	var
	singleton, $el,
	optKey = 'llama_config',
	config = {},
	defaultOpts = {
		enabled: false,
		selector: 'header',
		positionCls: 'bottom-right',
		baseCls: 'llamada-gratis',
		simpleDlgBaseCls: 'simple-dialog',
		hashmark: 'launch-llamada-gratis',
		image: null,
		imageTitle: null,
		imageWidth: null,
		imageHeight: null,
		windowHintClose: 'Close tour',
		windowTitle: 'Llamada Gratis',
		windowWidth: '406px',
		windowHeight: '600px',
		url: null
	},
	debounce = function(func, threshold, execAsap) {

		var timeout;

		return function debounced() {
			var obj = this, args = arguments;
			function delayed() {
				if (!execAsap)
					func.apply(obj, args);
				timeout = null;
			}

			if (timeout)
				clearTimeout(timeout);
			else if (execAsap)
				func.apply(obj, args);

			timeout = setTimeout(delayed, threshold || 100);
		};
	}

	function unconfigure(me) {

		if($el !== undefined) {
			$el.remove();
			$el = undefined;
		}

		return me;
	}
	function configure(me, cfg) {

		$.extend(config, cfg);

		// normalize enabled
		config.enabled = cfg.enabled !== undefined ?
				(cfg.enabled === true) :
				config.enabled;

		return me; // return latest config
	}
	function createEl(sel) {
		return $('<div><a><img/><a></div>')
			.appendTo(sel);
	}
	function showCallWindow(tourUrl, baseCls, closeHint, windowTitle,
			windowWidth, windowHeight) {
		var $o, $w, $win = $(window), root = 'html',
		centerWin = function(){
			var
			wX = $win.width(), wY = $win.height(),
			pX = ((wX/2)-($w.width()/2)),
			pY = ((wY/2)-($w.height()/2));

			if(pX < 0) pX = 0;
			if(pY < 0) pY = 0;

			$w.css({
				left: pX,
				top: pY
			});
		},
		closeWin = function(e){
			e.preventDefault();
			$o.remove();
			$w.remove();
			$win.un('resize', centerWin);
		};
		$o = $('<div>').appendTo(root);
		$w = $(['<div class="'+baseCls+'-window fixed">',
		 	'<div class="'+baseCls+'-wrapper">',
		 		'<a class="'+baseCls+'-button"></a>',
		 		'<h3 class="'+baseCls+'-title"></h3>',
		 		'<div class="'+baseCls+'-body-wrapper">',
		 			'<iframe />',
		 		'</div>',
		 	'</div>',
		 '</div>'].join(''))
			.appendTo(root);

		$win.on('resize', debounce(centerWin,150));

		$w.css({
			width: windowWidth,
			height: windowHeight
		});

		centerWin();

		$o.addClass(baseCls + '-overlay').on('click', closeWin);

		$w.find('a').attr({
			title: closeHint,
			href: '#close-tour'
		}).on('click', closeWin);

		$w.find('h3').html(windowTitle);

		$w.find('iframe').attr({
			src: tourUrl,
			frameborder: 0
		});
	}
	function render(me) {

		if( !config.enabled || !config.selector ) {
			return me;
		}

		if($el === undefined) {
			$el = createEl(config.selector);
			$el.find('a')
				.on('click',function(e){
					e.preventDefault();
					showCallWindow(config.url, config.simpleDlgBaseCls,
						config.windowHintClose, config.windowTitle,
						config.windowWidth, config.windowHeight);
				});
		}

		$el.toggleClass(config.baseCls, !$el.hasClass(config.baseCls))
			.addClass(config.positionCls);

		$el.find('a')
			.attr({
				href: '#' + config.hashmark,
				title: config.imageTitle
			});

		$el.find('img')
			.attr({
				src: config.image,
				alt: config.imageTitle,
				width: config.imageWidth,
				height: config.imageHeight
			});

		return me;
	}

	function LlamadaGratis(cfg){
		var me = this;

		me.reconfigure = function(cfg, performRender) {
			unconfigure(me);
			configure(me, cfg);
			return performRender ? render(me) : me;
		};

		me.render = function() {
			return render(me);
		};

		me.destroy = function() {
			return unconfigure(me);
		};

		return configure(me, cfg);
	};

	LlamadaGratis.getInstance = function() {
		return singleton ? singleton :
			singleton = new LlamadaGratis($.extend(true,{},defaultOpts,window[optKey]));
	};

	window.LlamadaGratis = LlamadaGratis;

	$(function(){
		LlamadaGratis.getInstance().render();
	});

})(jQuery);