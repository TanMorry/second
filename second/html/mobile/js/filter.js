var isLoad = false;
function filter(){
		/*点击…*/
		$('.header-r').on('click', '.header-bg-btn', function(event) {
			if($(".header-r-tc").css('display') == 'none') {
				$(".header-r-tc").slideDown();
			} else {
				$(".header-r-tc").css('display', 'none');
			}
		});
		$('.content').on('click', function(event) {
			$(".header-r-tc").css('display', 'none');
		});
		$('.footer').on('click', function(event) {
			$(".header-r-tc").css('display', 'none');
		});

		/*输入框*/
		$('.header-X').on('click', 'a', function(event) {
			$(this).parents('.header-X').siblings('input[type=text]').val('');
			$(this).parents('.header-X').css('display', 'none');
		});
		$('.header-c').on('blur', 'input[type=text]', function(event) {
			function isEmpty(val) {
				return val == '' || val === null || val === undefined;
			}
			if(!isEmpty($(this).val())) {
				$(this).siblings('.header-X').css('display', 'block');
			} else {
				$(this).siblings('.header-X').css('display', 'none');
			}
		});

		/*商品，同人铺，商业店切换*/
		$(function() {
			var h1 = $(".header-btn-a>a");
			var text = $(".header-input .header-in-btn");
			var val = text.find("a");
			h1.click(function() {
				text.slideDown();
				text.focus();
			});
			h1.blur(function() {
				text.slideUp();
			});
			val.click(function() {
				var tex = $(this).text();
				h1.html(tex);
			})
		});

		var phone = $('#cpp').MiaoDialog();

		// /*Ajax*/
		if(!isLoad){
			loadAjax();
		}


		function loadAjax() {
			var url = "/tongrenmiao/index.php?g=Mall&m=index&a=searchFilter";
			$.ajax({
				url: url,
				data: {},
				dataType: 'json',
				type: 'get',
				beforeSend: function() {

				},
				success: function(ret) {
                    isLoad = true;
					var searchContent = $('.header-c').find('input').val();
					var typeShop;
					var typeTR;
					var typeFavorite;
					var productLevel1 = new Array();
					var productLevel2 = new Array();
					var productLevel3 = new Array();
					var productLevel1Id = new Array();
					var productLevel2Id = new Array();
					var productLevel3Id = new Array();
					var chooseFoods = new Array();
					var chooseFoodsId = new Array();
					var categories = "";
					var canAction = false;
					for(i = 0; i < ret.product.length; i++) {
						if(ret.product[i].catid < 2000) {
							productLevel1.push(ret.product[i].cat);
							productLevel1Id.push(ret.product[i].catid);
						} else if(ret.product[i].catid < 200000) {
							productLevel2.push(ret.product[i].cat);
							productLevel2Id.push(ret.product[i].catid);
						} else if(ret.product[i].catid < 20000000) {
							productLevel3.push(ret.product[i].cat);
							productLevel3Id.push(ret.product[i].catid);
						}
					}
					for(i = 0; i < productLevel1.length; i++) {
						categories += '<li><a id="' + productLevel1Id[i] + '" >' + productLevel1[i] + '</a></li>';
						$('#categories').append('<ul class="menu-btn2 clearfix ss-pop-ups" id="' + productLevel1Id[i] + '" name="' + productLevel1[i] + '" ></ul>');
						for(j = 0; j < productLevel2.length; j++) {
							if(parseInt(productLevel2Id[j] / 100) == productLevel1Id[i]) {
								$('ul#' + productLevel1Id[i]).append('<li><a id=' + productLevel2Id[j] + '>' + productLevel2[j] + '</a></li>');
							}
						}
					}
					$('#categories1').html(categories);
					$('ul').each(function() {
						/*不含有三级列表  同人本  原创本*/
						if($(this).attr('id') == 1003 || $(this).attr('id') == 1004) {
							$(this).prepend('<li class="btn-qb"><a>全选</a></li>');
						}

						/*含有三级列表*/
						else if($(this).attr('id') == 1005 || $(this).attr('id') == 1006 || $(this).attr('id') == 1002) {

						}
					});

					/*页面逻辑*/
					$('.btn').on('click', 'a', function(event) {
						$(this).toggleClass('color');
						canAction = checkCanAction(chooseFoodsId.length);
						if(canAction){
							$('.shop-bottom').find('p').removeClass('inactivated');
							$('.shaixuan').find('a').removeClass('inactivated');
						}
						else{
							$('.shop-bottom').find('p').addClass('inactivated');
							$('.shaixuan').find('a').addClass('inactivated');
						}
					});

					/*周边逻辑*/

					/*一级列表逻辑*/
					$('.ss-pop-button').on('click', 'a', function(event) {
						var $this = $(this);
						var popups = $(this).parents('.ss-pop-button').siblings('.ss-pop-ups');
						var id = $(this).attr('id');

						popups.each(function() {
							if($(this).attr('id') == id) {
								if($(this).css('display') == 'block') {
									$(this).slideUp();
									$this.removeClass('color');
									$(this).find('a').removeClass('color');
								} else {
									$(this).slideDown();
									$(this).siblings('.ss-pop-ups').slideUp();
									$this.parents('li').siblings('li').find('a').removeClass('color');
									$(this).siblings('.ss-pop-ups').find('a').removeClass('color');
									if($(this).find('li').eq(0).hasClass('btn-qb')) {
										$(this).find('.btn-qb').find('a').addClass('color');
										$this.addClass('color');
										chooseFoods = [];
										chooseFoodsId = [];
										chooseFoods[0] = $this.text() + '全选';
										chooseFoodsId[0] = $this.attr('id');
									}
								}
								if(!$this.hasClass('color')) {
									chooseFoods = [];
									chooseFoodsId = [];
								}
								$('#text').text(chooseFoods.join('，'));
								if($('#text').text() == '')
									$('#text').parent().slideUp();
								else
									$('#text').parent().slideDown();
								console.log(chooseFoodsId);
								canAction = checkCanAction(chooseFoodsId.length);
								if(canAction){
									$('.shop-bottom').find('p').removeClass('inactivated');
									$('.shaixuan').find('a').removeClass('inactivated');
								}
								else{
									$('.shop-bottom').find('p').addClass('inactivated');
									$('.shaixuan').find('a').addClass('inactivated');
								}
							}
						});
					});

					/*二级列表逻辑*/
					$('.ss-pop-ups').on('click', 'a', function(event) {
						var isChoose = false;
						if($(this).parents('li').siblings('li').eq(0).hasClass('btn-qb') || $(this).parents('li').hasClass('btn-qb')) {
							$(this).toggleClass('color');
						}
						/*虚拟货品特殊对待*/
						else if($(this).parents('.ss-pop-ups').attr('id') == 1002) {
							$(this).toggleClass('color');
							$('#' + $(this).parents('.ss-pop-ups').attr('id')).toggleClass('color');
							chooseFoods = [];
							chooseFoodsId = [];
							if($(this).hasClass('color')) {
								chooseFoods[0] = $(this).text();
								chooseFoodsId[0] = $(this).attr('id');
							}
							$('#text').text(chooseFoods.join('，'));
							if($('#text').text() == '')
								$('#text').parent().slideUp();
							else
								$('#text').parent().slideDown();
							console.log(chooseFoodsId);
							canAction = checkCanAction(chooseFoodsId.length);
							if(canAction){
								$('.shop-bottom').find('p').removeClass('inactivated');
								$('.shaixuan').find('a').removeClass('inactivated');
							}
							else{
								$('.shop-bottom').find('p').addClass('inactivated');
								$('.shaixuan').find('a').addClass('inactivated');
							}
						} else {
							$(this).parents('li').siblings('li').find('a').removeClass('color');
							functionPopUp($(this).attr('id'));
							if(!$(this).hasClass('color')) {
								chooseFoods = [];
								chooseFoodsId = [];
								$('#text').text(chooseFoods.join('，'));
								if($('#text').text() == '')
									$('#text').parent().slideUp();
								else
									$('#text').parent().slideDown();
								console.log(chooseFoodsId);
								canAction = checkCanAction(chooseFoodsId.length);
								if(canAction){
									$('.shop-bottom').find('p').removeClass('inactivated');
									$('.shaixuan').find('a').removeClass('inactivated');
								}
								else{
									$('.shop-bottom').find('p').addClass('inactivated');
									$('.shaixuan').find('a').addClass('inactivated');
								}
							}
							$(this).addClass('color');
						}

						/*点击二级列表后一级列表逻辑*/
						$(this).parents('.ss-pop-ups').find('a').each(function() {
							if($(this).hasClass('color')) {
								isChoose = true;
							}
						});
						var id = $(this).parents('.ss-pop-ups').attr('id');
						if(isChoose) {
							$(this).parents('.ss-pop-ups').siblings('.ss-pop-button').find('a').each(function() {
								if($(this).attr('id') == id) {
									$(this).addClass('color');
								}
							});
						} else {
							$(this).parents('.ss-pop-ups').siblings('.ss-pop-button').find('a').each(function() {
								if($(this).attr('id') == id) {
									$(this).removeClass('color');
								}
							});
						}

						/*含有全选框逻辑*/
						if($(this).parents('li').hasClass('btn-qb')) {
							$(this).parents('li').siblings('li').find('a').removeClass('color');
							changeText();
						}
						if($(this).parents('li').siblings('li').eq(0).hasClass('btn-qb')) {
							$(this).parents('li').siblings('li').eq(0).find('a').removeClass('color');
							if($(this).parents('li').siblings('li').find('.color').length + 1 == $(this).parents('li').siblings('li').length) {
								$(this).parents('ul').find('a').removeClass('color');
								$(this).parents('li').siblings('li').eq(0).find('a').addClass('color');
							}
							changeText();
						}

						/*不含有全选框逻辑*/
						if(!$(this).parents('li').siblings('li').eq(0).hasClass('btn-qb') && !$(this).parents('li').hasClass('btn-qb')) {

						}

					});

					/*二级列表点击后输出框显示*/
					function changeText() {
						chooseFoods = [];
						chooseFoodsId = [];
						$('.ss-pop-ups').find('li').each(function() {
							if($(this).hasClass('btn-qb') && $(this).find('a').hasClass('color')) {
								chooseFoods = [];
								chooseFoodsId = [];
								chooseFoods[0] = $(this).parents('.ss-pop-ups').attr('name') + '全选';
								chooseFoodsId[0] = $(this).parents('.ss-pop-ups').attr('id');
							} else if($(this).siblings('li').eq(0).hasClass('btn-qb') && $(this).find('a').hasClass('color')) {
								chooseFoods.push($(this).find('a').text());
								chooseFoodsId.push($(this).find('a').attr('id'));
							}
						});
						//					if( !$('.ss-pop-ups').find('li').find('a').hasClass('color') ){
						//						chooseFoods = [];
						//						chooseFoodsId = [];
						//					}
						$('#text').text(chooseFoods.join('，'));
						if($('#text').text() == '')
							$('#text').parent().slideUp();
						else
							$('#text').parent().slideDown();
						console.log(chooseFoodsId);
						canAction = checkCanAction(chooseFoodsId.length);
						if(canAction){
							$('.shop-bottom').find('p').removeClass('inactivated');
							$('.shaixuan').find('a').removeClass('inactivated');
						}
						else{
							$('.shop-bottom').find('p').addClass('inactivated');
							$('.shaixuan').find('a').addClass('inactivated');
						}
					};

					/*三级列表Ajax*/
					function functionPopUp(e) {
						var level3 = "";
						phone.MiaoDialog('open');
						for(i = 0; i < productLevel3.length; i++) {
							if(parseInt(productLevel3Id[i] / 100) == e) {
								level3 += '<b class="text-clamp-one" id="' + productLevel3Id[i] + '">' + productLevel3[i] + '</b>';
							}
						}
						$('#level3').html(level3);
						$('#level3').attr('name', e);
						//					addCopy( foodsCopyId );
						addCopy(chooseFoodsId);
					};

					/*三级列表逻辑*/
					$('#level3').on('click', 'b', function() {
						$(this).toggleClass('color');
					});
					$('#determine').on('click', function(event) {
						chooseFoods = [];
						chooseFoodsId = [];
						$('#level3').find('b').each(function() {
							if($(this).hasClass('color')) {
								chooseFoods.push($(this).text());
								chooseFoodsId.push($(this).attr('id'));
							}
						});
						$('#text').text(chooseFoods.join('，'));
						if($('#text').text() == '')
							$('#text').parent().slideUp();
						else
							$('#text').parent().slideDown();
						console.log(chooseFoodsId);
						phone.MiaoDialog('close');
						if(chooseFoods.length == 0) {
							$('.ss-pop-ups').find('a').removeClass('color');
							$('.ss-pop-button').find('a').removeClass('color');
						}
					});
					$('#cancel').on('click', function(event) {
						$('#text').text(chooseFoods.join('，'));
						if($('#text').text() == '')
							$('#text').parent().slideUp();
						else
							$('#text').parent().slideDown();
						console.log(chooseFoodsId);
						phone.MiaoDialog('close');
						if(chooseFoods.length == 0) {
							$('.ss-pop-ups').find('a').removeClass('color');
							$('.ss-pop-button').find('a').removeClass('color');
						}
					});

					/*页面记忆*/
					function addCopy(e) {
						$('#level3').find('b').each(function() {
							for(i = 0; i < e.length; i++) {
								if(e[i] == $(this).attr('id')) {
									$(this).addClass('color');
								}
							}
						});
					}

					/*叉子*/
					$('.type-input').on('click', 'i', function() {
						$('.ss-pop-button').find('a').removeClass('color');
						$('.ss-pop-ups').find('a').removeClass('color');
						$('#text').text('');
						$('#text').parent().slideUp();
						chooseFoods = [];
						chooseFoodsId = [];
						canAction = checkCanAction(chooseFoodsId.length);
						if(canAction){
							$('.shop-bottom').find('p').removeClass('inactivated');
							$('.shaixuan').find('a').removeClass('inactivated');
						}
						else{
							$('.shop-bottom').find('p').addClass('inactivated');
							$('.shaixuan').find('a').addClass('inactivated');
						}
					});

					/*重置*/
					$('.shop-bottom>p').click(function(event) {
						if( !$(this).hasClass('inactivated') ){
							$('.btn').find('a').removeClass('color');
							$('.ss-pop-button').find('a').removeClass('color');
							$('.ss-pop-ups').find('a').removeClass('color');
							$('.ss-pop-ups').css('display', 'none');
							$('#text').text('');
							$('#text').parent().slideUp();
							chooseFoods = [];
							chooseFoodsId = [];
							canAction = checkCanAction(chooseFoodsId.length);
							if(canAction){
								$('.shop-bottom').find('p').removeClass('inactivated');
								$('.shaixuan').find('a').removeClass('inactivated');
							}
							else{
								$('.shop-bottom').find('p').addClass('inactivated');
								$('.shaixuan').find('a').addClass('inactivated');
							}
						}
					});

					/*确认*/
					$('.footer.shaixuan').on('click', 'a', function() {
						if( !$(this).hasClass('inactivated') ){
							typeShop = binary($('.content').find('.shop-type').eq(0));
							typeTR = binary($('.content').find('.shop-type').eq(1));
							typeFavorite = binary($('.content').find('.shop-type').eq(2));
							//              		console.log(searchContent);
							//                 		console.log(typeShop);
							//                 		console.log(typeTR);
							//                 		console.log(typeFavorite);
							//                 		console.log(chooseFoodsId);
							loadAjax2(searchContent, typeShop, typeTR, typeFavorite, chooseFoodsId);
						}
					});

				}
			})
		};

		function loadAjax2(a, b, c, d, e) {
			var data = {};
			data['searchContent'] = a;
			data['typeShop'] = b;
			data['typeTR'] = c;
			data['typeFavorite'] = d;
			data['chooseFoodsId'] = e;
			IndexList(null,data,null,null);
            $('.btn').find('a').removeClass('color');
            $('.ss-pop-button').find('a').removeClass('color');
            $('.ss-pop-ups').find('a').removeClass('color');
            $('.ss-pop-ups').css('display', 'none');
            $('#text').text('');
            $('#text').parent().slideUp();
            chooseFoods = [];
            chooseFoodsId = [];
			canAction = checkCanAction(chooseFoodsId.length);
			if(canAction){
				$('.shop-bottom').find('p').removeClass('inactivated');
				$('.shaixuan').find('a').removeClass('inactivated');
			}
			else{
				$('.shop-bottom').find('p').addClass('inactivated');
				$('.shaixuan').find('a').addClass('inactivated');
			}
				// var url = "/tongrenmiao/index.php?g=Mall&m=index&a=searchFilter";
				// $.ajax({
				//     url:url,
				//     data:{
				//     	'searchContent':a,
				//     	'typeShop':b,
				//     	'typeTR':c,
				//     	'typeFavorite':d,
				//     	'chooseFoodsId':e,
				//      'filterType':'shaixuan'
				//     },
				//     dataType:'json',
				//     type:'get',
				//     beforeSend:function()
				//     {
				//
				//     },
				//     success:function(ret)
				//     {
				//     	console.log(123);
				//     }
				// })
		};

		function binary($this) {
			var num = "";
			var isAll = true;
			for(i = 0; i < $this.find('a').length; i++) {
				if($this.find('a').eq(i).hasClass('color')) {
					isEmpty(num) ? num += $this.find('a').eq(i).attr('data-val') : num += "," + $this.find('a').eq(i).attr('data-val');
				} else {
					isAll = false;
				}
			}
			// if(isAll) {
			// 	num = "-1";
			// }
			return num;
		}
		
		function checkCanAction(e){
			if( e == 0 && !$('.shop-type').eq(0).find('a').hasClass('color') && !$('.shop-type').eq(1).find('a').hasClass('color') && !$('.shop-type').eq(2).find('a').hasClass('color') ){
				return false;
			}
			else{
				return true;
			}
		}

		function isEmpty(val) {
            return val == '' || val === null || val === undefined;
        }
}