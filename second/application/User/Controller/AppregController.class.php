<?php
/**
 * 会员注册
 */
namespace User\Controller;
use Common\Controller\HomebaseController;
header("Content-Type:text/html; charset=utf-8");
class AppregController extends HomebaseController {
	

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
          	
          	$rt = array("status"=>0,"msg" =>$type[1],"data"=>"");
          		
          	$rt = json_encode($rt,JSON_UNESCAPED_UNICODE);
			
			return $rt;
          }else
          {
             $a=$_SESSION['mobileverify'];//这个是张爽要的data

          	$rt = array("status"=>1,"msg" =>"验证码已发送","data"=>$a);
          	$_SESSION['mobile']=$mobile;
          	$rt = json_encode($rt,JSON_UNESCAPED_UNICODE);
			
			return $rt;
          	
          	
          }
			
		}else
		{
			$rt = array("status"=>0,"msg" =>"手机格式错误","data"=>"");
			//$this->ajaxReturn($rt);
			$rt = json_encode($rt,JSON_UNESCAPED_UNICODE);
			
			return $rt;
		}
		 
	}
	
	function appmobilecheckvverify($verify){
		
		$mobileverify=I('session.mobileverify');
// 		dump($mobileverify);
// 		dump($_SESSION['mobileverifytime']);
	//	if($mobileverify==null||time()-$_SESSION['mobileverifytime']>300){unset($_SESSION['mobileverify']); $this->appjsonReturn(0, "验证码过期", "");}	
// 		dump(time()-$_SESSION['mobileverifytime']);
		if($mobileverify==$verify)
		{
			
			$rt = array("status"=>1,"msg" =>"OK","data"=>"");
			$_SESSION['verifystatus']="在收密码的时候增加一个判断，验证码是否审核通过";
			$rt = json_encode($rt,JSON_UNESCAPED_UNICODE);
			
			return $rt;
		}
		else
		{
			//file_put_contents("D:/20160426mobileverify.txt", "mobileverify=".$mobileverify,FILE_APPEND);
			$rt = array("status"=>0,"msg" =>"验证码错误","data"=>"");
			
			$rt = json_encode($rt,JSON_UNESCAPED_UNICODE);
				
			return $rt;
		}
		
	}
	
	public function appmobilereg(){
		header('Content-Type:application/json; charset=utf-8');
		if(!isset($_POST['mobile'])&&!isset($_POST['verify'])&&!isset($_POST['userpwd']))
		{$this->appjsonReturn(0, "非法字段请求", "");}
		if(isset($_POST['mobile'])&&!isset($_POST['verify'])&&!isset($_POST['userpwd']))
		{
			$mobile=I('post.mobile');
			$users_model=M("Users");
			$where['mobile']=$mobile;
			
			
			$result = $users_model->where($where)->count();
			if($result){
				
				$this->appjsonReturn(0, "手机号已被注册！", "");			
					
			}
			else
			{
			//file_put_contents("D:/20160407mobile.txt", "mobile=".$mobile,FILE_APPEND);
			echo  $this->appmobilesendverify(I('post.mobile'));
			}
		}
		if(isset($_POST['verify']))
		{
			$verify=$_POST['verify'];
			if($verify==null){$this->appjsonReturn(0, "验证码为空", "");}
			echo $this->appmobilecheckvverify(I('post.verify'));
		}
		if(isset($_POST['userpwd']))
		{

			if($_SESSION['verifystatus']==NULL){  $this->appjsonReturn(0, "非法操作,请通过验证码", "");}
             
		$mobile = I('session.mobile');
		$userpwd= I('post.userpwd');
		//file_put_contents("d:/20160407.txt", "mobile=".$mobile.$userpwd."\r\n",FILE_APPEND);
		$rules = array(
				array('userpwd','/^[a-zA-Z0-9]{6,12}$/','密码需要6~12位英文或数字！'),
		);
			
		$users_model2=M("Users");
	
		if($users_model2->validate($rules)->create()===false){
			$this->appjsonReturn(0, $users_model2->getError(), "");
		}
			$where['mobile']=$mobile;
				$data=array(
						'user_login' => '',
						'user_email' => '',
						'mobile' =>$mobile,
						'user_nicename' =>'',
						'user_pass' => sp_password($userpwd),
						'last_login_ip' => get_client_ip(0,true),
						'create_time' => date("Y-m-d H:i:s"),
						'last_login_time' => date("Y-m-d H:i:s"),
						'user_status' => 1,
						"user_type"=>2,//会员
				);
				$rst = $users_model2->add($data);
				//dump($_SESSION);
				unset($_SESSION['verifystatus']);//unset($_SESSION[''])得放在if语句外面啊！！！！！！！！！！！！！！！！！！！！！！！！！
				if($rst){
					$_SESSION['user']['id']=$rst;
					$this->appjsonReturn(1, "OK", "");
				}else{
                    $this->appjsonReturn(0, "数据库错误", "");
				}
	

		
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
		$api_send_url="http://222.73.117.158/msg/HttpBatchSendSM";
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
	    //file_put_contents("D:/20160426.txt", "url=".$content,FILE_APPEND);
		$_SESSION['mobileverify']=$key;
		$_SESSION['mobileverifytime']=time();
		file_put_contents("D:/20160407.txt", "key=".$key,FILE_APPEND);
		return $result;

	}
	
	
	
	public function forgetpwd()
	{
	if(isset($_POST['mobile']))
		{
			$mobile=I('post.mobile');
			$model=M("users");
			$where['mobile']=$mobile;
			$result=$model->where($where)->find();
			//file_put_contents("D:/20160411mobileexist.txt", "sql=".$model->getLastSql(),FILE_APPEND);
			if($result)
			{
				echo  $this->appmobilesendverify(I('post.mobile'));
			}
			else {
				$this->appjsonReturn(0, "用户不存在", "");
			}
		}
		
	if(isset($_POST['verify']))
	{
		$verify=$_POST['verify'];
		if($verify==null){
			$this->appjsonReturn(0, "验证码为空", "");
		}
		echo $this->appmobilecheckvverify(I('post.verify'));
	}
	if(isset($_POST['userpwd']))
	{
				$mobile = I('session.mobile');
		        $userpwd= I('post.userpwd');
		        $model=M("users");
		        if($_SESSION['verifystatus']==NULL){$this->appjsonReturn(0, "非法操作,请通过验证码", "");}
		        if(!preg_match('/^[a-zA-Z0-9]{6,12}$/',$userpwd)){
		        	$this->appjsonReturn(0, "密码为6-12位英文或数字", "");	
		        }else
		        {
		        $where['mobile']=$mobile;
		        $data['user_pass']=sp_password($userpwd);
		        $result=$model->where($where)->save($data);
		        unset($_SESSION['verifystatus']);
		        if($result!==false)
		        {
		        	$this->appjsonReturn(1, "OK", "");
		        }
		        else 
		        {
		        	$this->appjsonReturn(0, "重置失败", "");
		        }
		        }
	}
	}
	
	
	public function modifypwd() {
		$userid=sp_get_current_userid();
		//dump($userid);
		if (isset ( $userid ) && isset ( $_POST ['userpwd'] )) {
			$userpwd = I ( 'userpwd' );
			$newpwd = I ( 'newpwd' );
			$model = M ( "users" );
			if (preg_match('/^[a-zA-Z0-9]{6,12}$/',$newpwd)) {
				$where ['id'] = $userid;
				$result1 = $model->where ( $where )->find ();
				if ($result1) {
					if (sp_compare_password ( $userpwd, $result1 ['user_pass'] )) {
						
						$data ['user_pass'] = sp_password ( $newpwd );
						$result = $model->where ( $where )->save ( $data );
						if ($result !== false) {
							$this->appjsonReturn(1, "OK", "");
						} else {
							$this->appjsonReturn(0, "修改密码操作失败，请联系客服", "");
						}
					} else {
						$this->appjsonReturn(0, "原密码输入错误", "");
					}
				
				} else {
					$this->appjsonReturn(0, "找不到用户信息", "");
				}
			} else {
				$this->appjsonReturn(0, "新密码为6-12的数字或英文组合", "");
			}
		}
	
	}
	
	/**
	 * 发送邮箱激活
	 */
	public function app_send_email_active()
	{
	  $model=M('users');
      $userid=sp_get_current_userid();
	  $email = I('post.email');
	  $resultmobile=$model->where("id='$userid'")->find();
	  if($resultmobile){
	  	$rules = array(
	  			array('email','email','需要邮箱格式！',1),
	  	);

	  	if($model->validate($rules)->create()===false){
	  		$this->appjsonReturn(0, $model->getError(), "");
	  	}
           	     $where1['user_email'] = $email;
                 $resultcount = $model->where($where1)->count();
           	     if(!$resultcount||$resultmobile['user_email']==$email){
           	     	$data=array('user_email' => $email,);
           	     	$resultsave=$model->where("id='$userid'")->save($data);
           	     	if($resultsave!==false)
           	     	{
           	     		$resultmobile['user_email']=$email;
           	     		$_SESSION['user']=$resultmobile;
           	     		$resultsend=$this->_app_send_to_active();
           	     		if($resultsend){
           	     		$this->appjsonReturn(1, "OK", "");
           	     		}else{
           	     			$this->appjsonReturn(0, "发送激活邮件失败", "");
           	     		}
           	     	}else{
           	     	   $this->appjsonReturn(0, "添加邮件信息失败", "");
           	     	}
                    
                 }else{
                 	$this->appjsonReturn(0, "邮箱已被注册", "");
                 }
	   }else  {
	  	    $this->appjsonReturn(0, "查询不到用户", "");
	      }
	}      
	  
    public function app_user_info(){
        $userid=sp_get_current_userid();
    	if($userid)
    	{
    		$user_model = M('users');
    		$where['id']=$userid;
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
    			$this->appjsonReturn(0, "没有用户信息", "");
    		}
    	}else{$this->appjsonReturn(0, "没有登陆","");}
    }
    
    public function app_user_info_modify(){
    	$userid=sp_get_current_userid();
    	

    	$user_nicename = I('post.user_nicename');
    	$sex = I('post.sex');
    	$birthday = I('post.birthday');
    	$user_love = I('post.user_love');
    	if($userid)
    	{
    		
    		$user_model = M('users');
    		$where['id']=$userid;
    		$result=$user_model->where($where)->find();
    		if($result){
    			$a=$_FILES['avatar'];
    			 if($a){
    			 	$this->avatarupdate();
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
    			 if(empty($_FILES)&&empty($user_nicename)&&empty($sex)&&empty($birthday)&&empty($user_love)){
    			 		$this->appjsonReturn(0, "没有传入修改数据", "");
    			 		
    			 }
    		}else{
    			$this->appjsonReturn(0, "该手机没有注册", "");
    		}
    	}else{$this->appjsonReturn(0, "没有登陆","");
    	}
    }
    
    
    
    public function saveAvatar(){
        dump($_SESSION);
        exit();
    	$filename = intval($_GET['id']).'.jpg';
    	$xmlstr = $GLOBALS['HTTP_RAW_POST_DATA'];
    
    	if(!empty($xmlstr)) {
    		$xmlstr = file_get_contents('php://input');
    	}
    
    	if(!$xmlstr){
    		exit( '没有接收到数据流.' );
    	}
    
    	$jpg = $xmlstr;//得到post过来的二进制原始数据
    
    	$file = fopen(".tongrenmiao/data/upload/avatar/".$filename,"w");//打开文件准备写入
    	fwrite($file,$jpg);//写入
    	fclose($file);//关闭
    
    }
    
    public function avatarupdate(){
    	$config=array(
    			'rootPath' => './'.C("UPLOADPATH"),
    			'savePath' => './avatar/',
    			'maxSize' => 512000,//500K
    			'saveName'   =>    array(),
    			'exts'       =>    array('jpg', 'png', 'jpeg'),
    			'autoSub'    =>    false,
    	);
    	$driver_type = sp_is_sae()?"Sae":'Local';//TODO 其它存储类型暂不考虑
    	$upload = new \Think\Upload($config,$driver_type);//
    	$info=$upload->upload();
    	//开始上传
    	if ($info) {
    		//上传成功
    		//写入附件数据库信息
    		$first=array_shift($info);//因为它是个二维数组哦 array(1)(array(x))这样的。
    		$avatar=$first['savename'];
    		$avatar_dir=C("UPLOADPATH")."avatar/";
    		$userid=sp_get_current_userid();
    		$model=M('users');
    		$resultdel=$model->where(array("id"=>$userid))->find();
    		$del="D:/heyman/tongrenmiao/".$avatar_dir.$resultdel['avatar'];//换头像的时候删那个以前的头像
    		unlink($del);
    		$result=$model->where(array("id"=>$userid))->save(array("avatar"=>$avatar));
    		$data['avatar']="http://192.168.1.66/tongrenmiao/".$avatar_dir.$avatar;
    		if(result){
    			$this->appjsonReturn(1, "OK", $data);
    		}else{
    			$this->appjsonReturn(0, "插入数据错误", "");
    		}
    
    	} else {
    		//上传失败，返回错误
    		$this->appjsonReturn(0, $upload->getError(), "");
    
    	}
    }
    
    /**
     * 就用json_encode太占地方 用了这个
     * @access protected
     * @param mixed $data 要返回的数据
     * @param $status 要返回的状态（1是过 0是不过） $msg是要返回的信息
     * @return void
     */
    function appjsonReturn($status, $msg ,$data) {
    	 
    	 
    	 
    	// 返回JSON数据格式到客户端 包含状态信息
    	header('Content-Type:application/json; charset=utf-8');
    	$rt=array(
    			"status"=>$status,
    			"msg" =>$msg,
    			"data"=>$data
    	);
    	 
    	exit(json_encode($rt,JSON_UNESCAPED_UNICODE));
    	 
    	 
    }
    
	
}