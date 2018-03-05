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
class AppsponsorController extends HomebaseController {
	
    //首页
    public function app_sponsor_send_verify(){
    	if(isset($_POST['mobile']))
    	{
    		$mobile=I('post.mobile');
    		$users_model=M("sponsor");
    		$where['checkphone']=$mobile;
    			
    			
    		$result = $users_model->where($where)->count();
    		//dump($users_model->getLastSql());
    		if($result){$this->appjsonReturn(0, "手机号已被注册！", "");}
    		else
    		{
    			echo  $this->appmobilesendverify(I('post.mobile'));
    		}
    	}else{$this->appjsonReturn(0, "没有手机号", "");}

    }
	
	
	public function app_sponsor_reg(){
		if(isset($_POST['verify']))
		{
			$verify=$_POST['verify'];
			echo $this->appmobilecheckvverify(I('post.verify'));
		}else{$this->appjsonReturn(0, "请输入验证码", "");}
		
	    if(isset($_POST['username']))
    	{
    		$rules = array(
    				//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
    				array('username', '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]{1,20}+$/u', '用户名为20位内的汉字或英文或数字组合！' ),
    				array('username', 'require', '用户名不能为空！', 1 ),
    				//array('username','email','需要邮箱格式！',1),
    				array('username','','该用户名注册过了',1,unique,3),	
    				//array('nickname', 'require', '昵称不能为空！', 1 ),
    				array('userpwd','/^[a-zA-Z0-9]{6,12}$/','密码需要6~12位英文或数字！'),
    				
    				//array('repassword','userpwd','确认密码不正确。',0,'confirm'),
    				//array('checkwenzhang','require','主办方协议须知！',1),

    		);
    
    		$model = M("sponsor");
    		
    		if($model->validate($rules)->create()===false){
    			$this->appjsonReturn(0, $model->getError(), "");
    		}
			
	    }
	    
	    $data=array(
	    		'username'=>$_POST['username'] ,
	    		'checkphone'=>$_SESSION['mobile'],
	    		//'nickname'=>$_POST['nickname'] ,
	    		//'checkphone'=>$_POST['checkphone'] ,
	    		'userpwd'=>sp_password($_POST['userpwd']) ,
	    		'last_login_ip' => get_client_ip(0,true),
	    		'last_login_time' => date("Y-m-d H:i:s"),
	    		'create_time' => date("Y-m-d H:i:s"),
	    );
	    
	    $rst = $model->add($data);
	    if($rst){
	    	$data['id_sponsor']=$rst;
	    	$_SESSION['sponsor']=$data;
	    	$this->appjsonReturn(1, "OK", "");

	    }else{
	    	$this->appjsonReturn(0, "注册失败，请联系客服", "");
	    }
	     
	    
	}
	
	
	
	public function app_sponsor_reg_type(){
		$type = I("post.applytype");
		if($type==0)
		{$this->app_per_reg();}
		else if($type==1)
		{$this->app_org_reg();}
		else
		{$this->appjsonReturn(0, "申请类型错误", "");}
			
	}
	
	private function app_per_reg(){
         //dump($_SESSION);
		 $sponsorid=$_SESSION['sponsor']['id_sponsor'];
		 $model=M('sponsor');
		 $result=$model->find($sponsorid);
		 if(!$result){$this->appjsonReturn(0, "查询用户数据错误", "");}
 		 $decimgsresult="";
 		 $identityimg1=$_FILES['identityimg1'];
 		 $identityimg2=$_FILES['identityimg2'];
 		 $decimgs0=$_FILES['decimgs0'];
 		 $decimgs1=$_FILES['decimgs1'];
 		 $decimgs2=$_FILES['decimgs2'];
 		 $decimgs3=$_FILES['decimgs3'];
 		 $decimgs4=$_FILES['decimgs4'];
 		 if(!$identityimg1['name']){$this->appjsonReturn(0, "没有身份证正面照", "");}else{$identityimg1=$this->sponsorupdate(identityimg1);}
  		 if(!$identityimg2['name']){$this->appjsonReturn(0, "没有身份证反面照", "");}else{$identityimg2=$this->sponsorupdate(identityimg2);}
 		 if($decimgs0['name']){$decimgsresult=$decimgsresult.'|'.$this->sponsorupdate(decimgs0);}
 		 if($decimgs1['name']){$decimgsresult=$decimgsresult.'|'.$this->sponsorupdate(decimgs1);} 		 
	 	 if($decimgs2['name']){$decimgsresult=$decimgsresult.'|'.$this->sponsorupdate(decimgs2);}
	 	 if($decimgs3['name']){$decimgsresult=$decimgsresult.'|'.$this->sponsorupdate(decimgs3);}
	 	 if($decimgs4['name']){$decimgsresult=$decimgsresult.'|'.$this->sponsorupdate(decimgs4);}	 
		 if(isset($_POST['truename']))
    	{
    		$rules = array(
    				//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
    				array('truename', 'require', '用户名不能为空！', 1 ),
    				array('qq','0,30','qq太长了！',3,'length'),
    				array('email','email','邮箱格式错误！',1),
    				array('email','','该邮箱注册过了',1,unique,3),
//      		    array('identityimg1','require','身份证正面不能为空！',1),
//      			array('identityimg2','require','身份证反面不能为空！',1),
     				array('address','require','展会地址不能为空！',1),
//      				array('homeurl','url','官方链接格式不对！',2),
//     				array('oldurl','url','官方链接格式不对！',2),
    				//array('nickname', 'require', '昵称不能为空！', 1 ),
    				//array('userpwd','^[a-zA-Z0-9]{6,12}$','密码需要6~12位英文或数字！',3),
    				//array('repassword','userpwd','确认密码不正确。',0,'confirm'),
    				//array('checkwenzhang','require','主办方协议须知！',1),
    		
    		);
    

    		
    		if($model->validate($rules)->create()===false){
    			$this->appjsonReturn(0, $model->getError(), "");
    		}
			
	    }else{$this->appjsonReturn(0, "用户名不能为空", "");}

    		$data=array(
    				'id_sponsor'=>$_SESSION['sponsor']['id_sponsor'] ,
    				'truename'=>$_POST['truename'] ,
    				'qq'=>$_POST['qq'] ,
    				'email'=>$_POST['email'] ,
    				'identityimg1'=>$identityimg1,
    		        'identityimg2'=>$identityimg2,
    				'address'=>$_POST['address'] ,
    				'homeurl'=>$_POST['homeurl'] ,
    				'oldurl'=>$_POST['oldurl'] ,
    				'description'=>$_POST['description'] ,
    				'decimgs'=>$decimgsresult ,
    				'applytype'=>$_POST['applytype'],
    				'checkstatus'=>'0'
    		);
	    $rst = $model->save($data);
	    if($rst!==false){
	    	$this->appjsonReturn(1, "OK", "");
	    
	    }else{
	    	$this->appjsonReturn(0, "注册失败，请联系客服", "");
	    }
	}
	
	private function app_org_reg(){
		$sponsorid=$_SESSION['sponsor']['id_sponsor'];
		$model=M('sponsor');
		$result=$model->find($sponsorid);
		if(!$result){$this->appjsonReturn(0, "查询用户数据错误", "");}

		
		
			 if(isset($_POST['truename']))
    	{
    		$rules = array(
    				//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
    				array('institution', 'require', '用户名不能为空！', 1 ),
    				array('truename', 'require', '负责人不能为空！', 1 ),
    				array('qq','0,30','qq太长了！',3,'length'),
    				array('email','email','邮箱格式错误！',1),
    				array('email','','该邮箱注册过了',1,unique,3),
//     				array('papersimgs','require','三证不能为空！',1),
    				array('address','require','展会地址不能为空！',1),
//     				array('homeurl','url','官方链接格式不对！',2),
    				//array('nickname', 'require', '昵称不能为空！', 1 ),
    				//array('userpwd','^[a-zA-Z0-9]{6,12}$','密码需要6~12位英文或数字！',3),
    				//array('repassword','userpwd','确认密码不正确。',0,'confirm'),
    				//array('checkwenzhang','require','主办方协议须知！',1),
    		
    		);
    

    		
    		if($model->validate($rules)->create()===false){
    			$this->appjsonReturn(0, $model->getError(), "");
    		}
	    }
	    $decimgsresult="";
	    $papersimgsresult="";
	    $papersimgs0=$_FILES['papersimgs0'];
	    $papersimgs1=$_FILES['papersimgs1'];
	    $papersimgs2=$_FILES['papersimgs2'];
	    $decimgs0=$_FILES['decimgs0'];
	    $decimgs1=$_FILES['decimgs1'];
	    $decimgs2=$_FILES['decimgs2'];
	    $decimgs3=$_FILES['decimgs3'];
	    $decimgs4=$_FILES['decimgs4'];
	    if(!$papersimgs0['name']){
	    	$this->appjsonReturn(0, "三证不全", "");
	    }else{$papersimgsresult=$papersimgsresult.'|'.$this->sponsorupdate(papersimgs0);
	    }
	    if(!$papersimgs1['name']){
	    	$this->appjsonReturn(0, "三证不全", "");
	    }else{$papersimgsresult=$papersimgsresult.'|'.$this->sponsorupdate(papersimgs1);
	    }
	    if(!$papersimgs2['name']){
	    	$this->appjsonReturn(0, "三证不全", "");
	    }else{$papersimgsresult=$papersimgsresult.'|'.$this->sponsorupdate(papersimgs2);
	    }
	    if($decimgs0['name']){
	    	$decimgsresult=$decimgsresult.'|'.$this->sponsorupdate(decimgs0);
	    }
	    if($decimgs1['name']){
	    	$decimgsresult=$decimgsresult.'|'.$this->sponsorupdate(decimgs1);
	    }
	    if($decimgs2['name']){
	    	$decimgsresult=$decimgsresult.'|'.$this->sponsorupdate(decimgs2);
	    }
	    if($decimgs3['name']){
	    	$decimgsresult=$decimgsresult.'|'.$this->sponsorupdate(decimgs3);
	    }
	    if($decimgs4['name']){
	    	$decimgsresult=$decimgsresult.'|'.$this->sponsorupdate(decimgs4);
	    }
	    
	    
	    
    		$data=array(
    				'id_sponsor'=>$_SESSION['sponsor']['id_sponsor'] ,
    				'institution'=>$_POST['institution'] ,
    				'truename'=>$_POST['truename'] ,
    				'qq'=>$_POST['qq'] ,
    				'email'=>$_POST['email'] ,
    				'papersimgs'=>$papersimgsresult ,
    				'address'=>$_POST['address'] ,
    				'homeurl'=>$_POST['homeurl'] ,
    				'oldurl'=>$_POST['oldurl'] ,
    				'description'=>$_POST['description'] ,
    				'decimgs'=>$decimgsresult ,
    				'applytype'=>$_POST['applytype'],
    				'checkstatus'=>'0'
    				
    	
    		);
	    
	    $rst = $model->save($data);
// 	    dump($model->getLastSql());
// 	    dump($rst);
	    if($rst!==false){
	    	$this->appjsonReturn(1, "OK", "");
	    
	    }else{
	    	$this->appjsonReturn(0, "注册失败，请联系客服", "");
	    }
	}	
	
	
	
	
	
	function app_email_active(){
		$hash=I("get.hash","");
		if(empty($hash)){
			$this->error("激活码不存在");
		}
		
		$users_model=M("Users");
		$find_user=$users_model->where(array("user_activation_key"=>$hash))->find();
		
		if($find_user){
			$result=$users_model->where(array("user_activation_key"=>$hash))->save(array("user_activation_key"=>"","email_status"=>1));
			
			if($result){
				$find_user['email_status']=1;
				$_SESSION['user']=$find_user;
				$this->success("用户激活成功，正在登录中...",__ROOT__."/");
			}else{
				$this->error("用户激活失败!",U("user/login/index"));
			}
		}else{
			$this->error("用户激活失败，激活码无效！",U("user/login/index"));
		}
		
		
	}
	
	function index(){
		
		$this->display(":appreg");
	}
	
	
	function appmobilesendverify($mobile){
		if($mobile)//preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#',$mobile)
		{
		  $result=$this->sendSMS($mobile);//返回格式是（时间，状态码）需要状态码
		  $type = explode(',',$result);
          if($type[1]!='0')
          {

          	$this->appjsonReturn(0, $type[1], "");

          }else
          {

          	$_SESSION['mobile']=$mobile;
          	$this->appjsonReturn(1, "验证码已发送", "");
          }
			
		}else
		{

      		$this->appjsonReturn(0, "手机格式错误", "");
		}
		 
	}
	
	function appmobilecheckvverify($verify){
		$mobileverify=I('session.mobileverify');
		if($mobileverify==$verify)
		{
			
			$rt = array("status"=>1,"msg" =>"OK","data"=>""
			);
// 			$_SESSION['verifystatus']="密码那里需要一个状态来判断验证码是否通过";
// 			$rt = json_encode($rt,JSON_UNESCAPED_UNICODE);
			
			return;
		}
		else
		{
			$rt = array("status"=>0,"msg" =>"验证码错误","data"=>"");
			
			$rt = json_encode($rt,JSON_UNESCAPED_UNICODE);
				
			return $rt;
			$this->appjsonReturn(0, "验证码错误", "");
		}
		
	}

	
	/**
	 * 通过CURL发送HTTP请求
	 * @param string $url  //请求URL
	 * @param array $postFields //请求参数
	 * @return mixed
	 */
	private function curlPost($url,$postFields){
		$postFields = http_build_query($postFields);
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );
		$result = curl_exec ( $ch );
		curl_close ( $ch );
		return $result;
	}
	
	/**
	 * 发送短信
	 *
	 * @param string $mobile 手机号码
	 * @param string $msg 短信内容
	 * @param string $needstatus 是否需要状态报告
	 * @param string $product 产品id，可选
	 * @param string $extno   扩展码，可选
	 */
	public function sendSMS( $mobile, $msg, $needstatus = 'false', $product = '', $extno = '') {
		//创蓝接口参数
		$key=sp_random_num(4);
		$content = "验证码：".$key."，感谢您使用同人喵。如非本人操作，请忽略。";
		$msg = mb_convert_encoding($content,"utf-8","auto");
		$api_account="jiaochong";
		$api_password="Kidstone654";
		$api_send_url="http://222.73.117.156/msg/HttpBatchSendSM";
		$postArr = array (
				'account' => $api_account,
				'pswd' => $api_password,
				'msg' => $msg,
				'mobile' => $mobile,
				'needstatus' => $needstatus,
				'product' => $product,
				'extno' => $extno
		);
	
		$result = $this->curlPost( $api_send_url , $postArr);
		$_SESSION['mobileverify']=$key;
		return $result;

	}
	
	
	

	
	/**
	 * 发送邮箱激活
	 */
	public function app_send_email_active()
	{
	  $model=M('users');
	  $mobile = I('post.mobile');
	  $email = I('post.email');
	  $resultmobile=$model->where("mobile='$mobile'")->find();
	  if($resultmobile){
	     
           if(!empty($email)){
           	     $where1['user_email'] = $email;
                 $resultcount = $model->where($where1)->count();
           	     if(!$resultcount||$resultmobile['user_email']==$email){
           	     	$where2['mobile']=$mobile;
           	     	$data=array('user_email' => $email,);
           	     	$resultsave=$model->where($where2)->save($data);
           	     	if($resultsave!==false)
           	     	{
           	     		$resultmobile['user_email']=$email;
           	     		$_SESSION['user']=$resultmobile;
           	     		$resultsend=$this->_app_send_to_active();
           	     		if($resultsend){
           	     		$rt = array("status"=>1,"msg" => "OK","data"=>"");
           	     		$rt = json_encode($rt,JSON_UNESCAPED_UNICODE);
           	     		echo $rt;
           	     		}else{
           	     			$rt = array("status"=>0,"msg" => "发送邮件失败，请联系客服xxx","data"=>"");
           	     			$rt = json_encode($rt,JSON_UNESCAPED_UNICODE);
           	     			echo $rt;
           	     		}
           	     	}else{
           	     		$rt = array ("status" => 0, "msg" => "添加邮箱信息失败，请联系客服xxx", "data" => "" );
           	     		$rt = json_encode ( $rt, JSON_UNESCAPED_UNICODE );
           	     		echo $rt;
           	     	}
                    
                 }else{
                 	$rt = array("status"=>0,"msg" => "邮箱已被注册！","data"=>"");
                 	$rt = json_encode($rt,JSON_UNESCAPED_UNICODE);
                 	echo $rt;
                 }
                    	
           	
           }else{
           	   $rt0 = array ("status" => 0, "msg" => "邮箱不能为空", "data" => "" );
               $rt0 = json_encode ( $rt0, JSON_UNESCAPED_UNICODE );
           	   echo $rt0;
           }
	   }else  {
	  	    $rtmobile = array ("status" => 0, "msg" => "没有进行手机注册", "data" => "" );
	  	    $rtmobile = json_encode ( $rtmobile, JSON_UNESCAPED_UNICODE );
	  	    echo $rtmobile;
	      }
	}      
	  
    public function app_user_info(){
    	$mobile = I('post.mobile');
    	if($mobile)
    	{
    		$user_model = M('users');
    		$where['mobile']=$mobile;
    		$result=$user_model->where($where)->find();
    		if($result){
    			$data=array(
    					'avatar'=>$result['avatar'],
    					'user_nicename'=>$result['user_nicename'],
    					'sex'=>$result['sex'],
    					'birthday'=>$result['birthday'],
    					'user_love'=>$result['user_love'],
    					'mobile'=>$result['mobile'],
    					'user_email'=>$result['user_email'],
    					'email_status'=>$result['email_status'],
    					);
    			$this->appjsonReturn(1, "OK", $data);
    		}else{
    			$this->appjsonReturn(0, "该手机没有注册", "");
    		}
    	}else{$this->appjsonReturn(0, "没有传入手机号","");}
    }
    
    public function app_user_info_modify(){
    	$mobile = I('post.mobile');
    	$avatar = I('post.avatar');
    	file_put_contents("d:/postavatar.txt", $avatar);
    	$user_nicename = I('post.user_nicename');
    	$sex = I('post.sex');
    	$birthday = I('post.birthday');
    	$user_love = I('post.user_love');
    	if($mobile)
    	{
    		//file_put_contents("d:/20160415mobile.txt", $mobile);
    		$user_model = M('users');
    		$where['mobile']=$mobile;
    		$result=$user_model->where($where)->find();
    		if($result){
    			 if($avatar){
    			 	file_put_contents("d:/avatar.txt", $avatar);
    			 	$data['avatar']=$avatar;
    			 	//var_dump($data);
    			 	$resultmodify=$user_model->where($where)->save($data);
    			 	if($resultmodify !== false){
    			 		$this->appjsonReturn(1, "OK", "");
    			 	}else{
    			 		$this->appjsonReturn(0, "修改个人信息错误", "");
    			 	}
    			 }
    			 if($user_nicename){
    			 	$data['user_nicename']=$user_nicename;
    			 	//var_dump($data);
    			 	$resultmodify=$user_model->where($where)->save($data);
    			 	if($resultmodify !== false){
    			 		$this->appjsonReturn(1, "OK", "");
    			 	}else{
    			 		$this->appjsonReturn(0, "修改个人信息错误", "");
    			 	}
    			 }
    			 if($sex){
    			 	$data['sex']=$sex;
    			 	//var_dump($data);
    			 	$resultmodify=$user_model->where($where)->save($data);
    			 	if($resultmodify !== false){
    			 		$this->appjsonReturn(1, "OK", "");
    			 	}else{
    			 		$this->appjsonReturn(0, "修改个人信息错误", "");
    			 	}
    			 }
    			 if($birthday){
    			 	$data['birthday']=$birthday;
    			 	//var_dump($data);
    			 	$resultmodify=$user_model->where($where)->save($data);
    			 	if($resultmodify !== false){
    			 		$this->appjsonReturn(1, "OK", "");
    			 	}else{
    			 		$this->appjsonReturn(0, "修改个人信息错误", "");
    			 	}
    			 }
    			 if($user_love){
    			 	$data['user_love']=$user_love;
    			 	//var_dump($data);
    			 	$resultmodify=$user_model->where($where)->save($data);
    			 	if($resultmodify !== false){
    			 		$this->appjsonReturn(1, "OK", "");
    			 	}else{
    			 		$this->appjsonReturn(0, "修改个人信息错误", "");
    			 	}
    			 	
    			 }
    			 if(empty($avatar)&&empty($user_nicename)&&empty($sex)&&empty($birthday)&&empty($user_love)){
    			 		$this->appjsonReturn(0, "没有传入修改数据", "");
    			 		
    			 }
    		}else{
    			$this->appjsonReturn(0, "该手机没有注册", "");
    		}
    	}else{$this->appjsonReturn(0, "没有传入手机号","");
    	}
    }
    
    private function sponsorupdate($a){
    	$config=array(
    			'rootPath' => './'.C("UPLOADPATH"),
    			'savePath' => './'.$a.'/',
    			'maxSize' => 5120000,//5000K
    			'saveName'   =>    array(),
    			'exts'       =>    array('jpg', 'png', 'jpeg'),
    			'autoSub'    =>    false,
    	);
    	$driver_type = sp_is_sae()?"Sae":'Local';//TODO 其它存储类型暂不考虑
    	$upload = new \Think\Upload($config,$driver_type);//
    	$info=$upload->uploadOne($_FILES[''.$a.'']);
    	//开始上传
    	if ($info) {
 
    		//上传成功
    		//写入附件数据库信息
    		$first=$info;
    		file_put_contents("D:/201607051.txt",var_export($first,TRUE).PHP_EOL,FILE_APPEND);
    		$identityimg1=$first['savename'];
    		$identityimg1_dir=C("UPLOADPATH")."'.$a.'/";
//     		$userid=sp_get_current_sponsorid();
//     		$model=M('sponsor');
//     		$resultdel=$model->where(array("id"=>$userid))->find();
//     		$del="D:/heyman/tongrenmiao/".$identityimg1_dir.$resultdel['identityimg1'];//换头像的时候删那个以前的头像
//     		unlink($del);
//     		$result=$model->where(array("id"=>$userid))->save(array("identityimg1"=>$identityimg1));
//     		$data['identityimg1']="http://192.168.1.66/tongrenmiao/".$identityimg1_dir.$identityimg1;
 
    		return $identityimg1;
    
    	} else {
    		//上传失败，返回错误
    		$this->appjsonReturn(0, $upload->getError(), "");
    
    	}
    }
    
    public function identityimg2update(){
    	$config=array(
    			'rootPath' => './'.C("UPLOADPATH"),
    			'savePath' => './identityimg2/',
    			'maxSize' => 5120000,//5000K
    			'saveName'   =>    array(),
    			'exts'       =>    array('jpg', 'png', 'jpeg'),
    			'autoSub'    =>    false,
    	);
    	$driver_type = sp_is_sae()?"Sae":'Local';//TODO 其它存储类型暂不考虑
    	$upload = new \Think\Upload($config,$driver_type);//
    	$info=$upload->uploadOne($_FILES['identityimg2']);
    	//开始上传
    	if ($info) {
    		//上传成功
    		//写入附件数据库信息
    		$first=$info;
    		$identityimg2=$first['savename'];
    		$identityimg2_dir=C("UPLOADPATH")."identityimg2/";
//     		$userid=sp_get_current_userid();
//     		$model=M('users');
//     		$resultdel=$model->where(array("id"=>$userid))->find();
//     		$del="D:/heyman/tongrenmiao/".$identityimg2_dir.$resultdel['identityimg2'];//换头像的时候删那个以前的头像
//     		unlink($del);
//     		$result=$model->where(array("id"=>$userid))->save(array("identityimg2"=>$identityimg2));
//     		$data['identityimg2']="http://192.168.1.66/tongrenmiao/".$identityimg2_dir.$identityimg2;
//     		if(result){
//     			$this->appjsonReturn(1, "OK", $data);
//     		}else{
//     			$this->appjsonReturn(0, "插入数据错误", "");
//     		}
    		return $identityimg2;
    	} else {
    		//上传失败，返回错误
    		$this->appjsonReturn(0, $upload->getError(), "");
    
    	}
    }
    
    public function decimgsupdate(){
    	$config=array(
    			'rootPath' => './'.C("UPLOADPATH"),
    			'savePath' => './decimgs/',
    			'maxSize' => 5120000,//5000K
    			'saveName'   =>    array(),
    			'exts'       =>    array('jpg', 'png', 'jpeg'),
    			'autoSub'    =>    false,
    	);
    	file_put_contents("D:/20160705.txt",var_export($_FILES['decimgs'],TRUE).PHP_EOL,FILE_APPEND);
    	$driver_type = sp_is_sae()?"Sae":'Local';//TODO 其它存储类型暂不考虑
    	$upload = new \Think\Upload($config,$driver_type);//
    	$info=$upload->upload($_FILES['decimgs']);
    	//开始上传
    	if ($info) {
    		//上传成功
    		//写入附件数据库信息
    		$first=$info;
    		$decimgs=$first['savename'];
    		$decimgs_dir=C("UPLOADPATH")."decimgs/";
    		//     		$userid=sp_get_current_userid();
    		//     		$model=M('users');
    		//     		$resultdel=$model->where(array("id"=>$userid))->find();
    		//     		$del="D:/heyman/tongrenmiao/".$decimgs_dir.$resultdel['decimgs'];//换头像的时候删那个以前的头像
    		//     		unlink($del);
    		//     		$result=$model->where(array("id"=>$userid))->save(array("decimgs"=>$decimgs));
    		//     		$data['decimgs']="http://192.168.1.66/tongrenmiao/".$decimgs_dir.$decimgs;
    		//     		if(result){
    		//     			$this->appjsonReturn(1, "OK", $data);
    		//     		}else{
    		//     			$this->appjsonReturn(0, "插入数据错误", "");
    		//     		}
    		return $decimgs;
    	} else {
    		//上传失败，返回错误
    		$this->appjsonReturn(0, $upload->getError(), "");
    
    	}
    }
       
    
}


