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
class IndexController extends HomebaseController {
	
    //首页
	public function index() {		
		$token = json_decode(getToken(sp_get_current_userid(),'2','3'),true);
		$this->assign($token);		
    	$this->display(":index");
    }
    
    public function send() {
    	$receveid = I('get.receveid');
    	$content = I('get.content');
    	$model = M('usermessage');
    	$model->add(array(
    			'sendId'=>sp_get_current_userid(),
    			'recevieId'=>$receveid,
    			'content'=>$content,
    			'sendtime'=>date('y/m/d h:m:s'),   			
    			));
    	echo messagePublish(sp_get_current_userid(),$receveid,'RC:TxtMsg','{"content":"hello","extra":"helloExtra"}','','');
    }
    
    public function chat()
    {
    	$token = json_decode(getToken(sp_get_current_userid(),'2','3'),true);
    	$this->assign($token);
    	$this->display(":chat");
    }
    
    public function getChatUsers()
    {
    	$sql = "SELECT DISTINCT(sendId),user_nicename,sendtime,avatar from cmf_usermessage as a LEFT JOIN cmf_users as b ON a.sendId=b.id where recevieId = ".sp_get_current_userid()." GROUP BY sendId"; 	    	
    	$model = M();
    	$list = $model->query($sql);
    	$ids = array();
    	for ($i = 0 ;$i<count($list);$i++)
    	{
    		$list[$i]['avatar'] = sp_get_user_avatar_url($list[$i]['avatar']);
    		array_push($ids, $list[$i]['sendid']);
    	}
    	$sql1 = "SELECT DISTINCT(recevieId),user_nicename,sendtime,avatar from cmf_usermessage as a LEFT JOIN cmf_users as b ON a.recevieId=b.id where sendId = ".sp_get_current_userid()." GROUP BY recevieId";
    	$list1 = $model->query($sql1);
    	for ($i = 0 ;$i<count($list1);$i++)
    	{	
    		if(array_search($list1[$i]['recevieid'], $ids) == -1)
    		{
    			$list1[$i]['avatar'] = sp_get_user_avatar_url($list1[$i]['avatar']);
    			array_push($list, $list1[$i]);
    		}
    		
    	}    	    	    	
    	$rt = array(
    			"status"=>1,
    			"msg" => "ok",
    			"data"=>$list
    			);
    	$rt = json_encode($rt);
    	echo $rt;
    }
    
    public function getMsgs()
    {
    	$chatuserid = I('get.chatid');
    	$sql = "SELECT sendId,user_nicename,avatar,content,recevieId,sendtime from cmf_usermessage as a LEFT JOIN cmf_users as b ON a.sendId=b.id where (recevieId = ".$chatuserid."  and  sendId=".sp_get_current_userid().")or(recevieId = ".sp_get_current_userid()."  and  sendId=".$chatuserid.")";
    	$model = M();
    	$list = $model->query($sql);
    	for ($i = 0 ;$i<count($list);$i++)
    	{
    	$list[$i]['avatar'] = sp_get_user_avatar_url($list[$i]['avatar']);
    	}
    	$rt = json_encode($list);
    	
    	echo $rt;
    }
    
    public function getLeastSend()
    {
    	$chatuserid = I('get.sendid');
    	$sql = "SELECT sendId,user_nicename,avatar,content,recevieId,sendtime from cmf_usermessage as a LEFT JOIN cmf_users as b ON a.sendId=b.id where recevieId = ".sp_get_current_userid()." and  sendId=".$chatuserid." ORDER BY a.id desc  limit 1";
    	$model = M();
    	$list = $model->query($sql);
    	for ($i = 0 ;$i<count($list);$i++)
    	{
    	$list[$i]['avatar'] = sp_get_user_avatar_url($list[$i]['avatar']);
    	}
    	$rt = json_encode($list);
    	echo $rt;
    }
    //主办方账号注册
    public function reg()
    {
    	if(isset($_POST['username']))
    	{
    		$rules = array(
    				//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
    				array('username', 'require', '用户名不能为空！', 1 ),
    				array('userpwd','require','密码不能为空！',1),
    		);
    		
    		$model = M("sponsor");
    		 
    		if($model->validate($rules)->create()===false){
    			$this->error($model->getError());
    		}
    		 
    		$password=$_POST['userpwd'];
    		$mobile=$_POST['repwd'];
    		 
    		if(strlen($password) < 5 || strlen($password) > 20){
    			$this->error("密码长度至少5位，最多20位！");
    		}
    		 
    		
    		$data=array(
    				'username'=>$_POST['username'] ,
    				'nickname'=>$_POST['nickname'] ,
    				'checkphone'=>$_POST['checkphone'] ,
    				'userpwd'=>$_POST['userpwd'] ,
    		);
    		$rst = $model->add($data);
    		if($rst){
    			//登入成功页面跳转
    			$data['id']=$rst;
    			$_SESSION['user']=$data;
    			$this->success("注册成功！",__ROOT__."/");
    		
    		}else{
    			$this->error("注册失败！",U("user/register/index"));
    		}
    		 
    	}
    	else
    	{

    		$this->display(":reg");
    	}
    	
    }
    
   
}


