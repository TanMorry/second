$(document).ready(function(){
	
	$(".M-H-li").on('mouseout mouseover', 'a', function(event) {
		if(event.type == 'mouseover')
		{
			$(this).parent().addClass('checked');
		}
		else if(event.type == 'mouseout')
		{
			$(this).parent().removeClass('checked');
		}
	});
	
	$(".M_GoTop").MiaoGoTop({
		fixed: true,
		x: 960, //960是页面宽
		xMargin: 15, //距离页面和屏幕右边最小的边距
		y: 60 //距离底部
	});
	
	$(".align_left a").each(function(){
		var text = $(this).text();
		if($(this).text().length > 42)
		{
			$(this).text(text.substring(0, 42) + "...");
		}
	});
});