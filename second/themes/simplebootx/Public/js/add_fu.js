$(document).ready(function(){

	//下拉选择框
	$('.dropkick').dropkick({autoWidth:true});

	$(".ss-dd").on('click', '.select_qx', function() {
		if($(this).hasClass('checked'))
		{
			$(this).removeClass('checked');
			$("#CPDetailDIV"+$(this).index()).hide();
		} else{
			$(this).addClass('checked');
			$("#CPDetailDIV"+$(this).index()).show();
		}
	});
	
	$("#nimingdiv").on('click', function() {
		if($(this).hasClass('checked'))
		{
			$(this).removeClass('checked');
		} else{
			$(this).addClass('checked');
		}
	});
});
var i = 1;
function addCp(val)
{
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
		var str = '<p id="addCP'+i+'"><span>攻：<input type="text" name="gong[]" class="gong_input" value="'+val[0]+'"/></span> <span>受：<input type="text" class="gong_input" name="shou[]" value="'+val[1]+'"/></span><img onclick="closeAddP('+i+')" src="/themes/simplebootx/Public/images/trxq/xiaochu_07.png" /></p>'
		$("#gengduoCpDIV").append(str);
	}
	i++;
}

var b =1;
function addNPGong(val)
{
	if(val == undefined)
	{
		val = "";
	}

	if(b>19)
	{
		alert('最多只能添加20对cp');
		return false;
	}
	else
	{
		var str= '<div class="detail" id="addCP'+b+'"><div class="spanDiv">攻：</div><div class="inputtext"><input type="text" name="gong[]" value="'+val+'"/><img onclick="closeAddP('+b+')" src="/themes/simplebootx/Public/images/trxq/np_close.png" /></div></div>'
		$("#gengduoNpGongDIV").append(str);
	}
	b++;
};

var c = 1;
function addNPShou(val)
{
	if(val == undefined)
	{
		val = "";
	}
	if(c>19)
	{
		alert('最多只能添加20对cp');
		return false;
	}
	else
	{
		var str = '<div class="detail" id="addCP'+c+'"><div class="spanDiv">受：</div><div class="inputtext"><input type="text" name="shou[]" value="'+val+'"/><img onclick="closeAddP('+c+')" src="/themes/simplebootx/Public/images/trxq/np_close.png" /></div></div>'
		$("#gengduoNpShouDIV").append(str);
	}
	c++;
};

var d=1;
/*------百合大众js-------*/
function addRole(val)
{
	if(val[0] == undefined)
	{
		val[0] = "";
	}
	if(val[1] == undefined)
	{
		val[1] = "";
	}

	if(d>19)
	{
		alert('最多只能添加20对cp');
		return false;
	}
		var str = '<p id="addCP'+d+'"><span>角色名：<input type="text" name="gong[]" class="gong_input" value="'+val[0]+'"/></span> <span>角色名：<input type="text" class="gong_input" name="shou[]" value="'+val[1]+'"/></span><img onclick="closeAddP('+d+')" src="/themes/simplebootx/Public/images/trxq/xiaochu_07.png" /></p>'
		$("#gengduoCpDIV").append(str);
		d++;

}

var e=1
function addMoreRole(val)
{
	if(val == undefined)
	{
		val = "";
	}
	if(e>39)
	{
		alert('最多只能添加40个角色名');
		return false;
	}
	else
	{
		var str = '<div class="detail" id="addCP' + e + '"><div class="spanDiv">角色名：</div><div class="inputtext"><input type="text" name="gong[]" value="'+val+'"/><img onclick="closeAddP(' + e + ')" src="/themes/simplebootx/Public/images/trxq/np_close.png" /></div></div>'
		$("#gengduoNpShouDIV").append(str);
	}
	i++;
};
/*------百合大众js-------*/

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

function addYuanzuo(authorNum) {
	if(authorNum == undefined)
	{
		authorNum = "";
	}
	var str = '<div class="detail" id="addYuanzuo'+j+'"><div class="spanDiv">&</div><div class="inputtext"><input type="text" name="authorName[]" value="'+authorNum+'"/><img onclick="closeAddYuanzuo('+j+')" class="img1" src="/themes/simplebootx/Public/images/trxq/np_close.png" /></div><div class="title">原作'+j+'：</div></div>';
	$("#yuanzuoDIV").append(str);
	++j;
}


function changeCrossover(op){
	var str = '<div class="detail1"><input type="text" name="authorName[]" class="gong_input"> <div class="title">原作1：</div></div> <div class="detail"> <div class="spanDiv">&</div> <input type="text" name="authorName[]" class="gong_input" > <div class="title">原作2：</div></div>';
	if(op==1){
		$("#fei_crossoverDIV").hide();
		$("#fei_crossoverDIV").find('input').val("");
		$("#crossoverDIV").show();
	} else {
		$("#fei_crossoverDIV").show();
		$("#crossoverDIV").hide();
		$("#yuanzuoDIV").html(str);

	}
}