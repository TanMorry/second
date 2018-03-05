/*防止没有调试功能的老式浏览器报错*/
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
			callback(data);
		},
		error : function(data) {
			console.log(data);
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

/*公共事件绑定*/
$().ready(function() {
	//
});
