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
header('Content-Type:text/html;charset=utf-8');
/**
 * 首页
 */
class ChenweiController extends HomebaseController {

    //首页
	public function test() {
               /**$buy=691;
	           $_SESSION['buy']=$buy;
	           $this->success("",U('chenwei/test2'));**/
		$tuo=$_POST['tuo'];
		$tuo2=$_POST['tuo2'];
		$tuo3=$_POST['tuo3'];
		$tuo4=$_POST['tuo4'];
		$tuo5=$_POST['tuo5'];
		echo count($tuo)."</br>";
		for($i=0;$i<count($tuo);$i++)
		{
			if(!empty($tuo[$i])&&!empty($tuo2[$i])&&!empty($tuo3[$i])&&!empty($tuo4[$i])&&!empty($tuo5[$i]))
				
			{echo $tuo[$i]."\r".$tuo2[$i]."\r".$tuo3[$i]."\r".$tuo4[$i]."\r".$tuo5[$i]."\r\n";}
			else
			{
				echo "空</br>";
			}
			
		}
	}
               
    public function test2() {
    	header("Content-type: text/html; charset=utf-8");

    	$modl=M(sponsor);
        $data=array(
        		truename=>nihao,
        		);
    	$rrr=$modl->add($data);
    	echo $rrr;
        echo $modl->getLastSql();
    	file_put_contents("d:/20160324.txt",'model过le'.$modl->getlastsql().'------------------',FILE_APPEND);

    	
    	//echo $modl->getlastsql();
    	//echo $rrr[phone];
    	//echo $data[truename];
    	//echo '再见！';

    	   }
 
    	   
    public function test3()
    {
    	$test252=M('sponsor');
    	$bb=$test252->find(666);
    	echo $test252->getLastSql();
    	dump($bb);
    	echo "1";
    	
    	echo "2";
    	//$test->db(1,'mysql://root:root@localhost:3060/tsgmdb',$force=false);
    	//$test = M('testticket','cmf_','mysql://root:root@localhost:3060/tsgmdb');
    	 $connection = 'mysql://root:root@localhost:3060/tsgmdb';
    	 $test=M('testticket','cmf_',$connection);
    	 dump ($test);
    	$b=$test->where("ticket_name=33")->find();
    	dump($b);
    	
    	echo $test->getLastSql();
    	//dump($b);
    	
    	//$this->display(test3);
    }
	 

    public function test4()
    {
      
    	$b=$_REQUEST['fileField'];
    	if($b!=null)
    	{echo $b;}
    	else
    		echo "没有传过来";
    	dump(error);

    }
    
    public function test6()
    {
    
    	$b=$_POST['bb'];
    	$c=$_POST['cc'];
    	$arr['b']=$b;
    	$arr['c']=$c;
    	$bbbb=json_encode($arr,0);
    	echo $bbbb;
    	//$this->ajaxReturn ($arr,'JSON');
    
    }

    public function test7()
    {
    
        file_put_contents("D:/2222.txt","1",FILE_APPEND);
    	$data=array("status"=>1,"user"=>array("fff"=>111,"nnn"=>222));
    	//$rt=json_encode($rt);
    	
        $users_model=M("Users");
        file_put_contents("D:/2222.txt","2",FILE_APPEND);
    	$rules = array(
    			//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
    			array('username', 'require', '手机号/邮箱/用户名不能为空！', 1 ),
    			array('password','require','密码不能为空！',1),
    	
    	);
    	file_put_contents("D:/2222.txt","3",FILE_APPEND);
    	if($users_model->validate($rules)->create()===false){
    		$this->ajaxReturn($users_model->getError());
    	}
    	file_put_contents("D:/2222.txt","4",FILE_APPEND);
    
    }
    
    
    public function test8()
    {
    
    	//$a=0x123123;
    	//var_dump(is_int($a));
    	$a=sp_random_num(4);
    	echo $a;
    	
    }
    
    public function test9(){
    	$b="seee222e";
    	$data2=array(
    			'mobile'=>$b);
    	$data['mobile']=$b;
    	dump($data);
    	dump($data2);
    	$model=M('test');
    	
    	$rt=$model->add($data);
    	dump($rt);
    	file_put_contents("d:/20160409.txt", $data['mobile']."\r\n",FILE_APPEND);
    	
    }
    ###b3322f94f318f5e744e93f2ad2b4f500

    public function test10(){
    	$b="seee222e";
    	$data2=array(
    			'mobile'=>$b);
    	$data['mobile']=$b;
    	dump($data);
    	dump($data2);
    	$model=M('test');
    	 
    	$rt=$model->add($data);
    	dump($rt);
    	file_put_contents("d:/20160409.txt", $data['mobile']."\r\n",FILE_APPEND);
    	 
    }
    
    
    public function test11()
    {
      $model=M('users');
      $result=$model->find('47');
      $_SESSION['user']=$result;
      //$this->_send_to_active();
      $title='123';
      $content='你是猪吗3 陈韡';
      $send_result=$this->_app_send_to_active();;
      dump($send_result);
      echo '1';

    }
    
    public function test12()
    {
    	$model=M('test');
    	$data1=array(
    			'mobile'=>1243,);
    	$data2=array(
    			'mobile'=>12434,);
    	$data3=array(
    			'mobile'=>124344,);
        $data=array(
        		$data1,$data2,$data3,

        		
        		
        		);
       $result=$model->addall($data);
       $result1=$model->getField('$result',true);
       dump($result);
       dump($result1);
    
    }
    
    public function test13()
    {     
    	$j='{"title":"ThinkCMF邮件激活通知.","template":"<p>本邮件来自<a href=\"http:\/\/192.168.1.66\/tongrenmiao\">同人喵<\/a><br\/><br\/>&nbsp; &nbsp;<strong>---------------<\/strong><br\/>&nbsp; &nbsp;<strong>帐号激活说明<\/strong><br\/>&nbsp; &nbsp;<strong>---------------<\/strong><br\/><br\/>&nbsp; &nbsp; 尊敬的<span style=\"FONT-SIZE: 16px; FONT-FAMILY: Arial; COLOR: rgb(51,51,51); LINE-HEIGHT: 18px; BACKGROUND-COLOR: rgb(255,255,255)\">#username#，您好。<\/span>如果您是同人喵的新用户，或在修改您的注册Email时使用了本地址，我们需要对您的地址有效性进行验证以避免垃圾邮件或地址被滥用。<br\/>&nbsp; &nbsp; 您只需点击下面的链接即可激活您的帐号：<br\/>&nbsp; &nbsp; <a title=\"\" href=\"http:\/\/#link#\" target=\"_self\">http:\/\/#link#<\/a><br\/>&nbsp; &nbsp; (如果上面不是链接形式，请将该地址手工粘贴到浏览器地址栏再访问)<br\/>&nbsp; &nbsp; 感谢您的访问，祝您使用愉快！<br\/><br\/>&nbsp; &nbsp; 此致<br\/>&nbsp; &nbsp; 同人喵 管理团队.<\/p>"}'; 
    	var_dump(json_decode($j	,true));
    	$a=json_decode($j,true);
    	$b=json_encode($a,JSON_UNESCAPED_UNICODE);
    	var_dump($b);

    }
    
    public function test14(){
    	$data=array('a'=>'123','b'=>'234');
    	$b='444';
    	$data['c']=$b;
    	$_SESSION['num']=$data;
    	
    	$d=I('session.num');
    	file_put_contents("d:/55555.txt", var_dump($d));
    }
    
    public function test15(){
    	$data=array(
    			'a'=>'123333',
    			'b'=>'1231231',
    			'c'=>array(
    					'cc'=>'45345',
    					
    					'cd'=>'ffff'
    					
    					)
    			
    			);
    	$msg='你好';
    	$rt = array(
    			"status"=>"0",
    			"msg" =>$msg,
    			"data"=>$data
    	);
    	 $_FILES=123;
    	$rt = json_encode($rt,JSON_UNESCAPED_UNICODE);
    	echo $rt;
    	$rc = json_decode($rt,true);
    	dump($rc);
    	$this->appjsonReturn(1, '卧槽的',$data);
    	$rt = array(
    			"status"=>0,
    			"msg" =>$msg,
    			"data"=>$data
    	);
    	
    	$rt = json_encode($rt,JSON_UNESCAPED_UNICODE);
    	echo $rt;
    }
    //是上传文件的，，，，！
    public function test16(){
    	$tempFile = $_FILES['avatar']['tmp_name'];
    	$a=$_FILES;
    	dump($a);
    	file_put_contents("d:/20160419.txt", json_encode($a));
    	
    	$targetPath = "D:/testfile/";//要保存到的新目录
    	$targetFile = $_FILES['avatar']['name'];//要生成的文件名
    	dump($targetFile);
    	dump($targetPath);
    	//$aa=move_uploaded_file($tempFile,$targetPath.$targetFile);//PHP自带函数
    	$aa=move_uploaded_file($tempFile,iconv('UTF-8','gb2312',$targetPath.$targetFile));
    	dump($aa);
    	dump($tempFile);
    	$data = file_get_contents('php://input');
    	dump($data);	
    	$a=I('post.form_data');
    	dump($a);
    	$c=I('post.mobile');
    	dump($c);
    }
    
    public function test17(){

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
    	    $model=M('users');
    		$where=array('id'=>"65");
    		$result=$model->where($where)->save(array("avatar"=>$avatar));
    		$data['avatar']="http://192.168.1.66/tongrenmiao/".$avatar_dir.$avatar;
    		dump($data);
    		if(result){
    			$this->appjsonReturn(1, "OK", "");
    		}else{
    			$this->appjsonReturn(0, "插入数据错误", "");
    		}

    	} else {
    		//上传失败，返回错误
    		$this->appjsonReturn(0, $upload->getError(), "");

    	}
    }
    
    public function test18(){
    	if(!empty($_SESSION['avatar'])){
    		$targ_w = "10";
    		$targ_h = "10";
    		$x = "10";
    		$y = "10";
    		$jpeg_quality = 90;
    
    		$avatar=$_SESSION['avatar'];
    		$avatar_dir=C("UPLOADPATH")."avatar/";
    		if(sp_is_sae()){//TODO 其它存储类型暂不考虑
    			$src=C("TMPL_PARSE_STRING.__UPLOAD__")."avatar/$avatar";
    		}else{
    			$src = $avatar_dir.$avatar;
    		}
    
    		$avatar_path=$avatar_dir.$avatar;
            dump($avatar_path);
    
    		if(sp_is_sae()){//TODO 其它存储类型暂不考虑
    			$img_data = sp_file_read($avatar_path);
    			$img = new \SaeImage();
    			$size=$img->getImageAttr();
    			$lx=$x/$size[0];
    			$rx=$x/$size[0]+$targ_w/$size[0];
    			$ty=$y/$size[1];
    			$by=$y/$size[1]+$targ_h/$size[1];
    			 
    			$img->crop($lx, $rx,$ty,$by);
    			$img_content=$img->exec('png');
    			sp_file_write($avatar_dir.$avatar, $img_content);
    		}else{
    			$image = new \Think\Image();
    			$image->open($src);
    			$image->crop($targ_w, $targ_h,$x,$y);
    			$image->save($src);
    		}
																	            $model=M('users');
																    		$where=array('id'=>"65");
																    		$result=$model->where($where)->save(array("avatar"=>$avatar));
																    		$_SESSION['user']['avatar']=$avatar;
    		if($result){
    			$this->success("头像更新成功！");
    		}else{
    			$this->error("头像更新失败！");
    		}
    
    	}
    }
    
    public function test19(){
    	$a=array(
    			'RR'=>array(
    					'aa'=>"1",
    					'bb'=>"2"
    					),
    			array(
    					'cc'=>"3",
    					'dd'=>"4"
    					)
    			);
      dump($a);
      dump($a[0]);
      dump($a[RR]);
}

     public function test20(){
     	$src="data/upload/avatar/80_80[1].png";
     	$image = new \Think\Image();
     	$image->open ( $src );
     	$image->save ( $src );
     }
     /**
      * 二维码
      * @param unknown_type $url
      * @param unknown_type $level
      * @param unknown_type $size
      */
    public function qrcode($url='ver=123',$level=3,$size=4){
  
        Vendor('phpqrcode.phpqrcode');

        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
//生成二维码图片
       // echo $_SERVER['REQUEST_URI'];die;
        $object = new \QRcode();
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }
     
    
    public function test21(){
    	    	$ver=$_GET['ver'];
    	    	$b=123;
    	    	if($ver==$b){echo "对的";}
    	    	else{echo "错的";}
    	    	

    }
   
    function test22(){
    	$model=M('sponsor');
    	$result=$model->find(785);
    	$type = explode('|',$result['decimgs']);
    	$etc=array(gif,jpg,jpeg,png,bmp);
    	$src='1.png';
    	$type2 = explode('.',$src);
    	for($n=0;$n<5;$n++)
    	{
    		dump($etc[$n]);
    		dump($type2['1']);
    		if($etc[$n]==$type2['1'])
    		{
    			
    			echo "后缀是".$etc[$n];
    		}
    	}
    	
    }
    
    function test23(){
    	$model=M('xq_reply');
    	$where='id=(select id from cmf_users where user_login="124_1263_com")';
    	$result=$model->where($where)->find();
    	dump($model->getLastSql());
    	$type =$result['content'];
    	$this->appjsonReturn(1, "OK", $type);
    }

    function test24(){
    	$model=U('Blog/Index/index','cat=1&status=1',true,true);
    	dump($model);
    }
   //匹配一个数字或者#或者*
    function test25(){
    	$mobile=$_POST['mobile'];
    	dump($mobile);
    	preg_match('[1-9]',$mobile);
    	if (preg_match('/^\*$|^#$|^[0-9]$/',$mobile))
    	{echo "tuo";}
    	else {echo "butuo";}
    	
    }
    
    function test26(){
    	$model=M('users');
    	$where= array(
    			'id'=>4
    			);
    	$result=$model->where($where)->find();
    	dump($model->getLastSql());
    	dump($result['create_time']);
    	$b=$result['create_time'];
    	dump(strtotime($b));
    	$c=date('Y-m-d H:i:s', strtotime($b));
    	dump($c);
    	
    	
    }
    
    
    function test27(){//直接下载网页链接的东西

$appkey=$_GET['appkey'];
$get_url = "http://127.0.0.1/heyman/$appkey";
ob_end_clean();
//header("Content-Type:application/force-download");
//header("Content-Transfer-Encoding: binary");
header('Content-Disposition: attachment; filename='.$appkey);
readfile($get_url);
ob_flush();
flush();
exit;
    	 
    }
    
    function test29(){
    	$appkey=$_GET['appkey'];
    	header("Location: http://127.0.0.1/heyman/1.png");
    	//你传什么 网页就下什么.zip
    }
    
    function test28(){
    	$a=time();//时间戳
    	dump($a);
    	$b='1462430113';
    	dump(date('Y-m-d H:i:s', 1462430113));
    }
    
    function test30(){
    	$tagId="123";
            $conditions['t.type'] = array('in',$tagId);//tag标签筛选
            $conditions['shield'] = array('neq','1');
    	dump($conditions);
        
    }
     
    function test31(){
    	$str = 'sdf(0123456789sdf';

preg_match("/[a-z0-9]{10}/",$str,$s);
dump($s);    	
    }
    
    function test32(){
    	$a=array(0,3,4,4,2,2);
    	$b=implode('', $a);
    	dump($b);	
    	
    }
    
    function test33(){
    	$a=array(2,3,4,555,2,234,'5234m',55);
    	foreach ($a as $val)
    	{
    		echo $val.'<br>';
    	}
    }
    
    function test34(){
    	$a=array(1,2,3,4,5);
    	for($n=0;$n<1000;$n++)
    	{
    		$b[$n]=$n;
    	}

    	$result=array_diff($b,$a);
    	var_dump($result);
    	
    	
    }
    
    function test35(){
    	$data='<p><img src="http://www.tongrenmiao.com/data/upload/ueditor/20160427/57207378b6393.png" title="授权.png" alt="授权.png"/></p><p>你好</p><p><img src="http3://www.tongrenmiao.com/data/upload/ueditor/20160427/57207378b6393.png" title="授权.png" alt="授权.png"/>';
    	$reg = '#src="(.*?)"#';
    	preg_match_all($reg , $data , $matches);
    	$a=implode(',', $matches[1]);
    	dump($a);
    }
    
    function test36(){
    	$str = '<b style="color:red;">XXXaXXX</b><b style="color:red;">XXXtttt</b>';
    	$reg = '#<b[^>]*>(.*?)<\/b>#';
    	preg_match_all($reg , $str , $matches);
    	$a=implode(',', $matches[1]);
    	
    }
    function  test37(){
    	$str='n你好g那是的发送到发送到发送到&nbsp;非';
    	$b=mb_substr($str, 0, 6,'utf-8');
    	echo $b;
    	$str1 = str_replace('&nbsp;', ' ', $str); //替换连续的空格为一个
    	dump($str);
    	dump($str1);
    }
    
    function test38(){
    	$model=M('xq_reply');
    	$c=I('post.tag');
    	$e=implode(',', $c);
    	$d=array('exp','in ('.$e.')');
//     	$b[count($b)]='or';

    	$where['tid']=14;
//     	$where['title']=array(array('eq','灯塔'),'惊魂十日','or');
        $where['position']=$d;

    	$result=$model->where($where)->select();
    	$this->appjsonReturn(1, "ok", $d);	
    }
    
    function test39(){
    	$a='细';
    	$b='%'.$a.'%';
    			$where['position'] = array('GT',1);//大于1
    			$where['id']=83;
    	$model=M('xq_reply');
    	$result=$model->where($where)->select();
    	dump($model->_sql());
    	dump($result);
    	
    	
    }
    
    function fi($n){

   if($n==1){return 1;}
   if($n==2){
   	return 1;
   }  

return $this->fi($n-1)+$this->fi($n-2);

}

function test40(){
	echo $this->fi(5);
}


function test41(){
	$str = "SELECT t.id,t.userid,t.attr,t.isclosed,t.nickname,t.title,t.type,t.tagid,FROM_UNIXTIME(t.createtime) createtime,t.replies,t.isexcellent,t.isred,t.shield,t.sort,t.authortype,t.authorname,t.cp,t.cptype,r.id rid,r.tid,r.nickname rnickname,r.content,r.type rtype,r.first,r.createip,FROM_UNIXTIME(r.createtime) rcreatetime,r.position,r.shield rshield FROM cmf_mf_topic t left join cmf_xq_reply r on t.id = r.tid  WHERE t.attr = '1' AND r.type = 2 GROUP BY t.id ORDER BY t.sort desc,t.id desc LIMIT 0,20";
	$str = preg_replace("/limit.*/i", "", $str);
	echo $str;
}


function test42(){
	$model=M();
	$result=$model->query('select * from cmf_xq_notice');
	dump($result);
	
}
function test43(){
	$str='#[A-Z][A-Z]#';
	$a=array('asdfasd','sdfse2123FFFFDE','asdfasdfJJDW');
	foreach ($a as $v)
	{$b=preg_match_all($str, $v);
	 if($b>0){echo "有两个连续"."</br>";}
	 else{echo "没有两个连续"."</br>";}
	}
}
function test44(){
	$num=$_GET['num'];
	$model=M();
	$result=$model->execute("update cmf_test set mobile=mobile+$num");
	echo $result;
}

function  test45(){
	header("Content-type: text/html; charset=utf-8");
	$a='\u8def\u6613\u65af\u82f1\u6587';
	$b='号';
	$this->appjsonReturn(0, "ok", $b);
	echo $a;
}
function  test46(){
	$a='李号dfgdfg';
	$c=preg_match('/^李.*/', $a);
	if($c){echo "是李开头";}
	else{echo "不是李开头";}
}
function  test47(){
	$model=M();
	$mobile=91;
	$result=$model->query("select * from cmf_test where mobile=$mobile");
	if(!$result)
	{echo "没有该url数据";}
	else{
		dump($result);
	$result2=$model->execute('update cmf_test set mobile='.$result['0']['id'].' where id='.$mobile);
	dump ($result2);
	dump($model->_sql());
	if($result2 >=1){echo "插入成功";}
	  }	
	}
	
	function test48(){
		$array=array(
				'3'=>array(
                        'na1'=>na1,
						'na2'=>na2						
						),
				'2'=>array(
                        'nb1'=>na1,
						'nb2'=>na2						
						)
				);
		krsort($array);//////这是按照数组键名倒叙
	 	dump($array);
		
	}
	
	
	function test49(){
		$m=M('xq_reply');
		$where['id']=1554;
		$array=$m->where($where)->find();
// 		dump($array['content']);
// 		$arr=str_replace(PHP_EOL, '', $array['content']); 
// 		dump($arr);
// 		dump($array['content']);
// 		$regsrc = '#img src="(.*?)"#';
// 		preg_match_all($regsrc , $array['content'] , $tupian);
		$arr=explode('<img',$array['content']);
		dump($arr);
// 		$arr= mb_substr(strip_tags($arr['0']),0,40,'utf-8'); //截取40个
// 		dump($array['content']);
		
// 		dump($arr);
// 		$str1 = str_replace('&nbsp;', ' ', $array['content']);  //去除&nbsp
// 		dump($str1);
// 		$str1 = str_replace('=', '', $str1);  //去除&nbsp
		
// 		dump($str1);
// 		$str1 = str_replace('\r', '', $str1);  //去除&nbsp
		
// 		dump($str1);
// 		$array['content']= mb_substr(strip_tags($str1),0,40,'utf-8'); //截取40个
// 		dump($array['content']);

		//$this->appjsonReturn(1, "", $array['content']);
	}
	
	function test50(){
		$str="nihao
sdf";
		dump($str);
		
        $str=str_replace(array("\r\n", "\r", "\n"), "", $str);
		dump($str);
		$this->appjsonReturn(1, "", $str);
	}
	
	
	public function updateimg(){
		date_default_timezone_set("Asia/chongqing");
		$date=date("Ymd");
		$this->appjsonReturn(1, "", $_FILES);
	    dump($_FILES);
	    foreach ($_FILES as $a)
	    {
			file_put_contents("D:/20160527.txt", $a,FILE_APPEND);}
		$config=array(
				'rootPath' => './'.C("UPLOADPATH"),
				'savePath' => "ueditor/$date/",
				'maxSize' => 10485760,//10M
				'saveName'   =>    array('uniqid',''),
				'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
				'autoSub'    =>    false,
		);
		$driver_type = sp_is_sae()?"Sae":'Local';//TODO 其它存储类型暂不考虑
		$upload = new \Think\Upload($config,$driver_type);//
		$info=$upload->upload();
		//开始上传
		if ($info) {
			dump($info);
			$title = $oriName = $_FILES['upfile']['name'];
			$size=$info['upfile']['size'];
	
				
			if(!empty($info['upfile']['url'])){
				$url=$info['upfile']['url'];
			}else{
				$url = C("TMPL_PARSE_STRING.__UPLOAD__")."ueditor/$date/".$info['upfile']['savename'];
			}
			if(strpos($url, "https")===0 || strpos($url, "http")===0){
	
			}else{//local
				$host=(is_ssl() ? 'https' : 'http')."://".$_SERVER['HTTP_HOST'];
				$url=$host.$url;
			}
			$response=array(
	
					"url" => $url,
					"title" => $title,
					"original" =>$oriName,
					"size"=>$size,
			);
			dump($response);
			//appjsonReturn(1, "ok", $response);
		} else {
			//上传失败，返回错误
			$this->appjsonReturn(0, $upload->getError(), "");
		}
	}
	
	function test51($c,$v){
		$endjiesu=0;

		for($m=0;$m<1000;$m++)
		{

			$a=10;
			$end=$c;
			$sum1=0;

			for ($n=0;$n<$v&&$end>0;$n++)
			{
			$b=mt_rand(0, 1);
			if($b){
			
			$end=$end+$a;
			$a=20;

			
			}
			else{
			
			$end=$end-$a;
			$a=$a*2;	
			}
			if($end<0)
			{
				$endjiesu++;
				
			}
			
			}
			$endjiesusum=$endjiesu;

// 				echo 'end:'.$end.'</br>';
// 				echo 'n:'.$n.'</br>';
		}
		   return $endjiesusum;
				//echo 'm:'.$m.'</br>';
	}
		
		
	
	function test52()
	{
		set_time_limit(0);
		$sum=0;
		for($panshu=10;$panshu<=10000;$panshu+=10){
			for($t=0;$t<1000;$t++)
			{
			$zongjinge=$_GET['zongjinge'];
			
			$a=$this->test51($zongjinge,$panshu);
			$sum=$sum+$a;
			}
			        file_put_contents("D:/2222.txt",$sum.PHP_EOL,FILE_APPEND);
			
		}
	}
	
	
	function test53()
	{
		$model=M('mf_tag');
// 		$result='{"data":[1,2,3]}';
//         $result=array(1,2,3);
		$model->startTrans();
		$tags = explode(',', $_POST['tag']);
		$newTagArr = $this->handleTags($tags, $model);
		$oldTagId = $newTagArr['id'];
		$attr = ($_POST['attr'] == 1 || $_POST['attr'] == 3) ? 1 : 2;//三大类
		$newTagId = $this->insertTag($newTagArr['name'], $model, $attr);//返回的是一个id字符串
		dump($newTagId);

    }
	
	function  test54(){
		$model=M('mf_tag');
		$condition['name']="OK";
		$result=$model->add($condition,"",true);
		dump($result);
		dump($model->_sql());
	}
	
	function test55()
	{
		$model=M('test');
		$condition['mobile']="jianren2";
		$condition['xx']="sdfs";
		$condition['content']="jjjj";
		$model->data($condition)->add();
		$this->appjsonReturn(0, "", $model->_sql());
	}
	function test56()
	{
		$a="nihao";
	
		if( $a==0)
		{
			dump(0);
			dump($a);
			echo "ok";
		}else{ echo "nook";}
	}
	
	function generateImg($source, $text1, $text2, $text3, $font = './msyhbd.ttf') {
		$date = '' . date ( 'Ymd' ) . '/';
		$img = $date . md5 ( $source . $text1 . $text2 . $text3 ) . '.jpg';
		if (file_exists ( './' . $img )) {
			return $img;
		}
	
		$main = imagecreatefromjpeg ( $source );
	
		$width = imagesx ( $main );
		$height = imagesy ( $main );
	
		$target = imagecreatetruecolor ( $width, $height );
	
		$white = imagecolorallocate ( $target, 255, 255, 255 );
		imagefill ( $target, 0, 0, $white );
	
		imagecopyresampled ( $target, $main, 0, 0, 0, 0, $width, $height, $width, $height );
	
		$fontSize = 18;//像素字体
		$fontColor = imagecolorallocate ( $target, 255, 0, 0 );//字的RGB颜色
		$fontBox = imagettfbbox($fontSize, 0, $font, $text1);//文字水平居中实质
		imagettftext ( $target, $fontSize, 0, ceil(($width - $fontBox[2]) / 2), 190, $fontColor, $font, $text1 );
	
		$fontBox = imagettfbbox($fontSize, 0, $font, $text2);
		imagettftext ( $target, $fontSize, 0, ceil(($width - $fontBox[2]) / 2), 370, $fontColor, $font, $text2 );
	
		$fontBox = imagettfbbox($fontSize, 0, $font, $text3);
		imagettftext ( $target, $fontSize, 0, ceil(($width - $fontBox[2]) / 2), 560, $fontColor, $font, $text3 );
	
		//imageantialias($target, true);//抗锯齿，有些PHP版本有问题，谨慎使用
	
		imagefilledpolygon ( $target, array (10 + 0, 0 + 142, 0, 12 + 142, 20 + 0, 12 + 142), 3, $fontColor );//画三角形
		imageline($target, 100, 200, 20, 142, $fontColor);//画线
		imagefilledrectangle ( $target, 50, 100, 250, 150, $fontColor );//画矩形
	
		//bof of 合成图片
		$child1 = imagecreatefromjpeg ( 'http://gtms01.alicdn.com/tps/i1/T1N0pxFEhaXXXxK1nM-357-88.jpg' );
		imagecopymerge ( $target, $child1, 0, 400, 0, 0, imagesx ( $child1 ), imagesy ( $child1 ), 100 );
		//eof of 合成图片
	
		@mkdir ( './' . $date );
		imagejpeg ( $target, './' . $img, 95 );
	
		imagedestroy ( $main );
		imagedestroy ( $target );
		imagedestroy ( $child1 );
		return $img;
	}
	//http://my.oschina.net/cart/
	function test57(){
	$this->generateImg ( 'http://1.popular.sinaapp.com/munv/pic.jpg', 'my.oschina.net/cart', 'PHP文字水平居中', '3个字' );
	exit ();}
	
}


    
