$(document).ready(function(){

	/*输入数量验证*/
	$(".footer input").on('blur input propertychange', function(event) {

		function isEmpty (val)
		{
			return val == '' || val === null || val === undefined;
		}

		var val = $(this).val();
		/*如果不是空则强转数字*/
		if (!isEmpty(val))
		{
			val = parseInt(val.replace(/[^\d]/ig,''), 10);
		}

		isNaN(val) ? $(this).val("") : $(this).val(val);
		if(type == 1)
		{
			if(val > loveCountPage)
			{
				$("#go").css('background','#999');
			}
			else
			{
				$("#go").css('background','#eb6877');
			}
		}
		else
		{
			if(val > kindCountPage)
			{
				$("#go").css('background','#999');
			}
			else
			{
				$("#go").css('background','#eb6877');
			}
		}
		// if(event.type == 'blur' || event.type == 'focusout')
		// {
		// 	if(isEmpty(val))
		// 	{
		// 		$(this).val(1);
		// 	}
		// }
	});
});