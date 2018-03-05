function Slide(obj,direction,speed){ //面向对象的方法，可以自由控制方向，speed=>3 ie下可以
    this.container=document.getElementById(obj);
    this.content=this.container.getElementsByTagName("ul")[0];
    this.lis=this.content.getElementsByTagName("li");
    this.a = this.container.getElementsByTagName("a");
    // this.content.innerHTML+=this.content.innerHTML;
    // this.content.style.width=(this.lis[0].offsetWidth)*(this.lis.length)+"px";
    this.content.style.width=(this.lis[0].offsetWidth*this.lis.length)+(this.a[0].offsetWidth * this.a.length)+10 +"px";//加上了a标签里面的宽度
    // debugger;
    var that=this
    if(direction=="left"){
        this.speed=-speed
    }else if(direction=="right"){
        this.speed=speed
    }
    Slide.prototype.scroll=function(){
        this.content.style.left=this.content.offsetLeft+this.speed+"px";
        if(this.content.offsetLeft <= -this.content.offsetWidth){
            this.content.style.left ="0px";
        }else if(this.content.offsetLeft >=0){
            this.content.style.left = -this.content.offsetWidth + "px";
        }
    }
    this.time=setInterval(function(){that.scroll()},100);
    this.container.onmouseover=function(){
        clearInterval(that.time);
    }
    this.container.onmouseout=function(){
        that.time=setInterval(function(){that.scroll()},100);
    }
    // debugger;
}