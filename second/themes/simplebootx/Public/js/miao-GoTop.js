
;(function ($) {

	$.MiaoGoTop = {version: '0.0.1'};

	var methods = {
		init: function (options) {
			// this
			var opts = $.extend({}, defaluts, options);
			return this.each(function () {

				var $this = $(this);

				if(opts.fixed == true)
				{
					var clientWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
					var clientHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

					var x = parseFloat(opts.x);
					var y = parseFloat(opts.y);

					x = (clientWidth - x) / 2 - $this.width() - opts.xMargin;
					x = Math.max(x, opts.xMargin);



					$this.css({
						position: 'fixed',
						display: 'none',
						right: x + 'px',
						bottom: y+25 + 'px',
						zIndex: '99'
					});
					$this.next().css({
						position: 'fixed',
						display: 'none',
						right: x + 'px',
						bottom: y-30 + 'px',
						zIndex: '99'
					});
				}

				// $(window).scroll(function() {
				// 	/*浏览页面多少以后出现滚动条*/
				// 	var top = Math.floor(clientHeight * 0.5);
				// 	if($(window).scrollTop() > top)
				// 	{
				// 		if($this.css('display') == 'none')
				// 		{
				// 			// $(".M_GoBottom").fadeOut('266');//TL
				// 			$this.fadeIn('266');
				// 		}
				// 	}
				// 	else
				// 	{
				// 		if($this.css('display') != 'none')
				// 		{
				// 			// $(".M_GoBottom").fadeIn('266');//TL
				// 			$this.fadeOut('266');
				// 		}
				// 	}
				// });
				$this.css('display','block');
				$this.next().css('display','block');
				$(window).on('resize', function(event) {

					clientWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
					clientHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

					x = (clientWidth - parseFloat(opts.x)) / 2 - $this.width() - opts.xMargin;
					x = Math.max(x, opts.xMargin);

					$this.css({
						right: x + 'px'
					});
					$this.next().css({
						right: x + 'px'
					});
				});

				$this.on('click', function(event) {
					$("body,html").animate({scrollTop: 0}, 100);
				});
				$this.next().on('click', function(event) {
					$("body,html").animate({scrollTop: $(document).height()}, 100);
				});
			});
		}
	};

	$.fn.MiaoGoTop = function (method) {
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
			$.error('method' + method + 'does not exist By jQuery.MiaoGoTop');
		}
	}

	/******
	默认参数
	x: 浮动于div右侧,div的宽度,默认就是50% --- 随便写写吧^_^!!
	******/
	var defaluts = {
		fixed: 'false',
		x: 0,
		xMargin: 0,
		bottom: 0
	};

})(window.jQuery);