<?php
/**
 * 会员注册
 */
namespace User\Controller;
use Common\Controller\HomebaseController;
header("Content-Type:text/html; charset=utf-8");
class ApploginController extends HomebaseController {
	

	function app_email_active(){
		$hash=I("get.hash","");
		if(empty($hash)){
			$this->error("激活码不存在");
		}
		
		$users_model=M("Users");
		$find_user=$users_model->where(array("user_activation_key"=>$hash))->find();
		
		if($find_user){
			$result=$users_model->where(array("user_activation_key"=>$hash))->save(array("user_activation_key"=>"","user_status"=>1));
			
			if($result){
				$find_user['user_status']=1;
				$_SESSION['user']=$find_user;
				$this->success("用户激活成功，正在登录中...",__ROOT__."/");
			}else{
				$this->error("用户激活失败!",U("user/login/index"));
			}
		}else{
			$this->error("用户激活失败，激活码无效！",U("user/login/index"));
		}
		
		
	}
	
	
	public function app_login()
	{
		$name = I('post.loginName');
		$find='@';
		$pos= stripos($name, $find);//判断是否存在 返回bool值
		if ($pos)
		{$this->appemail_login();}
		else
		{$this->appmobile_login(); }
	}
	
	public function appmobile_login(){
		$users_model=M('Users');
		$where['mobile']=$_POST['loginName'];
		$userpwd=$_POST['userpwd'];
		$result = $users_model->where($where)->find();
		//echo $users_model->getLastSql();
	
		if(!empty($result)){
			if(sp_compare_password($userpwd, $result['user_pass'])){
				$_SESSION["user"]['id']=$result['id'];
				//写入此次登录信息
				$data = array(
						'last_login_time' => date("Y-m-d H:i:s"),
						'last_login_ip' => get_client_ip(0,true),
				);
				
				$users_model->where(array('id'=>$result["id"]))->save($data);
				$resultuser = $users_model->where($where)->find();

				//dump($_SESSION);
               $this->appjsonReturn(1, "OK", "");
			}else{
					$this->appjsonReturn(0, "密码错误", "");
			}
		}else{
					$this->appjsonReturn(0, "手机用户名不存在", "");
		}
	}
	
	
	
	public function appemail_login(){
		$users_model=M('Users');
		$where['user_email']=$_POST['loginName'];
		$userpwd=$_POST['userpwd'];
		$result = $users_model->where($where)->find();
		//echo $users_model->getLastSql();
	
		if(!empty($result)){
			if(sp_compare_password($userpwd, $result['user_pass'])){
				$_SESSION["user"]=$result;
				//写入此次登录信息
				$data = array(
						'last_login_time' => date("Y-m-d H:i:s"),
						'last_login_ip' => get_client_ip(0,true),
				);
	
				$users_model->where(array('id'=>$result["id"]))->save($data);
				$resultuser = $users_model->where($where)->find();
				$_SESSION['user']=$resultuser;

				 $this->appjsonReturn(1, "OK", "");
			}else{
					$this->appjsonReturn(0, "密码错误", "");
			}
		}else{

			$this->appjsonReturn(0, "邮箱不存在", "");
		}
	}
	

	
	
	
}