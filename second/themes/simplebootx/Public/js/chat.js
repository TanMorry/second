
function sendMsg()
{
	alert(1);
	$.ajax({

	     type: 'get',

	     url: '/tongrenmiao/?g=exhibition&m=index&a=send',

	    data: $("#textarea").val(),

	    success: function(data){alert(data)} ,

	});
}
function reciveMsg(data)
{
	
	var description = ""; 
	 for(var i in data){ 
	  var property=data[i]; 
	  description+=i+" = "+property+"\n"; 
	 } 
	 console.log("data:" + description);
	var g = data.getContent();
	var b = "img/head/2024.jpg";
	var a = 3;
	var f = data.getSentTime();
	function h() {
		-1 != g.indexOf("*#emo_") && (g = g.replace("*#", "<img src='img/").replace("#*", ".gif'/>"), h())
	}
	h();
	var i = "<div class='message clearfix'><div class='user-logo'><img src='" + b + "'/>" + "</div>" + "<div class='wrap-text'>" + "<h5 class='clearfix'>\u5f20\u98de</h5>" + "<div>" + g + "</div>" + "</div>" + "<div class='wrap-ri'>" + "<div clsss='clearfix'><span>" + g + "</span></div>" + "</div>" + "<div style='clear:both;'></div>" + "</div>" ;
	$(".mes" + a).append(i), $(".chat01_content").scrollTop($(".mes" + a).height());

}
$(document).ready(function() {
		function e() {
			function h() {
				-1 != g.indexOf("*#emo_") && (g = g.replace("*#", "<img src='img/").replace("#*", ".gif'/>"), h())
			}
			var e = new Date,
				f = "";
			f += e.getFullYear() + "-", f += e.getMonth() + 1 + "-", f += e.getDate() + "  ", f += e.getHours() + ":", f += e.getMinutes() + ":", f += e.getSeconds();
			var g = $("#textarea").val();
			h();
			var i = "<div class='message clearfix'><div class='user-logo'><img src='" + b + "'/>" + "</div>" + "<div class='wrap-text'>" + "<h5 class='clearfix'>\u5f20\u98de</h5>" + "<div>" + g + "</div>" + "</div>" + "<div class='wrap-ri'>" + "<div clsss='clearfix'><span>" + f + "</span></div>" + "</div>" + "<div style='clear:both;'></div>" + "</div>" ;
			null != g && "" != g ? ($(".mes" + a).append(i), $(".chat01_content").scrollTop($(".mes" + a).height()), $("#textarea").val("")) : alert("\u8bf7\u8f93\u5165\u804a\u5929\u5185\u5bb9!")
			sendMsg();
		}
		var a = 3,
			b = "img/head/2024.jpg",
			c = "img/head/2015.jpg",
			d = "\u738b\u65ed";
		$(".close_btn").click(function() {
			$(".chatBox").hide()
		}), $(".chat03_content li").mouseover(function() {
			$(this).addClass("hover").siblings().removeClass("hover")
		}).mouseout(function() {
			$(this).removeClass("hover").siblings().removeClass("hover")
		}), $(".chat03_content li").dblclick(function() {
			var b = $(this).index() + 1;
			a = b, c = "img/head/20" + (12 + a) + ".jpg", d = $(this).find(".chat03_name").text(), $(".chat01_content").scrollTop(0), $(this).addClass("choosed").siblings().removeClass("choosed"), $(".talkTo a").text($(this).children(".chat03_name").text()), $(".mes" + b).show().siblings().hide()
		}), $(".ctb01").mouseover(function() {
			$(".wl_faces_box").show()
		}).mouseout(function() {
			$(".wl_faces_box").hide()
		}), $(".wl_faces_box").mouseover(function() {
			$(".wl_faces_box").show()
		}).mouseout(function() {
			$(".wl_faces_box").hide()
		}), $(".wl_faces_close").click(function() {
			$(".wl_faces_box").hide()
		}), $(".wl_faces_main img").click(function() {
			var a = $(this).attr("src");
			$("#textarea").val($("#textarea").val() + "*#" + a.substr(a.indexOf("img/") + 4, 6) + "#*"), $("#textarea").focusEnd(), $(".wl_faces_box").hide()
		}), $(".chat02_bar img").click(function() {
			e()
		}), document.onkeydown = function(a) {
			var b = document.all ? window.event : a;
			return 13 == b.keyCode ? (e(), !1) : void 0
		}, $.fn.setCursorPosition = function(a) {
			return 0 == this.lengh ? this : $(this).setSelection(a, a)
		}, $.fn.setSelection = function(a, b) {
			if (0 == this.lengh) return this;
			if (input = this[0], input.createTextRange) {
				var c = input.createTextRange();
				c.collapse(!0), c.moveEnd("character", b), c.moveStart("character", a), c.select()
			} else input.setSelectionRange && (input.focus(), input.setSelectionRange(a, b));
			return this
		}, $.fn.focusEnd = function() {
			this.setCursorPosition(this.val().length)
		}
	})(jQuery);