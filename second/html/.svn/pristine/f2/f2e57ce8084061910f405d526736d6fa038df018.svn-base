$().ready(function(){
	/*周边弹窗*/
	$('.ss-pop-button').on('click', 'a', function(event) {
		var $this = $(this);
		var popups = $(this).parents('.ss-pop-button').siblings('.ss-pop-ups');
		var id = $(this).attr('id');
		
		popups.each(function(){
			if( $(this).attr('id') == id ){
				if( $(this).css('display') == 'block' ){
					$(this).slideUp();
				}
				else{
					$(this).slideDown();
					$(this).siblings('.ss-pop-ups').slideUp();
					$this.parents('li').siblings('li').find('a').removeClass('color');
					$(this).siblings('.ss-pop-ups').find('a').removeClass('color');
					if( popups.find('li').eq(0).hasClass('btn-qb') ){
						popups.find('.btn-qb').find('a').addClass('color');
						$this.addClass('color');
					}
				}
			}
		});
		
		
		
	});	
	/*周边逻辑*/
	$('.ss-pop-ups').on('click', 'a', function(event) {
		var isChoose = false;
		$(this).toggleClass( 'color' );
		$(this).parents('.ss-pop-ups').find('a').each(function(){
			if( $(this).hasClass( 'color' ) ){
				isChoose = true;
			}
		});
		var id = $(this).parents('.ss-pop-ups').attr('id');
		if(isChoose){
			$(this).parents('.ss-pop-ups').siblings('.ss-pop-button').find('a').each(function(){
				if( $(this).attr('id') == id ){
					$(this).addClass( 'color' );
				}
			});
		}
		else{
			$(this).parents('.ss-pop-ups').siblings('.ss-pop-button').find('a').each(function(){
				if( $(this).attr('id') == id ){
					$(this).removeClass( 'color' );
				}
			});
		}
		if( $(this).parents('li').hasClass('btn-qb') ){
			$(this).parents('li').siblings('li').find('a').removeClass('color');
		}
		if( $(this).parents('li').siblings('li').eq(0).hasClass('btn-qb') ){
			$(this).parents('li').siblings('li').eq(0).find('a').removeClass('color');
			if( $(this).parents('li').siblings('li').find('.color').length + 1 == $(this).parents('li').siblings('li').length ){
				$(this).parents('ul').find('a').removeClass('color');
				$(this).parents('li').siblings('li').eq(0).find('a').addClass('color');
			}
//			console.log( $(this).parents('li').siblings('li').length );
		}
	});
	/*原著输入*/
	var booksname = [];
	function getdata(arr){
		for(i = 0;i < arr.length;i++){
			booksname.push( arr[i].name )
		}
		function isEmpty (val){
			return val == '' || val === null || val === undefined;
		}
		if(!isEmpty(booksname)){
			$('.shop-type').find('.input-box').find('input[type=text]').val(booksname.join('，'));
		}
	}
	getdata(test1);
	/*重置*/
	$('.shop-bottom>p').click(function(event){
		$('.btn').find('a').removeClass('color');
		$('.ss-pop-button').find('a').removeClass('color');
		$('.ss-pop-ups').find('a').removeClass('color');
		$('.ss-pop-ups').css('display','none');
		$('.shop-type').find('.input-box').find('input[type=text]').val('');
		$('.shop-type').find('.ss-search').find('input[type=text]').val('');
	});
   /*全选*/
//	$('.menu-btn2').on('click','a',function(){
//		if( $(this).parent().hasClass('btn-qb') ){
//			if( $(this).hasClass('color') ){
//				$(this).parent('li').siblings('li').find('a').addClass('color');
//			}
//			else if( !$(this).hasClass('color') ){
//				$(this).parent('li').siblings('li').find('a').removeClass('color');
//			}
//		}
//		else{
//			var isChoose = false;
//			$(this).toggleClass( 'color' );
//			$(this).parents('.ss-pop-ups').find('a').each(function(){
//				if( $(this).hasClass( 'color' ) ){
//					isChoose = true;
//				}
//			});
//			var id = $(this).parents('.ss-pop-ups').attr('id');
//			if(isChoose){
//				$(this).parents('.ss-pop-ups').siblings('.ss-pop-button').find('a').each(function(){
//					if( $(this).attr('id') == id ){
//						$(this).addClass( 'color' );
//					}
//				});
//			}
//			else{
//				$(this).parents('.ss-pop-ups').siblings('.ss-pop-button').find('a').each(function(){
//					if( $(this).attr('id') == id ){
//						$(this).removeClass( 'color' );
//					}
//				});
//			}
//		}
//		
//	});
});

/*测试数据*/
	var test1 = [
		{id:'1001',name:'啊啊啊'},
		{id:'1002',name:'别别别'},
		{id:'1003',name:'错错错'},
		{id:'1004',name:'打断的'}
	];