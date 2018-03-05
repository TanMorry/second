<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Exhibition\Controller;
use User\Controller\LoginController;

use Common\Controller\HomebaseController; 
/**
 * 首页
 */
class SponsorController extends HomebaseController {
	
    //首页
	public function index() {
		
		$id=I("get.id");
		
		$users_model=M("sponsor");
		
		$user=$users_model->find();
		//var_dump($user);
		if(empty($user)){
			$this->error("查无此人！");
		}
		$this->assign($user);
    	$this->display(":sponsor/index");
    }
    
    
    //主办方账号注册
    public function reg()
    {

    	if(isset($_POST['username']))
    	{
    		$rules = array(
    				//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
    				array('username', 'require', '用户名不能为空！', 1 ),
    				//array('username','email','需要邮箱格式！',1),
    				//array('username','','该用户名注册过了',1,unique,3),
    				//array('nickname', 'require', '昵称不能为空！', 1 ),
    				array('userpwd','require','密码不能为空！',1),
    				//array('userpwd','5,20','密码需要5~20位	！',3,'length'),
    				//array('repassword','userpwd','确认密码不正确。',0,'confirm'),
    				//array('checkwenzhang','require','主办方协议须知！',1),
    		);
    
    		$model = M("sponsor");
    		
    		if($model->validate($rules)->create()===false){
    			$this->error($model->getError());
    		}
    		
    		//$password=$_POST['userpwd'];
    		//$mobile=$_POST['repwd'];
    		 
    		//if(strlen($password) < 5 || strlen($password) > 20){
    		//	$this->error("密码长度至少5位，最多20位！");
    		//}
    		 
    		//$data=array();
    		$data=array(
    				'username'=>$_POST['username'] ,
    				//'nickname'=>$_POST['nickname'] ,
    				//'checkphone'=>$_POST['checkphone'] ,
    				'userpwd'=>sp_password($_POST['userpwd']) ,
    		);

    		$rst = $model->add($data);
    		if($rst){
    			//$arr['flag']=true;
    			//$bbbb=json_encode($arr,0);
    			//echo $bbbb;
    			
    			//$this->ajaxReturn ($arr,'JSON');
    			//登入成功页面跳转
    			$data['id']=$rst;
    			//$id_sponsor=$rst;
    			//$_SESSION['id_sponsor']=$rst;
    			$_SESSION['user']=$data;
    			$_SESSION['id_sponsor']=$rst;
    			//$this->assign('id_sponsor',$rst);
    			$this->success("第".$data['id']."位用户注册成功！",U("Exhibition/sponsor/apply"));
    
    		}else{
    			$this->error("注册失败！",U("Exhibition/sponsor/reg"));
    		}
    		 
    	}
    	else
    	{

  	      
    		$this->display(":sponsor/reg");
    	}
    	 
    }    
    public function zhubanfangxuzhi()
    {
    	$this->display(':sponsor/zhubanfangxuzhi');
    }
    
    public function login()
    {
    	if(isset($_POST['username']))
    	{
    		$rules = array(
    				//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
    				array('username', 'require', '用户名不能为空！', 1 ),
    				array('userpwd', 'require', '密码不能为空！', 1 ),
    				
    		);
    		 
    		$model = M("sponsor");
    		 
    		if($model->validate($rules)->create()===false){
    			$this->error($model->getError());
    		}
    		if(!sp_check_verify_code()){
    			$this->error("验证码错误！");
    		}
    		$users_model=M('sponsor');
    		$where['username']=$_POST['username'];
    		$username=$_POST['username'];
    		$password=$_POST['userpwd'];
    		$result = $users_model->where($where)->find();
    		$ucenter_syn=C("UCENTER_ENABLED");
    		
    		$ucenter_old_user_login=false;
    		
    		$ucenter_login_ok=false;
    		if($ucenter_syn){
    			setcookie("thinkcmf_auth","");
    			include UC_CLIENT_ROOT."client.php";
    			list($uc_uid, $username, $password, $email)=uc_user_login($username, $password);
    			 
    			if($uc_uid>0){
    				if(!$result){
    					$data=array(
    							'user_login' => $username,
    							'user_email' => $email,
    							'user_pass' => sp_password($password),
    							'last_login_ip' => get_client_ip(0,true),
    							'create_time' => time(),
    							'last_login_time' => time(),
    							'user_status' => '1',
    							'user_type'=>2,
    					);
    					$id= $users_model->add($data);
    					$data['id']=$id;
    					$result=$data;
    				}
    		
    			}else{
    				 
    				switch ($uc_uid){
    					case "-1"://用户不存在，或者被删除
    						if($result){//本应用已经有这个用户
    							if(sp_compare_password($password, $result['userpwd'])){//本应用已经有这个用户,且密码正确，同步用户
    								$uc_uid2=uc_user_register($username, $password, $result['username']);
    								if($uc_uid2<0){
    									$uc_register_errors=array(
    											"-1"=>"用户名不合法",
    											"-2"=>"包含不允许注册的词语",
    											"-3"=>"用户名已经存在",
    											"-4"=>"Email格式有误",
    											"-5"=>"Email不允许注册",
    											"-6"=>"该Email已经被注册",
    									);
    									$this->error("同步用户失败--".$uc_register_errors[$uc_uid2]);
    									 
    									 
    								}
    								$uc_uid=$uc_uid2;
    							}else{
    								$this->error("密码错误！");
    							}
    						}
    		
    						break;
    					case -2://密码错
    						if($result){//本应用已经有这个用户
    							if(sp_compare_password($password, $result['userpwd'])){//本应用已经有这个用户,且密码正确，同步用户
    								$uc_user_edit_status=uc_user_edit($username,"",$password,"",1);
    								if($uc_user_edit_status<=0){
    									$this->error("登陆错误！");
    								}
    								list($uc_uid2)=uc_get_user($username);
    								$uc_uid=$uc_uid2;
    								$ucenter_old_user_login=true;
    							}else{
    								$this->error("密码错误！");
    							}
    						}else{
    							$this->error("密码错误！");
    						}
    						 
    						break;
    						 
    				}
    			}
    			$ucenter_login_ok=true;
    			echo uc_user_synlogin($uc_uid);
    		}
    		//exit();

    		if(!empty($result)){
    			
    			if(sp_compare_password($password, $result['userpwd'])|| $ucenter_login_ok){
    				$_SESSION["sponsor"]=$result;
    				//写入此次登录信息
    				$data = array(
    						'last_login_time' => date("Y-m-d H:i:s"),
    						'last_login_ip' => get_client_ip(0,true),
    				);
    				$users_model->where("id_sponsor=".$result["id_sponsor"])->save($data);
    				
    				//$redirect=empty($_SESSION['login_http_referer'])?__ROOT__."/":$_SESSION['login_http_referer'];
    				//$_SESSION['login_http_referer']="";
    				$ucenter_old_user_login_msg="";
    		
    				if($ucenter_old_user_login){
    					//$ucenter_old_user_login_msg="老用户请在跳转后，再次登陆";
    				}
    		
    				$this->success("登录验证成功！",U("exhibition/sponsor/apply"));
    			}else{
    				$this->error("密码错误！");
    			}
    		}else{
    			$this->error("用户名不存在！");
    		}
    	}
    	else
    	{
    		$this->display(":sponsor/login");
    	}
    	
    }
    //主办方申请
    public function apply()
    {
    	
    	if(isset($_POST['truename']))
    	{
    		$rules = array(
    				//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
    				array('id_sponsor','','该账户注册过了',1,unique,3),
    				array('truename', 'require', '姓名不能为空！', 1 ),
    				array('identityimg1','require','身份证正面不能为空！',1),
    				array('identityimg2','require','身份证反面不能为空！',1),
    				array('phone','','此手机已经注册',1,unique,3),
    				array('phone','number','手机号必须数字！',1),
    				array('phone','11','手机号长度不符！',3,'length'),
    				array('qq','3,20','qq长度不符！',3,'length'),
    				array('qq','require','qq不能为空！',1),
    				array('qq','','此qq已经注册',1,unique,3),
    				array('email','email','邮箱格式错误！',1),
    				array('email','','此邮箱已经注册',1,unique,3),
    				array('s_province','require','省不能为空！',1),
    				array('s_city','require','市不能为空！',1),
    				array('s_county','require','区不能为空！',1),
    				array('address','require','目前所在地址不能为空！',1),
    				array('homeurl','url','官方链接不能为空！',1),
    				array('iscreate','require','是否举办过展会不能为空！',1),
    		);
    	
    		$model = M("sponsor");
    		 
    		if($model->validate($rules)->create()===false){
    			$this->error($model->getError());
    		}
    		 
    		 
    	
    		$data=array(
    				'id_sponsor'=>$_POST['id_sponsor'] ,
    				'truename'=>$_POST['truename'] ,
    				'identityimg1'=>$_POST['identityimg1'] ,
    				'identityimg2'=>$_POST['identityimg2'] ,
    				'phone'=>$_POST['phone'] ,
    				'qq'=>$_POST['qq'] ,
    				'email'=>$_POST['email'] ,
    				'address'=>$_POST['address'] ,
    				'homeurl'=>$_POST['homeurl'] ,
    				'iscreate'=>$_POST['iscreate'] ,
    				'oldurl'=>$_POST['oldurl'] ,
    				'decription'=>$_POST['decription'] ,
    				'decimgs'=>$_POST['decimgs'] ,
    				'applytype'=>$_POST['applytype'],
    	
    		);
    		$rst = $model-> save($data);
    		if($rst){
    			//登入成功页面跳转
    	
    			$this->success("填写成功！".$user[id],U("Exhibition/sponsor/pending"));
    	
    		}else{
    			$this->error("注册失败！",U("Exhibition/sponsor/iapply"));
    		}
    		 
    	}
    	else
    	{
    		$this->display(":sponsor/apply");
    	}
    }
    //主办方还是个人跳转
    public function ioapplyselect()
    {
    	$this->display(":sponsor/ioapplyselect");
    }
    //主办方还是个人审核界面
    public function pending()
    {
    	$this->display(":sponsor/pending");
    }
    //主办方个人申请
    public function iapply()
    {
    	$model = M("sponsor");
    	$jiance=$model->find($_SESSION['id_sponsor']);
    	if ($jiance[truename]!=false) {
    		$this->error("有过注册，请登录",U('sponsor/Login'))
    		;
    	}
    	if(isset($_POST['truename']))
    	{
    		$rules = array(
    				//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
    				array('id_sponsor', 'require', '没有注册！', 1 ),
    				array('truename', 'require', '姓名不能为空！', 1 ),
    				array('identityimg1','require','身份证正面不能为空！',1),
    				array('identityimg2','require','身份证反面不能为空！',1),
    				array('phone','','此手机已经注册',1,unique,3),
    				array('phone','number','手机号必须数字！',1),
    				array('phone','11','手机号长度不符！',3,'length'),
    				array('qq','3,20','qq长度不符！',3,'length'),
    				array('qq','require','qq不能为空！',1),
    				array('qq','','此qq已经注册',1,unique,3),
    				array('email','email','邮箱格式错误！',1),
    				array('email','','此邮箱已经注册',1,unique,3),
    				array('s_province','require','省不能为空！',1),
    				array('s_city','require','市不能为空！',1),
    				array('s_county','require','区不能为空！',1),
    				
    				array('address','require','目前所在地址不能为空！',1),
    				array('homeurl','url','官方链接不能为空！',1),
    				array('iscreate','require','是否举办过展会不能为空！',1),	
    		);
    

    		 
    		if($model->validate($rules)->create()===false){
    			$this->error($model->getError());
    		}
    		 

    
    		$data=array(
    				'id_sponsor'=>$_POST['id_sponsor'] ,
    				'truename'=>$_POST['truename'] ,
    				'identityimg1'=>$_POST['identityimg1'] ,
    				'identityimg2'=>$_POST['identityimg2'] ,
    				'phone'=>$_POST['phone'] ,
    				'qq'=>$_POST['qq'] ,
    				'email'=>$_POST['email'] ,
    				's_province'=>$_POST['s_province'] ,
    				's_city'=>$_POST['s_city'] ,
    				's_county'=>$_POST['s_county'] ,
    				'address'=>$_POST['address'] ,
    				'homeurl'=>$_POST['homeurl'] ,
    				'iscreate'=>$_POST['iscreate'] ,
    				'oldurl'=>$_POST['oldurl'] ,
    				'description'=>$_POST['description'] ,
    				'decimgs'=>$_POST['decimgs'] ,
	    			'applytype'=>$_POST['applytype'],
    				'checkstatus'=>0,

    		);
            $data[address]=$data[s_province].$data[s_city].$data[s_county].$data[address];

    		$rst = $model-> save($data);

    		if($rst){
    			//登入成功页面跳转

    			$this->success("填写成功！".$data[id_sponsor],U("Exhibition/sponsor/pending"));
    
    		}else{
    			$this->error("注册失败！",U("Exhibition/sponsor/iapply"));
    		}
    		 
    	}
    	else
    	{
    
    		$this->display(":sponsor/iapply");
    	}
    
    }
    
    //主办方机构申请
    public function oapply()
    {
    	$model = M("sponsor");
    	$jiance=$model->find($_SESSION['id_sponsor']);
    	if ($jiance[truename]!=false) {
    		$this->error("有过注册，请登录",U('sponsor/Login'))
    		;
    	}
    	if(isset($_POST['truename']))
    	{
    		$rules = array(
    				//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
    				array('id_sponsor', 'require', '没有注册！', 1 ),
    				array('institution', 'require', '机构名称不能为空！', 1 ),
    				array('truename', 'require', '负责人姓名不能为空！', 1 ),
    				array('identityimg1','require','身份证正面不能为空！',1),
    				array('identityimg2','require','身份证反面不能为空！',1),
                    array('phone','','此手机已经注册',1,unique,3),
    				array('phone','number','手机号必须数字！',1),
    				array('phone','11','手机号长度不符！',3,'length'),
    				array('qq','3,20','qq长度不符！',3,'length'),
    				array('qq','require','qq不能为空！',1),
    				array('qq','','此qq已经注册',1,unique,3),
    				array('email','email','邮箱格式错误！',1),
    				array('email','','此邮箱已经注册',1,unique,3),
    				array('papersimgs','require','三证详细图不能为空！',1),
    				array('homeurl','require','官方链接不能为空！',1),
    				array('iscreate','require','是否举办过展会不能为空！',1),
    		);
    

    		if($model->validate($rules)->create()===false){
    			$this->error($model->getError());
    		}
    		 
    		$phone=$_POST['phone'];
    		$numcheck=is_numeric($phone);
    		
    		if(strlen($phone) <>11||$numcheck==false ){
    			 
    			$this->error("手机长度必须是11位数字！".strlen($phone));
    			 
    		}
    		 
    		
    		$data=array(
    				'id_sponsor'=>$_POST['id_sponsor'] ,
    				'institution'=>$_POST['institution'] ,
    				'truename'=>$_POST['truename'] ,
    				'identityimg1'=>$_POST['identityimg1'] ,
    				'identityimg2'=>$_POST['identityimg2'] ,
    				'phone'=>$_POST['phone'] ,
    				'qq'=>$_POST['qq'] ,
    				'email'=>$_POST['email'] ,
    				'papersimgs'=>$_POST['papersimgs'] ,
    				'homeurl'=>$_POST['homeurl'] ,
    				'iscreate'=>$_POST['iscreate'] ,
    				'oldurl'=>$_POST['oldurl'] ,
    				'decription'=>$_POST['decription'] ,
    				'decimgs'=>$_POST['decimgs'] ,
    				'applytype'=>1,
    
    		);
    		$rst = $model->save($data);
    		if($rst){
    			//登入成功页面跳转

    			$this->success("填写成功！",U("Exhibition/sponsor/pending"));
    
    		}else{
    			$this->error("注册失败！",U("Exhibition/sponsor/oapply"));
    		}
    		 
    	}
    	else
    	{
    
    		$this->display(":sponsor/oapply");
    	}
    
    }
    
    
}


