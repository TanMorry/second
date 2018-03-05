$(document).ready(function(){
//  点击全选按钮,全部选择
	$('.cho-1').on('click', 'input', function(event){
	    if( $(this).is(':checked') )
			$('.cho-2').find('input').prop('checked',true);
		else
			$('.cho-2').find('input').prop('checked',false);
		console.log(1);
	});
//  点击店铺按钮,该店铺所有商品全部选择
	$('.shopping-title').on('click', 'input', function(event){
	    if( $(this).is(':checked') )
			$(this).parents('.cho-2').find('input').prop('checked',true);
		else
			$(this).parents('.cho-2').find('input').prop('checked',false);
		console.log(2);
	});

	(function(p, t){
		$(p).on('click', t, function(event){
			var temp = true;

			$(this).parents(p).find(t).each(function(index, el) {
				if(!$(this).is(':checked'))
				{
					return temp = false;
				}
			});

			$(this).parents(p).find('input').first().prop('checked', temp);

		})
	})('.cho-2', '.shopping-che');
	
	(function(p, t){
		$(p).on('click', t, function(event){
			var temp = true;

			$(this).parents(p).find(t).each(function(index, el) {
				if(!$(this).is(':checked'))
				{
					return temp = false;
				}
			});

			$(this).parents(p).find('input').first().prop('checked', temp);

		})
	})('.M_Center', '.shopping-che');
	
	
	
	
	
	
	
	//选择收货地址
	$('.access-address').on('click', 'label', function(event){
		$(this).addClass('bg-lightred');
		$(this).siblings().removeClass('bg-lightred');
		$(this).find('i').addClass('send-to');
		$(this).siblings().find('i').removeClass('send-to');
		$(this).find('i').find('p').html('寄送至');
		$(this).siblings().find('i').find('p').html('');
	});
});