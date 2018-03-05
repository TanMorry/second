
;(function ($) {
	/*这个插件就是随便写的*/
	$.MiaoMenu = {version: '0.0.1'};

	var methods = {
		init: function (options) {
			// this
			var opts = $.extend({}, defaluts, options);

			return this.each(function () {
				var $this = $(this);

				var gf = $this.parent().width(); //grandfather
				var fa = $this.width(); //father

				function getSecond (menu) {
					return $(opts.second).find("b[parent='" + menu + "']");
				}

				function getThird (menu) {
					return $(opts.third).find("b[parent='" + menu + "']");
				}

				/*二级菜单控制*/
				function secondControl ($that) {
					var $second = getSecond($that.attr('menu'));

					$(opts.second).css({
						'marginLeft': 0,
						'display': 'block'
					}).find('b').hide();

					var l = $that[0].offsetLeft - ((gf - fa) / 2) + ($that.width() / 2); //left
					var b = $second.width() + 4; //inline-block 4px
					var m = l - (b / 2) + 2;

					m = Math.min(m, fa - b); //不能超过容器宽
					m = Math.max(m, 0); //不能为负数
					m = Math.floor(m); //防止小数

					$(opts.second).css({'marginLeft': m}); //设置margin-left
					$second.show();
				}

				$this.find('ul').on('click', 'a', function(event) {
					$this.find('ul a').removeClass('fixed');
					secondControl($(this).addClass('fixed'));
				});
				$this.on('click', 'a', function(event) {
					return false;
				});
				//设置默认值
				secondControl($this.find('ul').find('.fixed'));

				/*二级菜单hover展现三级菜单*/
				$(opts.second).on('mouseover mouseout', 'a', function(event) {
					if(event.type == 'mouseover')
					{
						var menu = "[parent='" + $(this).attr("menu") + "']";
						var $third = $(opts.third).find("b" + menu); //没有的length是0
						if($third.length > 0)
						{
							$(opts.third).find('b').not(parent).hide();
							$third.show(); //确保之前没有被隐藏
							var top = $(this)[0].offsetTop + $(this)[0].offsetHeight;
							var m = $(this)[0].offsetLeft - ((gf - fa) / 2) + ($(this)[0].offsetWidth / 2) - 4;
							$(opts.third).css({
								'top': top
							}).show()
							.children().first().css({
								'marginLeft': m
							});
						}
					}
					else if(event.type == 'mouseout')
					{
						if($(event.relatedTarget)[0] != $(opts.third)[0] && $(event.relatedTarget)[0] != $(opts.third).children().first()[0])
						{
							$(opts.third).hide();
						}
					}
				});
				/*鼠标移除三级菜单则隐藏*/
				$(opts.third).mouseleave(function(event) {
					$(this).hide();
				});
				/*初始化*/
				$(opts.third).css({
					'position': 'absolute',
					'z-index': '2',
					'top': '0',
					'left': (gf - fa) / 2,
					'width': fa
				})
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
		second: '.cs-menu',
		third: '.cs-item'
	};

})(window.jQuery);