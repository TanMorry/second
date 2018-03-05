<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Zh\Controller;
use Common\Controller\HomebaseController;
use Think\Model;
header("Content-type: text/html; charset=utf-8");

class IndexController extends HomebaseController
{
    /**
     * 闲情首页 默认腐向
     */
    public function index()
    {
        $this->display(':index');
    }
}


















