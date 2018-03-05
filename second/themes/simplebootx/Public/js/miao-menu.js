;(function ($) {
	/*这个插件就是随便写的*/
	$.MiaoMenu = {version: '0.0.1'};

	var methods = {
		init: function (options) {
			// this
			var opts = $.extend({}, defaluts, options);
			return this.each(function () {
				var $this = $(this);

				$this.prev('.banner-panel-bg').hide();
				$this.hide();

				$this.mouseleave(function () {
					$this.prev('.banner-panel-bg').hide();
					$this.hide();
				});

				$(opts.control).on('mouseover mouseout', 'ul li', function(event) {
					if(event.type == 'mouseover')
					{
						var attr = $(this).attr('menu'),ctx = "";
						if(opts.data[attr])
						{
							var me = opts.data[attr];
							for (var i = 0; i < me.length; i++)
							{
								ctx += "<a href='" + me[i].href + "'>" + me[i].text + "</a>";
							}
						}
						$this.prev('.banner-panel-bg').show();
						$this.empty().append(ctx).show();
					}
					else if(event.type == 'mouseout')
					{
						if($(event.relatedTarget)[0] != $this[0])
						{
							$this.prev('.banner-panel-bg').hide();
							$this.hide();
						}
					}
				});
			});
		}
	};

	$.fn.MiaoMenu = function (method) {
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
			$.error('method' + method + 'does not exist By jQuery.MiaoMenu');
		}
	}

	//默认参数
	var defaluts = {
		control: '',
		data: []
	};

})(window.jQuery);