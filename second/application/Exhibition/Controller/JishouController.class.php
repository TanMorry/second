<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Exhibition\Controller;
use Common\Controller\HomebaseController; 
/**
 * 首页
 */
class JishouController extends HomebaseController {
	
    //首页
	public function index() {
		
		$id=session('id_sponsor');
        echo $id;
		
		$users_model=M("sponsor");

		$user=$users_model->find($id);
		echo $user[truename];
		//var_dump($user);
		if(empty($user)){
			$this->error("查无此人！");
		}

    }
    
    public function jishouapply() {
    	file_put_contents("sucaihuo.txt", date("Y-m-d H:i:s") . "\r\n", FILE_APPEND);
    	$id=session('id_basic_exhibition');//接收展会id
    	echo $id;
     	$zhanhui_model=M("basic_exhibition");
    	$zhanhui=$zhanhui_model->find($id);
    	if(empty($zhanhui)){
    		$this->error("查询不到展会！");
    	}
    	$info_model=M("stand_service");
    	$data['id_exhibition']=$id;
    	$info_model->add($data);
    	$info=$info_model->where("id_exhibition='$id'")->find();
    	file_put_contents("sucaihuo.txt", $info_model->getlastsql() . "\r\n", FILE_APPEND);
    	echo $zhanhui[name];
    	echo "id_exhibition:".$id;
    	echo '</br>';
    	echo $zhanhui_model->end;
    	
    	//var_dump($user);

        if(empty($info)){

        	$this->error("查询基本信息失败");
        }
    
    	$this->assign('zhanhui',$zhanhui);
        $this->assign('info',$info);
    	$this->display(":jishou/jishouapply");
    }
    
    public function pending() {
    

    	$this->display(":jishou/pending");
    }
    
  
}


