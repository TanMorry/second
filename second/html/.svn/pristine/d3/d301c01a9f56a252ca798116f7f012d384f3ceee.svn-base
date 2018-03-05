function movetouch( a ){
	
	
	window.addEventListener('load',function(){
			var $div1 = a;
			var $div2 = a.find('.content-delete');
			var $div3 = a.parent();
			var initX = 0;
			var moveX = 0;
			var changeX = 0;
			var objX = 0;
			var divheight = $div1.height() + parseInt($div1.css('padding-top')) + parseInt($div1.css('padding-bottom'))  + parseInt($div1.css('border-top'))  + parseInt($div1.css('border-bottom')) ;
			$div2.css('height',divheight);
			var deletewidth = $div2.width();
			var deletewidth2 = deletewidth / 2; 
			window.addEventListener('touchstart',function(event){
				//event.preventDefault();
//				var obj = event.target.parentNode;
				var obj = $(event.target).closest('.content-top');

				var del = $(event.target);
//				if(del.hasClass('content-choice'))
//				{
//					del = del.find('.content-delete');
//				}
//				else
//				{
//					del = del.closest('.content-choice').find('.content-delete');
//				}
				
				del = del.hasClass('content-choice') ? del.find('.content-delete') : del.closest('.content-choice').find('.content-delete');
				

				if(del.length > 0)
				{
					$div2 = del;
				}else{
					del=$(event.target).parents(".content-choice").find(".content-delete");
					if(del.length > 0)
					{
						$div2 = del;
					}
				}
//				$('.div1').find(':not(".class1")')
				
				if(!!obj[0] && !!$div3[0] && obj[0].className == $div3[0].className){
					initX = event.targetTouches[0].pageX;
					objX =($div2[0].style.WebkitTransform.replace(/translateX\(/g,"").replace(/px\)/g,""))*1;
				}
				if( objX == 0){
					window.addEventListener('touchmove',function(event) {
						//event.preventDefault();

						var obj = $(event.target);
						if (obj.hasClass('content-choice') || obj.closest('.content-choice').length > 0) {
							moveX = event.targetTouches[0].pageX;
							changeX = moveX - initX;
							if (changeX > 0) {
								$div2[0].style.WebkitTransform = "translateX(" + 0 + "px)";
							}
							else if (changeX < 0) {
								var l = Math.abs(changeX);
								$div2[0].style.WebkitTransform = "translateX(" + -l + "px)";
								if(l>deletewidth){
									l=deletewidth;
									$div2[0].style.WebkitTransform = "translateX(" + -l + "px)";
								}
							}
						}
					});
				}
				else if(objX<0){
					window.addEventListener('touchmove',function(event) {
						//event.preventDefault();
						/*var obj = event.target;
						if (obj.className == $div1[0].className) {*/
						var obj = $(event.target);
						if (obj.hasClass('content-choice') || obj.closest('.content-choice').length > 0) {
							moveX = event.targetTouches[0].pageX;
							changeX = moveX - initX;
							if (changeX > 0) {
								var r = -deletewidth + Math.abs(changeX);
								$div2[0].style.WebkitTransform = "translateX(" + r + "px)";
								if(r>0){
									r=0;
									$div2[0].style.WebkitTransform = "translateX(" + r + "px)";
								}
							}
							else {     //向左滑动
								$div2[0].style.WebkitTransform = "translateX(" + -deletewidth + "px)";
							}
						}
					});
				}
			});
			window.addEventListener('touchend',function(event){
				//event.preventDefault();
				/*var obj = event.target;
				if(obj.className == $div1[0].className){*/
				var obj = $(event.target);
				if (obj.hasClass('content-choice') || obj.closest('.content-choice').length > 0) {
					objX =($div2[0].style.WebkitTransform.replace(/translateX\(/g,"").replace(/px\)/g,""))*1;
					if(objX>-deletewidth2){
						$div2[0].style.WebkitTransform = "translateX(" + 0 + "px)";
					}else{
						$div2[0].style.WebkitTransform = "translateX(" + -deletewidth + "px)";
					}
				}

			 });

		})
}
