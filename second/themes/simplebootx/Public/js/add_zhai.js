
$(document).ready(function(){
	
	//下拉选择框
	$('.dropkick').dropkick({autoWidth:true});

	// var checkedCP = -1;
	// $(".ss-dd").on('click', '.select_qx', function() {
	// 	if(checkedCP!=-1 && checkedCP != $(this).index()) {
	// 		alert("CP和非CP只能选一个");
	// 		return;
	// 	}
	// 	if($(this).hasClass('checked'))
	// 	{
	// 		$(this).removeClass('checked');
	// 		$("#CPDetailDIV"+$(this).index()).hide();
	// 		checkedCP = -1;
	// 	} else{
	// 		$(this).addClass('checked');
	// 		$("#CPDetailDIV"+$(this).index()).show();
	// 		checkedCP = $(this).index();
	// 	}
	// });
	
	$("#nimingdiv").on('click', function() {
		if($(this).hasClass('checked'))
		{
			$(this).removeClass('checked');
		} else{
			$(this).addClass('checked');
		}
	});

});
function addRole(val)
{
	var i = 1;
	if(val[0] == undefined)
	{
		val[0] = "";
	}
	if(val[1] == undefined)
	{
		val[1] = "";
	}
	if(i>19)
	{
		alert('最多只能添加20对cp');
		return false;
	}
	else
	{
		var str = '<p id="addCP'+i+'"><span>角色名：<input type="text" name="gong[]" class="gong_input" value="'+val[0]+'"/></span> <span>角色名：<input type="text" class="gong_input" name="shou[]" value="'+val[1]+'"/></span><img onclick="closeAddP('+i+')" src="/tongshi/themes/simplebootx/Public/images/trxq/xiaochu_07.png" /></p>'
		$("#gengduoCpDIV").append(str);
	}
	i++;
}

function addMoreRole(val)
{
	alert(i);

	var i = 1;
	if(val == undefined)
	{
		val = "";
	}
	if(i>39)
	{
		alert('最多只能添加40个角色名');
		return false;
	}
	else
	{
		var str = '<div class="detail" id="addCP' + i + '"><div class="spanDiv">角色名：</div><div class="inputtext"><input type="text" name="gong[]" value="'+val+'"/><img onclick="closeAddP(' + i + ')" src="/tongshi/themes/simplebootx/Public/images/trxq/np_close.png" /></div></div>'
		$("#gengduoNpShouDIV").append(str);
	}
	i++;
};

function closeAddP(p) {
	$("#addCP"+p).hide();
}

var j = 3;
function closeAddYuanzuo(p) {
	if(p==j-1) {
		$("#addYuanzuo"+p).remove();
		--j;
	} else {
		alert("先删除最后一个！");
	}
}


/*--------tianlei  2016/4/13-----------*/

function addYuanzuo(authorNum) {

	if(authorNum == undefined)
	{
		authorNum = "";
	}
	var str = '<div class="detail" id="addYuanzuo'+j+'"><div class="spanDiv">&</div><div class="inputtext"><input type="text" value="'+authorNum+'"/><img onclick="closeAddYuanzuo('+j+')" class="img1" src="/tongshi/themes/simplebootx/Public/images/trxq/np_close.png" /></div><div class="title">原作'+j+'：</div></div>';
	$("#yuanzuoDIV").append(str);
	++j;
}
/*--------tianlei  2016/4/13-----------*/


function changeCrossover(op){
	if(op==1){
		$("#fei_crossoverDIV").hide();
		$("#crossoverDIV").show();
	} else {
		$("#fei_crossoverDIV").show();
		$("#crossoverDIV").hide();
	}
}