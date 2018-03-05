var URL_PATH = "http://localhost/jinyiwei/wx_shop_v1/";
//var URL_POST = "http://localhost/jyw/wx_server/cmd.php";
var URL_POST = "http://localhost/jinyiwei/wechat/cmd.php";
//var URL_POST = "http://wechat.neckyw.com/cmd.php";
var URL_FILE = "http://localhost/jyw/wx_server/upload/images";

//重写Jquery的click事件使其在移动端使用touchstart,消除ios的点击延迟
/*(function(){
	var isTouch = ('ontouchend' in document.documentElement) ? 'touchend' : 'click', _on = $.fn.on;
	$.fn.on = function(){
		arguments[0] = (arguments[0] === 'click') ? isTouch: arguments[0];
		return _on.apply(this, arguments);
	};
})();*/

//防止没有调试功能的老式浏览器报错
var console = console || {
	log : function (data) {
		return false;
	}
}

var SendAjax = function (value, callback, error) {
	$.ajax({
		url : URL_POST,
		type : 'POST',
		dataType : 'json',
		data : value,
		success : function (data) {
			console.log(data);
			//callback(data);
		},
		error : function(data) {
			console.log(data);
			ErrorCode('ERROR_URL_UNKNOWN');
		}
	});
}

var getUrlParam = function (url) {

	var param = [];
	var split = url.split("?");

	for (var i = 0; i < split.length; i++)
	{
		var p = split[i].split("=");
		if (p.length == 2)
		{
			param[p[0]] = p[1];
		}
	}

	return param;
}

var ErrorCode = function (code, way) {
	if(way == undefined) var way = 1;

	var error = [];
	error['ERROR_URL_UNKNOWN'] = '发生未知错误';
	error['ERROR_ORDER_UNKNOWN'] = '无法获取服务项目';

	if (error[code] !== undefined)
	{
		if(way == 1) alert(error[code]);
		//if(way == 2) console.log(error[code]);
		return error[code];
	}
	return 'Error';
}

var addHeader = function (text,url) {
	if(!text) var text = "";

	var menuDOM = "<div class=\"header\"><span class=\"btn_back\"></span><span class=\"header_text\">" + text + "</span><span class=\"header_empty\"></span></div>";

	$("body").children('.flex').children('.content').prepend(menuDOM);

	if(!url) return;

	if(typeof url == 'function')
	{
		$('.flex').children('.content').children('.header').find('.btn_back').on('click', function(){
			url();
		});
	}
	else if(typeof url == 'string')
	{
		$('.flex').children('.content').children('.header').find('.btn_back').on('click', function(){
			window.location.href = url;
		});
	}

	
}

var addFooter = function (de) {
	var menu = ['首页','预定','我的'];
	var menuDOM = "";

	if(!de) de = '首页';

	for (var i = 0; i < menu.length; i++)
	{
		if(de == menu[i]) var active = "<div class=\"active\">";
		else var active = "<div>";

		menuDOM += active + "<span class=\"nav_0" + i + "\">" + menu[i] + "</span></div>";
	}

	menuDOM = "<section class=\"footer\"><div class=\"foter\">" + menuDOM + "</div></section>";

	$("body").children('.flex').append(menuDOM);

	$("body").children('.flex').children('.footer').find('span').on('click', function(){
		var btn = $(this).attr('class');

		if (btn == 'nav_00') {
			window.location.href = "main.html";
		} else if (btn == 'nav_01') {
			window.location.href = "order_list.html";
		} else if (btn == 'nav_02') {
			window.location.href = "my_info.html";
		}
		//选中立即效果
		$("body").children('.flex').children('.footer').find('.active').removeClass('active');
		$(this).parent().addClass('active');
	});
}
