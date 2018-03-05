/*movetouch*/
var cannotMove = false;
function movetouch( a ){
	var mouseStartX;
	var mouseMoveX;
	var mouseEndX = 0;
	var canMove = false;
	var isChoose;
	var delHeight = $(a).height() + parseInt($(a).css('padding-top')) + parseInt($(a).css('padding-bottom')) + parseInt($(a).css('margin-top')) + parseInt($(a).css('margin-bottom'));
	var $c;
	var $choose;
	var changeX;
	$(a).parent().css('overflow-x','hidden');
	$(a).css('position','relative');
	$(a).css('overflow','visible');
	$(a).each(function(){
		$(this).prepend('<div class="mtDelete" style="position: absolute;width: 60px;height: 100%;top: 0;right: -60px;background: #959595;font-size: 0.9rem;color: #FFFFFF;text-align: center;display: -webkit-box;-webkit-box-align:center;-webkit-box-pack: center">删除</div>');
	});
	$(a).css('-webkit-transition','-webkit-transform 0.2s');
	// $('.mtDelete').css('height',delHeight + 'px');
	//$('.mtDelete').css('line-height',delHeight + 'px');
	
	
	window.addEventListener('touchstart',function(event){
		if( !cannotMove ){
			mouseStartX = event.targetTouches[0].pageX;
			isChoose = false;
			if( $(event.target).children().parents(a).length == 1 ){
				isChoose = true;
				$c = $(event.target);
			}
			if( $(event.target).parents(a).length == 1 ){
				isChoose = true;
				$c = $(event.target).parents(a);
			}
			if( $(event.target).parents(a).length != 1 && $(event.target).children().parents(a).length != 1){
				$c = null;
			}
			if( $(a).not($c).hasClass('isTurnOn') ){
				canMove = false;
			}
			else{
				canMove = true;
			}
			$(a).not($c).css('transform','translateX(0px)');
			$(a).not($c).removeClass('isTurnOn');
			changeX = 0;
		}
		
	});
	window.addEventListener('touchmove',function(event){
		if(canMove && !cannotMove){
			mouseMoveX = event.targetTouches[0].pageX;
			changeX = mouseMoveX - mouseStartX;
			if( isChoose ){
				if( !$c.hasClass('isTurnOn') ){
					if( changeX > -60 && changeX < 0 ){
						$c.css('transform','translateX(' + changeX + 'px)');
					}	
				}
				else{
					if( changeX > 0 && changeX < 60 ){
						var changeXOn = changeX - 60;
						$c.css('transform','translateX(' + changeXOn + 'px)');
					}
				}
			}
		}
		
	});
	window.addEventListener('touchend',function(event){
		if( isChoose && !cannotMove){
			if( !$c.hasClass('isTurnOn') ){
				if( changeX < -30 ){
					$c.addClass('isTurnOn');
					$c.css('transform','translateX(-60px)');
				}
				else{
					$c.removeClass('isTurnOn');
					$c.css('transform','translateX(0px)');
				}
			}
			else{
				if( changeX < 30 ){
					$c.addClass('isTurnOn');
					$c.css('transform','translateX(-60px)');
				}
				else{
					$c.removeClass('isTurnOn');
					$c.css('transform','translateX(0px)');
				}
			}
		}
	});
	
	
	
	
}
