function choose(obj)
{
    if ($(obj).hasClass('selected'))
    {
        $(obj).removeClass('selected');
    }
    else
    {
        $(obj).addClass('selected');
        $(obj).siblings().removeClass('selected');
    }
    //获取链接赋值给dom中的隐藏用来提交
    $("input[name=systemHead]").val($(obj).find('img').attr('src'));
}