<?php
/**
 * @param $tagNum  标签
 * @param $type    1=>闲情标签   2=>闲情文库标签
 * 闲情下的标签
 */
function XqTag($tagNum,$type)
{
    if(!isset($tagNum))
    {
        return false;
    }
    if($type == 1)
    {
        if($tagNum == 0)
        {
            return array(1=>'聊天',2=>'生活',3=>'系列',4=>'求文',5=>'贴图',6=>'公告');
        }
        else
        {
            switch ($tagNum)
            {
                case 1:
                    return "聊天";
                    break;
                case 2:
                    return "生活";
                    break;
                case 3:
                    return "系列";
                    break;
                case 4:
                    return "求文";
                    break;
                case 5:
                    return "贴图";
                    break;
                case 6:
                    return "公告";
                    break;
                default:
                    echo '没有该标签';
            }
        }
    }
    else if($type == 2)
    {
        if($tagNum == 0)
        {
            return array(1=>'连载',2=>'完结',3=>'公告');
        }
        else
        {
            switch ($tagNum)
            {
                case 1:
                    return "连载";
                    break;
                case 2:
                    return "完结";
                    break;
                case 3:
                    return "公告";
                    break;
                default:
                    echo '没有该标签';
            }
        }
    }
    else
    {
        return false;
    }
}

/**
 * 帖子筛选状态
 */
function stateList()
{
    return array(1=>'正常',2=>'加精',3=>'套红',4=>'加精，套红',5=>'已屏蔽',6=>'已屏蔽（精，红）',7=>'已关闭',8=>'已关闭（精，红）');
}

/**
 * 文库文章标签显示
 * @param string  $list  文章列表
 * @param int     $isDetail  1=>标题   2=>详情
 */
function articleTag(&$list,$isDetail)
{
    //查出当前属性下的文章标签
    $allTag = M('mf_tag')->select();
    foreach($list as $key=>$val)
    {
        if($val['tagid'] != "")
        {
            $tagId = explode(',',$val['tagid']);
            foreach($tagId as $va)
            {

                foreach ($allTag as $v)
                {
                    if($va == $v[id])
                    {
//                        $list[$key] = $v['name'];
                        $newArr[$key][] = $v['name'];
                    }
                }
            }

            if($isDetail == 1)
            {
                if(count($newArr[$key])>3)
                {
                    $list[$key]['tagInfo'][0] = $newArr[$key][0];
                    $list[$key]['tagInfo'][1] = $newArr[$key][1];
                    $list[$key]['tagInfo'][2] = $newArr[$key][3];
                    $list[$key]['tagInfo'][3] = '...';
                }
                else
                {
                    $list[$key]['tagInfo'] = $newArr[$key];
                }
            }
            else
            {
                $list[$key]['tagInfo'] = $newArr[$key];

            }
        }
    }

//    return $list;
}

/**
 * 分页
 * @param $model       实例化对象
 * @param $conditions  查询条件
 * @param $pageNum     显示条数
 * @param $type        文库搜索（关联标签表）
 */
function getPage($model,$conditions,$pageNum,$type)
{
    $module_name = 'index.php?g=' . MODULE_NAME . '&m=' . CONTROLLER_NAME . '&a=' . ACTION_NAME;//获取路径
//    echo "页面分页跳转地址(getPage方法)：".$module_name."<br>";
    $m = M("$model");
    if($type)
    {
        $count = $m
               ->join('left join cmf_xq_reply r on t.id = r.tid')
               ->join('left join '.C('DB_PREFIX').'mf_tag m ON FIND_IN_SET(m.id,t.tagid)')
               ->where($conditions)
               ->count('DISTINCT t.id');// 查询满足要求的总记录数
    }
    else
    {
        $count = $m->join('left join cmf_xq_reply r on t.id = r.tid')->where($conditions)->count();// 查询满足要求的总记录数
    }
    $Page = new \Think\Page($count,$pageNum);// 实例化分页类 传入总记录数和每页显示的记录数
    $Page->lastSuffix = false; // 最后一页是否显示总页数
    $Page->setConfig('prev', '上一页');
    $Page->setConfig('next', '下一页');
    $Page->setConfig('first', '首页');
    $Page->setConfig('last', '末页');

    $Page->setConfig('theme', '%FIRST% %UP_PAGE% %DOWN_PAGE% %END%<span class="bottom_skip"><span id="jump">&nbsp;跳转到&nbsp;<input type="tel" size="4" class="skiptext" style="width:30px;border:1px solid #ccc;" id="z"/>&nbsp;页&nbsp;<a class="tiaozhuan" href="' . $module_name . '&p=" id="test" >跳转</a></span>&nbsp;<span>当前第%NOW_PAGE%页</span>&nbsp;<span title="%TOTAL_PAGE%" class="all">共&nbsp;<b id="totalPage">%TOTAL_PAGE%</b>&nbsp;页</span></span>');
    return $Page;
}

/**
 * 前台角色类型显示（拼接）
 * @param  $list      结果集
 * @param  $isDetail  是否是详情  1=>是(显示所有的角色类型)  2=>否(根据规则显示)
 */
function showCharacter(&$list,$isDetail)
{
    if($isDetail == 2)
    {
        foreach($list as $key=>$val){
            $author = explode('&&&&',$val['authorname']);
            if(count($author)>2)
            {
                $list[$key]['author'] = '【'.$author[0].' + '.$author[1].' +...'.'】';
            }
            else
            {
                $str = $author[0].' + '.$author[1];
                $list[$key]['author'] = '【'.trim($str,' + ').'】';
            }
            if($val['cptype'] == 1)
            {
                $cpArr = explode(',',$val['cp']);
                $tempArr = str_replace('&&&&','X',$cpArr);
                if(count($tempArr)>1)
                {
                    $list[$key]['cpname'] = '【'.$tempArr[0].' ,...'.'】';
                }
                else
                {
                    $list[$key]['cpname'] = '【'.$tempArr[0].'】';
                }
            }
            if($val['cptype'] == 2)
            {
                $cpArr = explode('&&&&',$val['cp']);
                $tempArr = explode(',',$cpArr[0]);
                $tempArr2 = explode(',',$cpArr[1]);
                if(count($tempArr)>2)
                {
                    $gongStr = $tempArr[0].','.$tempArr[1].'...';
                }
                else
                {
                    $gongStr = $tempArr[0].','.$tempArr[1];
                }
                if(count($tempArr2)>2)
                {
                    $shouStr = $tempArr2[0].','.$tempArr2[1].'...';
                }
                else
                {
                    $shouStr = $tempArr2[0].','.$tempArr2[1];
                }
                $list[$key]['cpname'] = '【'.trim($gongStr,',').'X'.trim($shouStr,',').'】';
            }
            if($val['cptype'] == 3)
            {
                $cpArr = explode(',',$val['cp']);
                if(count($cpArr)>2)
                {
                    $list[$key]['cpname'] = '【'.$cpArr[0].','.$cpArr[1].' ,...'.'】';
                }
                else
                {
                    $list[$key]['cpname'] = '【'.$cpArr[0].$cpArr[1].'】';
                }
            }
        }
    }
    else
    {
        //详情（全部显示）
        foreach($list as $key=>$val)
        {
            $list[$key]['author'] = '【'.str_replace('&&&&','+',$val['authorname']).'】';
            if($val['cptype'] == 1 || $val['cptype'] == 2)
            {
                $list[$key]['cpname'] = '【'.str_replace('&&&&','X',$val['cp']).'】';
            }
            if($val['cptype'] == 3)
            {
                $list[$key]['cpname'] = '【'.$val['cp'].'】';
            }
        }
    }
//    return $list;

}

/**
 * 显示图片缩略图（只用于闲情）
 * 正则匹配取出图片地址，放在查询的数据结果集中，前台模板显示直接循环取出
 * @param  data  $list
 */
function addPhoto($list)
{
    $pattern ='<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>';//匹配有问题
    foreach($list as $key=>$val)
    {
        $new = htmlspecialchars($val['content'], ENT_QUOTES );
        preg_match_all($pattern,$new,$match);
        if(count($match[1])>0)
        {
            foreach($match[1] as $ke=>$v)
            {
                $match[1][$ke] = trim($v,'&quot;');
            }
            if(count($match[1]) > 3)
            {
                $list[$key]['imgSrc'][0] = $match[1][0];
                $list[$key]['imgSrc'][1] = $match[1][1];
                $list[$key]['imgSrc'][2] = $match[1][2];
            }
            else
            {
                $list[$key]['imgSrc'] = $match[1];
            }
        }
    }
    return $list;
}

/*
 * 将文章中的图片变成‘【图】’，文字取前50个字（去掉标签，提取前五十字符）
 * @param       $content   内容
 */
//$contentes = preg_replace("/<[^>]+>/", '', $contentes);
//echo cut_str($contentes, 100, 0, 'gb2312')
function handleReply($content,$key)
{
    //有图片将图片显示‘【图】’,最大三张
    //截取文章前50个字符
    $contentes = preg_replace("/<[^>]+>/", '', $content);
    if(strlen($contentes) > 150)//通过统计字节数的长度 GBK/GB2312下，中文字符占2个字节，而在UTF-8下，中文字符占3个字节
    {
        $shortText = mb_substr($contentes,0,50,'utf-8').'...';
    }
    else
    {
        $shortText = mb_substr($contentes,0,50,'utf-8');
    }

    $pattern ='<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>';//将图片转换为‘【图】’
    $new = htmlspecialchars($content, ENT_QUOTES );
    preg_match_all($pattern,$new,$match);
    $matchNum = count($match[1]);
    $str="";
    if($matchNum>0)
    {
        if($matchNum>3)
        {
            $str = '【图】【图】【图】';
        }
        else
        {
            $str .= '【图】';
        }
    }
    else
    {
        $str = '';//没有显示空默认标签也要显示（待处理）
    }
    $newContent[pic] = $str;
    $newContent[text] = $shortText;
    return $newContent;
}

/*
 * 过滤文章和标题的敏感字段（替换为‘OO’）
 * @param   $text   需要过滤的内容
 */
//function replaceSubtle($text)
//{
//    if($text == '')
//    {
//        return false;
//    }
//    $doc = new\DOMDocument();
//    $doc->load("./Public/MaskWords.xml");         //读取xml文件
////    var_dump($doc->load("./Public/MaskWords.xml"));die;
//    $root = $doc->getElementsByTagName('w');      //取得w标签的对象数组
//
//    $len = $root->length;                         //取得w标签的数量
//    //    $str = $root->item(0)->getAttribute('t');    //获取属性值
//    $arr = array();
//    for($i=0;$i<$len;$i++)
//    {
//        echo '"'.$root->item($i)->getAttribute('t').'",';
//    }
//    die;
//    $result = str_replace($arr,'OO',$text);
//    return $result;
//}













