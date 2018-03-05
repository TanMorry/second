/*!
 * pull to refresh v2.0
 *author:wallace
 *2015-7-22
 */
var refresher = {
	info: {
		"pullDownLable": "下拉刷新...",
		"pullingDownLable": "松开刷新...",
		"pullUpLable": "上拉加载更多...",
		"pullingUpLable": "松手加载更多...",
		"loadingLable": "Loading..."
	},
	parameter: null,
	init: function(parameter) {

		this.parameter = parameter;

		if(parameter.info)
		{
			var that = this.info;
			for (var key in parameter.info)
			{
				that[key] = parameter.info[key];
			}
		}
		var wrapper = document.getElementById(parameter.id);
		var div = document.createElement("div");
		div.className = "scroller";
		wrapper.appendChild(div);
		var scroller = wrapper.querySelector(".scroller");
		var list = wrapper.querySelector("#" + parameter.id + " .iscroll-main");
		scroller.insertBefore(list, scroller.childNodes[0]);
		var pullDown = document.createElement("div");
		pullDown.className = "pullDown";
		var loader = document.createElement("div");
		loader.className = "loader";
		for (var i = 0; i < 4; i++) {
			var span = document.createElement("span");
			loader.appendChild(span);
		}
		pullDown.appendChild(loader);
		var pullDownLabel = document.createElement("div");
		pullDownLabel.className = "pullDownLabel";
		pullDown.appendChild(pullDownLabel);
		scroller.insertBefore(pullDown, scroller.childNodes[0]);
		var pullUp = document.createElement("div");
		pullUp.className = "pullUp";
		var loader = document.createElement("div");
		loader.className = "loader";
		for (var i = 0; i < 4; i++) {
			var span = document.createElement("span");
			loader.appendChild(span);
		}
		pullUp.appendChild(loader);
		var pullUpLabel = document.createElement("div");
		pullUpLabel.className = "pullUpLabel";
		var content = document.createTextNode(refresher.info.pullUpLable);
		pullUpLabel.appendChild(content);
		pullUp.appendChild(pullUpLabel);
		scroller.appendChild(pullUp);
		var pullDownEl = wrapper.querySelector(".pullDown");
		var pullDownOffset = pullDownEl.offsetHeight;
		var pullUpEl = wrapper.querySelector(".pullUp");
		var pullUpOffset = pullUpEl.offsetHeight;

		if (parameter.header === false)
		{
			this.setPull('header', false);
		}
		if (parameter.footer === false)
		{
			this.setPull('footer', false);
		}


		this.scrollIt(parameter, pullDownEl, pullDownOffset, pullUpEl, pullUpOffset);
	},
	rename: function (parm)
	{
		if(typeof(parm) == 'object')
		{
			for (var key in parm)
			{
				this.info[key] = parm[key];
			}
		}
	},
	setPull: function (target, value)
	{
		var wrapper = document.getElementById(this.parameter.id);
		var pullDownEl = wrapper.querySelector(".pullDown");
		var pullUpEl = wrapper.querySelector(".pullUp");

		if( target == 'header' && (value === true || value === false) )
		{
			this.parameter.header = value;

			if (value === true)
			{
				pullDownEl.style.visibility = 'visible';
			}
			if (value === false)
			{
				pullDownEl.style.visibility = 'hidden';
			}
		}
		else if( target == 'footer' && (value === true || value === false) )
		{
			this.parameter.footer = value;

			if (value === true)
			{
				pullUpEl.style.display = 'block';
			}
			if (value === false)
			{
				pullUpEl.style.display = 'none';
			}
		}
	},
	scrollIt: function(parameter, pullDownEl, pullDownOffset, pullUpEl, pullUpOffset) {

		var text = parameter.id + "= new iScroll(parameter.id, {";
		text += "useTransition: false,vScrollbar: true,topOffset: pullDownOffset,";
		text += "onRefresh: function () {refresher.onRelease(pullDownEl,pullUpEl);},";
		text += "onScrollMove: function () {refresher.onScrolling(this,pullDownEl,pullUpEl,pullUpOffset)},";
		if(parameter.start && parameter.end)
		{
			text += "onScrollStart: function () {refresher.ScrollStart(this,parameter.start)},";
			text += "onScrollEnd: function () {refresher.onPulling(this,pullDownEl,parameter.pullDownAction,pullUpEl,parameter.pullUpAction,pullUpOffset);refresher.ScrollEnd(this,parameter.end)},";
		}
		else
		{
			text += "onScrollEnd: function () {refresher.onPulling(this,pullDownEl,parameter.pullDownAction,pullUpEl,parameter.pullUpAction,pullUpOffset)},";
		}
		text += " })";
		eval(text);
		pullDownEl.querySelector('.pullDownLabel').innerHTML = refresher.info.pullDownLable;
		document.addEventListener('touchmove', function(e) {
			e.preventDefault();
		}, false);
	},
	ScrollStart: function (e, fun) {
		fun(e);
	},
	ScrollEnd: function (e, fun) {
		fun(e);
	},
	onScrolling: function(e, pullDownEl, pullUpEl, pullUpOffset) {
		if (e.y > -(pullUpOffset) && this.parameter.header !== false) {
			pullDownEl.id = '';
			pullDownEl.querySelector('.pullDownLabel').innerHTML = refresher.info.pullDownLable;
			e.minScrollY = -pullUpOffset;
		}
		if (e.y > 0 && this.parameter.header !== false) {
			pullDownEl.classList.add("flip");
			pullDownEl.querySelector('.pullDownLabel').innerHTML = refresher.info.pullingDownLable;
			e.minScrollY = 0;
		}
		if ( ( e.scrollerH < e.wrapperH && e.y < (e.minScrollY - pullUpOffset) || e.scrollerH > e.wrapperH && e.y < (e.maxScrollY - pullUpOffset) ) && this.parameter.footer !== false) {

			pullUpEl.style.display = "block";
			pullUpEl.classList.add("flip");
			pullUpEl.querySelector('.pullUpLabel').innerHTML = refresher.info.pullingUpLable;
		}
		if ( ( e.scrollerH < e.wrapperH && e.y > (e.minScrollY - pullUpOffset) && pullUpEl.id.match('flip') || e.scrollerH > e.wrapperH && e.y > (e.maxScrollY - pullUpOffset) && pullUpEl.id.match('flip') ) && this.parameter.footer !== false ) {
			pullDownEl.classList.remove("flip");
			pullUpEl.querySelector('.pullUpLabel').innerHTML = refresher.info.pullUpLable;
		}
		
	},
	onRelease: function(pullDownEl, pullUpEl) {
		if (pullDownEl.className.match('loading') && this.parameter.header !== false) {
			pullDownEl.classList.toggle("loading");
			pullDownEl.querySelector('.pullDownLabel').innerHTML = refresher.info.pullDownLable;
			pullDownEl.querySelector('.loader').style.display = "none"
			pullDownEl.style.lineHeight = pullDownEl.offsetHeight + "px";
		}
		if (pullUpEl.className.match('loading') && this.parameter.footer !== false) {
			pullUpEl.classList.toggle("loading");
			pullUpEl.querySelector('.pullUpLabel').innerHTML = refresher.info.pullUpLable;
			pullUpEl.querySelector('.loader').style.display = "none"
			pullUpEl.style.lineHeight = pullUpEl.offsetHeight + "px";
		}
	},
	onPulling: function(e, pullDownEl, pullDownAction, pullUpEl, pullUpAction,pullUpOffset) {
		if (pullDownEl.className.match('flip') && this.parameter.header !== false /*&&!pullUpEl.className.match('loading')*/ ) {
			pullDownEl.classList.add("loading");
			pullDownEl.classList.remove("flip");
			pullDownEl.querySelector('.pullDownLabel').innerHTML = refresher.info.loadingLable;
			pullDownEl.querySelector('.loader').style.display = "block"
			pullDownEl.style.lineHeight = "20px";
			if (pullDownAction) pullDownAction();
		}
		if (pullUpEl.className.match('flip') && this.parameter.footer !== false /*&&!pullDownEl.className.match('loading')*/ ) {
			pullUpEl.classList.add("loading");
			pullUpEl.classList.remove("flip");
			pullUpEl.querySelector('.pullUpLabel').innerHTML = refresher.info.loadingLable;
			pullUpEl.querySelector('.loader').style.display = "block"
			pullUpEl.style.lineHeight = "20px";
			if (pullUpAction) pullUpAction();
		}
	}
}