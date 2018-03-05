function Arguments()
{
    this.Request = function()
    {
        var url = location.search; //获取url中"?"符后的字串
        var theRequest = new Object();
        if (url.indexOf("?") != -1) {
            var str = url.substr(1);
            strs = str.split("&");
            for(var i = 0; i < strs.length; i ++) {
                theRequest[strs[i].split("=")[0]]=(strs[i].split("=")[1]);
            }
        }
        return theRequest;
    }
    this.Page = function(url,data)
    {
        if($(".footer input").val() == "")
        {
            $("#go").css('background','#999');
            return false;
        }
        /*输入数量验证*/
        $(".footer input").on('blur input propertychange', function(event) {

            function isEmpty (val)
            {
                return val == '' || val === null || val === undefined;
            }

            var val = $(this).val();
            /*如果不是空则强转数字*/
            if (!isEmpty(val))
            {
                val = parseInt(val.replace(/[^\d]/ig,''), 10);
            }

            isNaN(val) ? $(this).val("") : $(this).val(val);

            if(type == 1)
            {
                if(val > countPage)
                {
                    $("#go").css('background','#999');
                    return false;
                }
                else
                {
                    $("#go").css('background','#eb6877');
                }
            }
            else
            {
                if(val > countPage2)
                {
                    $("#go").css('background','#999');
                    return false;
                }
                else
                {
                    $("#go").css('background','#eb6877');
                }
            }

            // if(event.type == 'blur' || event.type == 'focusout')
            // {
            // 	if(isEmpty(val))
            // 	{
            // 		$(this).val(1);
            // 	}
            // }
        });
    }
}