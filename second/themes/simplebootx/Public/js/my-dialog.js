
;(function ($) {

	$.MiaoDialog = {version: '0.0.1'};

	var methods = {
		init: function (options) {
			// this
			var opts = $.extend({}, defaluts, options);
			return this.each(function () {
				if($(this).parent().attr('miao') == 'miao-dialog')
				{
					return true;
				}
				var $this = $(this).wrap("<div miao='miao-dialog'></div>").parent();

				var width = 0;
				var height = 0;
				var clientWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
				var clientHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
				/*检测宽度*/
				if(!isNaN(opts.width))
				{
					width = opts.width;
				}
				else if(opts.width.indexOf('px') != -1)
				{
					width = parseFloat(opts.width);
				}
				else if(opts.width.indexOf('%') != -1)
				{
					width = clientWidth * (parseFloat(opts.width) / 100);
				}
				/*检测高度*/
				if(!isNaN(opts.height))
				{
					height = opts.height;
				}
				else if(opts.height.indexOf('px') != -1)
				{
					height = parseFloat(opts.height);
				}
				else if(opts.height.indexOf('%') != -1)
				{
					height = clientHeight * (parseFloat(opts.height) / 100);
				}

				var top = 0;
				var left = 0;
				var margin_top = 0;
				var margin_left = 0;
				/*计算左侧距离*/
				if(!isNaN(opts.left))
				{
					left = opts.left + 'px';
				}
				else if(opts.left == 'center')
				{
					left = '50%';
					margin_left = -(width / 2) + 'px';
				}
				else
				{
					left = opts.left;
				}
				/*计算顶部距离*/
				if(!isNaN(opts.top))
				{
					top = opts.top + 'px';
				}
				else if(opts.top == 'center')
				{
					top = '50%';
					margin_top = -(height / 2) + 'px';
				}
				else
				{
					top = opts.top;
				}

				var display = opts.autoOpen ? 'block' : 'none';

				$this.css({
					'position': 'fixed',
					'z-index': 9999,
					'display': display,
					'overflow': 'auto',
					'top': top,
					'left': left,
					'margin': margin_top + ' 0 0 ' + margin_left,
					'width': width + 'px',
					'height': height + 'px'
				});

				$this.data({'width': width, 'height': height});

				if(opts.modal)
				{
					$this.before("<div miao='miao-full-shade' style='position:fixed;z-index:9998;top:0;left:0;width:100%;height:100%;background:black;opacity:0.5;filter:alpha(opacity:50);display:" + display + "'></div>");
				}
			});
		},
		close: function (anim) {
			return this.each(function () {
				var $this = $(this).parent();

				if($(this).parent().attr('miao') != 'miao-dialog')
				{
					return true;
				}

				if(!anim)
				{
					$this.css({'display': 'none'});
				}
				else if(anim == 'fade')
				{
					$this.fadeOut('266');
				}
				else if(anim == 'height')
				{
					$this.animate({
						'height': '0',
						'margin-top': '0'
					},
					function(){
						$this.css({
							'display': 'none',
							'height': $this.data('height'),
							'margin-top': -($this.data('height') / 2)
						});
					});
				}
				else
				{
					$this.css({'display': 'none'});
				}

				var prev = $this.prev();
				if(prev.attr('miao') == 'miao-full-shade')
				{
					prev.css({
						'display': 'none'
					});
				}
			});
		},
		open: function (anim) {
			return this.each(function () {
				/*没初始化过则初始化默认值*/
				if($(this).parent().attr('miao') != 'miao-dialog')
				{
					$(this).MiaoDialog();
				}
				var $this = $(this).parent();

				if(!anim)
				{
					$this.css({'display': 'block'});
				}
				else if(anim == 'fade')
				{
					$this.fadeIn('266');
				}
				else if(anim == 'height')
				{
					$this.css({
						'height':'0',
						'margin-top': '0',
						'display': 'block'
					}).animate({
						'height': $this.data('height'),
						'margin-top': -($this.data('height') / 2)
					});
				}
				else
				{
					$this.css({'display': 'block'});
				}

				var prev = $this.prev();
				if(prev.attr('miao') == 'miao-full-shade')
				{
					prev.css({
						'display': 'block'
					});
				}
			});
		}
	};

	$.fn.MiaoDialog = function (method) {
		// 方法调用
		if (methods[method])
		{
            // apply 把 obj.method(arg1, arg2, arg3) 转换成 method(obj, [arg1, arg2, arg3])
            // Array.prototype.slice.call(arguments, 1) 把方法的参数转换成数组.
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		}
		else if (typeof method === 'object' || !method)
		{
			return methods.init.apply(this, arguments);
		}
		else
		{
			$.error('method' + method + 'does not exist By jQuery.MiaoDialog');
		}
	}

	//默认参数
	var defaluts = {
		top: 'center',
		left: 'center',
		width: '50%',
		height: '50%',
		modal: true,
		autoOpen: false
	};

})(window.jQuery);