<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Xq\Controller;
use Common\Controller\HomebaseController;
use Think\Model;
header("Content-type: text/html; charset=utf-8");

class AppxqController extends HomebaseController
{
	//手机登陆的主页  默认闲情百合
    public function appxqindex()
    {
    	if(isset($_POST['theme'])&&!isset($_POST['id']))//如果收到了大类型（闲情1文库2）没有收到文章标题id
    	{
    		$order='t.sort desc,t.id desc';
    		$rollnotice=$this->appallnotice(1);//全站滚动公告
    		$globalnotice=$this->appallnotice(2);//全局公告
    		$attr=$_POST['attr']?$_POST['attr']:3;//默认是百合(大众1、腐向2、百合3)

    		if(isset($_POST['tag']))//选择那个1,2,3,4,5,6的标签
    		{
    			
    			$b=$this->jsontoarray($_POST['tag'],'tag');
    			//dump($_POST['tag']['0']);
    			//file_put_contents("D:/heyman/heyman/dump.txt", var_export($b,TRUE).PHP_EOL,FILE_APPEND);
    			if($_POST['tag']['0']!=="")
    			{
    				$c=implode(',', $b);
    			$d=array('exp','in ('.$c.')');	
    			$conditions['t.type']=$d;
    			}
    		//	dump($d);
    			//dump($d);
//     			}else{
//     				//dump($_POST['tag']);
//     				$b=json_decode($_POST['tag'],true);
//     				$c=implode(',', $b['tag']);
    				
//     				$d=array('exp','in ('.$c.')');
    				 
//     				$conditions['t.type']=$d;
//     				//dump($d);
//     			}
    		}                                                                                                                                  
    		if(isset($_POST['xiaotag'])&&$_POST['theme']==2)//文库收到小标签
    		{

    			$xiaotag=$this->pingtagid();//返回findinset(1,t.tagid) or....
    		    if($xiaotag!="")
    		    {$conditions['_string']=$xiaotag;}//自带小括号的
    		    //file_put_contents("D:/heyman/heyman/xiaotag.txt", var_export($xiaotag,TRUE).PHP_EOL,FILE_APPEND);
    			//dump($xiaotag);
    			
    		}
    		if($_POST['isexcellent']==1)//加精是1
    		{
    			$conditions['t.isexcellent']=1;
    		}
    		if($_POST['isred']==1)//套红是1
    		{
    			$conditions['t.isred']=1;
    		}
    		if(isset($_POST['userid'])&&$_POST['userid']!="")//我的帖子
    		{
    			$conditions['t.userid']=$_POST['userid'];
    		}
    		if(isset($_POST['content']))//主帖内容
    		{
    			$a=$_POST['content'];
    			//file_put_contents("D:/heyman/heyman/content.txt", $a.PHP_EOL,FILE_APPEND);
    			$b='%'.$a.'%';
    			$conditions['r.content'] = array('like',$b);
    			$conditions['r.position']=0;//1楼
    		}
    		if(isset($_POST['title']))//帖子主题
    		{
    			$a=$_POST['title'];
    			$b='%'.$a.'%';
    			$conditions['t.title'] = array('like',$b);
    		}
    		if(isset($_POST['nickname']))//主题帖发帖人
    		{
    			$a=$_POST['nickname'];
    			$b='%'.$a.'%';
    			$conditions['t.nickname'] = array('like',$b);
    		}
    		
    		if(isset($_POST['rcontent']))//跟帖内容
    		{
    			$a=$_POST['rcontent'];
    			$b='%'.$a.'%';
    			$conditions['r.content'] = array('like',$b);
    			$conditions['r.position'] = array('GT',0);//大于0
    		}
    		if(isset($_POST['rnickname']))//跟帖发帖人
    		{
    			$a=$_POST['rnickname'];
    			$b='%'.$a.'%';
    			$conditions['r.nickname'] = array('like',$b);
    		}
    		if(isset($_POST['tiaojian']))//文库条件
    		{
    			$a=$_POST['tiaojian'];
    			$b='%'.$a.'%';
                 //file_put_contents("D:/heyman/heyman/tiaojian.txt", $_POST['tiaojian'].PHP_EOL,FILE_APPEND);
    			//dump($tiaojian);
    			$conditions['t.authorname|t.cp|t.title|t.nickname|r.nickname|r.content'] =array('like',$b);
    			$order='t.authorname like "'.$b.'" desc,t.cp like "'.$b.'" desc,t.title like "'.$b.'" desc,t.sort desc,t.id desc';
    			//dump($conditions);
    		}
    		if($_POST['theme']=='1'||$_POST['theme']=='2')
    		{$conditions['t.attr']=$attr;$conditions['t.shield']=2;}
    		
    		$theme=$_POST['theme'];
    		$page=$_POST['page']?$_POST['page']:1;
    		//file_put_contents("D:/heyman/heyman/page.txt", $_POST['page'].PHP_EOL,FILE_APPEND);
    		$group='t.id';
    		$a=$this->appbaseinfo($conditions, $theme, $page, $order, $group);
    		$other=$a['other'];
            $data['pagecount']=$a['pagecount'];
   		    $data['page']=$_POST['page']?$_POST['page']:1;
    		$data['rollnotice']=$rollnotice;
    		$data['globalnotice']=$globalnotice;
    		for($n=0;($n<($page*20))&&$other[$n]['content']!=NULL;$n++)//可能不到end的数量
    		{
    			//dump($other[$n]['content']);
    			$regsrc = '#img src="(.*?)"#';
    			preg_match_all($regsrc , $other[$n]['content'] , $tupian);
    			$other[$n]['contentsrc']=$tupian[1];
    			$str1=explode('<img',$other[$n]['content']);
    			//dump($str1);
    			$str1 =str_replace(array("\r\n", "\r", "\n","&nbsp;","="), "", $str1['0']);//哎。。要图前面的文字
    			$other[$n]['contenttext']= mb_substr(strip_tags($str1),0,40,'utf-8'); //截取40个
    			//dump($other[$n]['contenttext']);
    		}
          
            showCharacter($other,2); 
            articleTag($other,1);
            $this->pinheadimg($other);
         
          //  dump($other);
    		$data['other']=$other;
//     		dump($data);
    		$this->appjsonReturn(1, "OK", $data);
    	}

    	elseif(isset($_POST['theme'])&&isset($_POST['id']))//如果收到了大类型（闲情1文库2）没有收到文章标题id
    	{
    		file_put_contents("D:/heyman/heyman/dump20160629.txt", date("Y-m-dH:i:s").PHP_EOL,FILE_APPEND);
    		file_put_contents("D:/heyman/heyman/dump20160629.txt", var_export($_POST,TRUE).PHP_EOL,FILE_APPEND);
//     		$attr=$_POST['attr']?$_POST['attr']:3;//默认是百合(大众1、腐向2、百合3)
//     	    if($_POST['theme']=='1'||$_POST['theme']=='2')
//     		{$conditions['t.attr']=$attr;}
    		$conditions['t.id']=$_POST['id'];
    	     if($_POST['theme']=='1'||$_POST['theme']=='2')
    		{$conditions['t.shield']=2;}
    		$conditions['r.shield']=2;//都要非屏蔽
    		$theme=$_POST['theme'];
   		    $page=$_POST['page']?$_POST['page']:1;
    		$order='r.position';
    		$a=$this->appbaseinfo($conditions, $theme, $page, $order, NULL);

    		$other=$a['other'];
    		showCharacter($other,1);
            articleTag($other,2);
            $this->pinheadimg($other);
            $this->quchuhuiche($other);
            if($theme==3 or $theme==4){$this->perfect($other);}
            if($theme==2){
            	$this->perfect2($other);
            }//张爽要的
    		$data['pagecount']=$a['pagecount'];
    		$data['page']=$_POST['page']?$_POST['page']:1;
    	    //以下6行安卓要的
    	    		for($n=0;($n<($page*20))&&$other[$n]['content']!=NULL;$n++)//可能不到end的数量
    		{
    			$str1=explode('<img',$other[$n]['content']);
    			$str1 =str_replace(array("\r\n", "\r", "\n","&nbsp;","="), "", $str1['0']);//哎。。要图前面的文字
    			$other[$n]['contenttext']= mb_substr(strip_tags($str1),0,40,'utf-8'); //截取40个
    		}
    		
    		$data['other']=$other;

    		file_put_contents("D:/heyman/heyman/dump20160629d.txt", var_export($data,TRUE).PHP_EOL,FILE_APPEND);
    		$this->appjsonReturn(1, "OK", $data);
    		
    	}else{
    		$this->appjsonReturn(0, "查询要求错误", "");
    	}

        
    	
    }
    
 /**
     * 新增帖子
     */
    public function addInvitation()
    {
        if($_POST)
        {
            $topic = M('xq_topic');
            $reply = M('xq_reply');
            $rules = array(
            		//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
            		array('nickname','1,15','昵称1-15位！',1,'length'),
            		array('title','1,90','标题1-90位！',1,'length'),
            		array('content','1,10000','内容1-10000位！',1,'length'), 		 
            );
            if($topic->validate($rules)->create()===false){
            	$this->appjsonReturn(0, $topic->getError(), "");
            }
            $data = array();
            $data2['createtime'] = $data['createtime'] = $data['lasttime'] = time();
            $data2['nickname'] = $data['nickname'] = $_POST['nickname'];
            $data['attr'] = $_POST['attr'];
            $data['type'] = $_POST['tag'];
            $data2['shield'] = $data['shield'] = 2;
            $data2['cryptonym'] = $_POST['isCryptonym'];//是否匿名
//            echo "<pre>";
//            print_r($_POST);die;
            $data2['title'] = $data['title'] = $_POST['title'];//过滤敏感字段
//            $data2['content'] = $_POST['content'];
            $data2['createip'] = $data['createip'] = get_client_ip();//获取ip
            $data2['type'] = 1;
            $data2['headimg'] = $_POST['systemHead'];
            $data['isexcellent'] = 2;
            $data['replies'] = 0;
            $data['isred'] = 2;
            $data['isclosed'] = 2;
            $data2['userid'] = $data['userid'] = $_SESSION['user']['id']?$_SESSION['user']['id']:null;
            $data2['content'] = $_POST['content'];//过滤敏感字段
            $data2['first'] = 1;
            $data2['position'] = 0;
//            print_r($data2['content']);die;
            M()->startTrans();
            $flag=true;
//            $sql = 'SET NAMES UTF8mb4';
//            mysqli_query($sql);
            $insertId = $topic->data($data)->add();
            if($insertId)
            {
                $data2['tid'] = $insertId;
                $result = $reply->data($data2)->add();
                if(!$result)
                {
                    $flag = false;
                }
            }
            if($flag)
            {
                M()->commit();
                $this->appjsonReturn(1, "ok", "");
            }
            else
            {
                M()->rollback();
            }
        }
        else
        {
            $this->appjsonReturn(0, "请求失败", "");
        }
    }
    

    /**
     * 新增文章
     */
    public function createArticle()
    {
    	file_put_contents("D:/heyman/heyman/dump20160628.txt", var_export($_POST,TRUE).PHP_EOL,FILE_APPEND);
    	if($_POST)
    	{
    			$topic = M('mf_topic');
    			$reply = M('xq_reply');
    			$model = M('mf_tag');//需要筛选的table
    			$rules = array(
    					//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
    					array('nickname','1,15','昵称1-15位！',1,'length'),
    			//array('nickname', '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]{1,15}+$/u', '用户名为15位内的汉字或英文或数字组合！' ,1),
    					//array('username','email','需要邮箱格式！',1),
    					//array('username','','该用户名注册过了',1,unique,3),
    					array('title','1,90','标题1-90位！',1,'length'),
    					array('content','1,10000','内容1-10000位！',1,'length'),
    					//array('userpwd','/^[a-zA-Z0-9]{6,12}$/','密码需要6~12位英文或数字！'),
    					//array('repassword','userpwd','确认密码不正确。',0,'confirm'),
    					//array('checkwenzhang','require','主办方协议须知！',1),
    			
    			);
    			if($model->validate($rules)->create()===false){
    			$this->appjsonReturn(0, $model->getError(), "");
    			}

    			
    			$model->startTrans();//开启事物
    			$through = true;
    			if ($_POST['tag'] != "") {
    				$tags = explode(',', $_POST['tag']);
    				if(count($tags)>12){$this->appjsonReturn(0, "tag超过12个了", "");}
    				$tags=array_unique($tags);//得数组去重
    				$newTagArr = $this->handleTags($tags, $model);
    				$oldTagId = $newTagArr['id'];
    				$attr = ($_POST['attr'] == 1 || $_POST['attr'] == 3) ? 1 : 2;//三大类
    				$newTagId = $this->insertTag($newTagArr['name'], $model, $attr);//返回的是一个id字符串
    			}
    
    			//$conditions => 存入mf_topic表    $conditions2 => 存入xq_reply表
    			$conditions = array();
    			$conditions2 = array();
    			$authorName=$this->jsontoarray($_POST['authorName'],'authorName');
    			if ($_POST['authortype'] == 1) {
    				$str = '';
    				
    				foreach ($authorName as $val) {
    					$str .= $val . '&&&&';
    				}
    				$conditions['authorname'] = trim($str, '&&&&');
    			} else {
    				$conditions['authorname'] = $authorName[0];
    			}
    			$conditions['cptype'] = $_POST['cptype'];
    			$conditions['tagid'] = ($oldTagId == "" || $newTagId == "") ? $oldTagId . $newTagId : $oldTagId . "," . $newTagId;
    			$conditions['attr'] = $_POST['attr'];
    			$conditions2['nickname'] = $conditions['nickname'] = $_POST['nickname'];
    			$conditions['type'] = $_POST['type'];//文章类型
    			$conditions['authortype'] = $_POST['authortype'];
    			$conditions2['title'] = $conditions['title'] = $_POST['title'];
//     			$conditions2['hiddentext'] = $_POST['post']['edit_content'];
    			$conditions2['content'] = $_POST['content'];
    			$conditions2['createip'] = $conditions['createip'] = get_client_ip();
    			$conditions2['headimg'] = $_POST['systemHead'];
    
    			/*-----一些发帖的默认状态---------*/
    			$conditions2['userid'] = $conditions['userid'] = $_SESSION['user']['id'] ? $_SESSION['user']['id'] : null;
    			//            $conditions['attr'] = $_POST['attr'];//文章大类
    			$conditions2['first'] = 1;
    			$conditions2['cryptonym'] = $_POST['isCryptonym'];//是否匿名
    			$conditions['isexcellent'] = 2;
    			$conditions['isred'] = 2;
    			$conditions['isclosed'] = 2;
    			$conditions['replies'] = 0;
    			$conditions2['shield'] = $conditions['shield'] = 2;
    			$conditions2['position'] = 0;
    			$conditions2['type'] = 2;
    			$conditions2['createtime'] = $conditions['createtime'] = $conditions['lasttime'] = time();
    			/*-----一些发帖的默认状态---------*/
    			$gong=$this->jsontoarray($_POST['gong'],'gong');
    			
    			$shou=$this->jsontoarray($_POST['shou'],'shou');
    			$gongArr = array_values(array_filter($gong));//过滤掉数组中的空值
    			$shouArr = array_values(array_filter($shou));//过滤掉数组中的空值
    			$conditions['cp'] = $this->handlerole($_POST['cptype'], $gongArr, $shouArr);//处理角色
    			//            $sql = 'SET NAMES UTF8mb4';
    			//            mysqli_query($sql);
    			if ($through) {
    				
    					$tid = $topic->data($conditions)->add();
    					if (!$tid) {
    						$through = false;	
    				}
    
    			}
    
    			if ($through) {
    				
    					$conditions2['tid'] = $tid;
    					$result = $reply->data($conditions2)->add();
    					if (!$result) {
    						$through = false;
    				    }
    			}
    			if ($through) {
    				$model->commit();
    				$this->appjsonReturn(1, "OK", "");
    			} else {
    				$model->rollback();
    				$this->appjsonReturn(0, "发布失败", "");
    			}

    	}
    	else
    	{
    		$this->appjsonReturn(0, "发布失败", "");
    	}
    }
    
    
    
    
    
    
    
    
    
    
    
    //获取全局限定的
    public function set()
    {
    	$xiandingmodel=M('set');//全局限定
    	$xiandingresule=$xiandingmodel->find();
    	$result=array(
        'report'=>$xiandingresule['report_gap'],//发帖时间间隔
        'reply'=>$xiandingresule['reply_gap'],//回帖时间间隔
    	'global'=>$xiandingresule['global_notice_num'],//全局公告数	
    	'roll'=>$xiandingresule['roll_notice_num'],//滚动（全站）公告数	
    	'top'=>$xiandingresule['top_notice_num']//置顶公告数
        );
    	return $result;
    	
    }

    
    /**
     * app获得全站公告(滚动)和全局公告的id和title
     * 
     */
    function appallnotice($id)
    {
    
    	$set=$this->set();//获取闲情的各项限定 暂时没用
    	$model = M('xq_notice');
    	$condition['isroll'] = $id;//1滚动 2正常
    	$condition['type'] = $id;//1全站 2全局
    	//$condition['r.type'] = 3;//1闲情、2文库、3全站公告、4全局公告
    	$list = $model
    	->field('id,title')
    	->where($condition)
    	->order('id desc')
    	->limit($id)
    	->select();

    	return $list;
    }
    
    /**
     * app根据id(topic里的唯一标识) attr(大众、腐向、百合123)和theme(1xq 2mf)来获取标题或内容
     *
     */
    function appbaseinfo($conditions,$theme,$page,$order,$group)
    {


         if ($theme=='1')
         {
         	$m='xq_topic t';
         	$str = 't.id,t.attr,t.isclosed,t.type,t.nickname,t.userid,t.title,FROM_UNIXTIME(t.createtime) createtime,t.replies,t.isexcellent,t.isred,t.shield,t.sort,';
         	$conditions['r.type']=1;
         }
         else if($theme=='2')
         {
         	$m = 'mf_topic t';
         	$str = 't.id,t.userid,t.attr,t.isclosed,t.nickname,t.title,t.type,t.tagid,FROM_UNIXTIME(t.createtime) createtime,t.replies,t.isexcellent,t.isred,t.shield,t.sort,t.authortype,t.authorname,t.cp,t.cptype,';
         	$conditions['r.type']=2;
         }else if($theme=='3')
         {
         	$m = 'xq_notice t';
         	$str = 't.id,t.isclosed,t.type,t.nickname,t.title,FROM_UNIXTIME(t.createtime) createtime,t.replies,t.sort,';
            $conditions['r.type']=3;
         }else if($theme=='4')
         {
         	$m = 'xq_notice t';
         	$str = 't.id,t.isclosed,t.type,t.nickname,t.title,FROM_UNIXTIME(t.createtime) createtime,t.replies,t.sort,';
         	$conditions['r.type']=4;
         }
         
         else{$this->appjsonReturn(0, "主题查询错误", "");}
         $model=M("$m");
         $list = $model
         ->field($str.'r.id rid,r.tid,r.nickname rnickname,r.content,r.type rtype,r.first,r.createip,FROM_UNIXTIME(r.createtime) rcreatetime,r.position,r.shield rshield,r.headimg')
         ->join('left join '.C('DB_PREFIX').'xq_reply r on t.id = r.tid')
         ->where($conditions)
         ->group($group)
         ->order($order)
         ->page($page,20)
         ->select();
         //file_put_contents("d:/20160628.txt", $model->_sql(),FILE_APPEND);
 // dump($model->_sql());
//                 echo "贴子数据集（限制20条）：".M()->_sql()."<br>";
           $str = preg_replace("/ORDER.*/i", "", $model->_sql());
          // file_put_contents("d:/20160628str.txt", $str,FILE_APPEND);
           $sql="select count(*) from (".$str.") bb";
        
         $result=$model->query($sql);
         $count=$result['0']['count(*)'];	
         $pagecount=ceil($count/20);if($pagecount==0){$pagecount=1;}
         $a['pagecount']=$pagecount;
         $a['other']=$list;
         return $a;
         	
    }
    

    public function updateimg(){
    	date_default_timezone_set("Asia/chongqing");
    	$date=date("Ymd");
    	//$this->appjsonReturn(1, "", $_FILES);
    	file_put_contents("D:/20160627.txt",var_export($_FILES['upfile'],TRUE).PHP_EOL,FILE_APPEND);
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
    		//dump($info);
          	$title = $oriName = $_FILES['upfile']['name'];
			$size=$info['upfile']['size'];
		
			//dump($info);
			if(!empty($info['upfile']['url'])){
				$url=$info['upfile']['url'];
			}else{
				$url = C("TMPL_PARSE_STRING.__UPLOAD__")."ueditor/$date/".$info['upfile']['savename'];
			}
			//dump($url);
			if(strpos($url, "https")===0 || strpos($url, "http")===0){
		    
			}else{//local
				$host=(is_ssl() ? 'https' : 'http')."://".$_SERVER['HTTP_HOST'];
				$url=$host.$url;
			}
			//dump($_SERVER['HTTP_HOST']);
			//dump(url);
			$response=array(

					"url" => $url,
					"title" => $title,
					"original" =>$oriName,
					"size"=>$size,
			);			
			$this->appjsonReturn(1, "ok", $response);		
    	} else {
    		//上传失败，返回错误
    		$this->appjsonReturn(0, $upload->getError(), "");
    	}
    }
    
    
    
    function test1(){
    	$conditions['t.attr']=3;
    	$theme=2;
    	$start=0;
    	$end=2;
    	$order='t.sort desc,t.id desc';
    	$this->appbaseinfo(0, $conditions, $theme, $start, $end, $order, NULL);
    }

    function test2(){
		//dump($_REQUEST);die;
    }
  
    function text7()
    {
    	$user_agent = $_SERVER['HTTP_USER_AGENT'];
    	echo $user_agent;
    	$b=array(99,8,7,6,44,5,3,2,33,1);
    	dump($b);
    	for($n=0;$n<10;$n++)
    	{
    		for($m=0;$m<10;$m++)
    		{
    			if($b[$n]<$b[$m])
    			{
    				$temp=$b[$n];
    				$b[$n]=$b[$m];
    				$b[$m]=$temp;
    			}
    		}
    	}
    	dump($b);
    }
    
    function test3(){
    	$str=Array("孔笙","简川訸","123");
    	$str1='主演：';
    	foreach ($str as $val)
                {
                    $str1 =$str1.$val.',';

                }
                $strend=trim($str1,',');//去除最后个逗号
                echo $strend;
    }
    
    function test4(){
    	$a='{
    		"杀八方": {
    		"user": "abc"
    		},
    		"杀八方2": {
    		"user": "abc"
    		}
    	}';
    	$b=json_decode($a,true);
    	$b['杀八方2']=array();
    	$c=json_encode($b,JSON_UNESCAPED_UNICODE);

    }
    
    function test5(){
    	$model=M();
     	$result=$model->query("insert into cmf_test(mobile) values('333');");
//     	$result=$model->execute("update cmf_test set mobile=2342342 where id=70;");
    //	dump($result);
    }
    
    function test6(){
    	$a='井';
    	$b='星';
    	for($n=1;$n<100;$n++)
    	{
    		if(is_int($n/2))
    		{
    			for($m=0;$m<$n;$m++)
    			{
    				echo $b;
    			}
    			
    		}else{
    			for($m=0;$m<$n;$m++)
    			{
    			   echo $a;
    			}
    			
    		}
    		echo '<br/>';
    	}
    }
    
    function test7(){
            $model=M("xq_reply");
            $where=array(
            		'id'=>91,
            		'tid'=>17,
            		
            		);
            $result=$model->where($where)->find();

            echo time();
    		$new = strip_tags($result['content'],'<img>');
    	 echo time();
    	//	dump($new);
    }

    function test8(){
    	$url = U('index/topicDetail',array(type=>1,id=>2));
    	//dump($url);
    	
    	
    }
    
    function test9(){
    	$m=M('xq_notice');
    	$m->query('select * from cmf_xq_notice');
    	
    }
    function test10(){
    	 $this->display(':text7');
    	 
    }
    function test11()
    {
    	file_put_contents("D:201605191.txt", date('Y-m-d H:i:s').'</br>',FILE_APPEND);
    	file_put_contents("D:201605191.txt", $_POST['tt'],FILE_APPEND);

    	$this->appjsonReturn(1, "ok", $_POST);
//     $a=$_POST['theme'];
//     $m=M('test_copy');
//     $where['id']=1;
//     $m->where($where)->save(array('mobile'=>$a));
    }
    
    function test12()
    {
    	//     	file_put_contents("D:201605191.txt", date('Y-m-d H:i:s').'</br>',FILE_APPEND);
    	//     	file_put_contents("D:201605191.txt", $_POST,FILE_APPEND);
    	//     	dump($_POST);

    	$m=M('test_copy');

    	$result=$m->find();
    	dump($result);
    	echo $result['mobile'];
    }
    
    
    
    /**
     * 收到json变数组
     * @param   $a 需要判断的变量
     * @param   $b 是json的话 需要截取的名字
     */
    function jsontoarray($a,$b)
    {
    	//dump($a);
    	if(is_array($a))
    	{
    		return $a;
    	}else{
    		$result=json_decode($a,true);
    		$result1=$result[$b];
    		return $result1;
    	}
    }
    function  test89()
    {
    	dump($this->test88($_POST['tag'], 'tag'));
    }
    
    
    
    /**
     *筛选出数据表中已存在数据的id和不存在name值
     * @param  Array $tags 标签数组
     * @param  table $model
     */
    function handleTags($tags,$model)
    {
    	//先查出来所有的tag表里面所有的标签
    	$cmfTagArr = $model->select();
    	$idStr='';
    	$newTagArr = array();
    	foreach($tags as $val)
    	{
    		foreach ($cmfTagArr as $v)
    		{
    			if($v['name'] == $val)
    			{
    				$idStr .= $v['id'].',';//数据库已存在提取id;
    				$sameArr[] = $val;//取出已存在的标签名称
    			}
    		}
    	}
    
    	$newTagArr['id'] = trim($idStr,',');
    	if(count($sameArr) == 0)
    	{
    		$newTagArr['name'] = $tags;
    	}
    	else
    	{
    		$newTagArr['name'] = array_values(array_diff($tags,$sameArr));//取出差集
    	}
    	return $newTagArr;
    }
    
    /**
     *插入数据返回数据Id
     * @param  Array  $value  需要插入的数据（可以是多条，也可是单条）(一维数组)
     * @param  Table  $model
     * @param  Int    $type   1=>百合&大众  2=>腐向
     */
    public function insertTag($value,$model,$type)
    {
    	$idStr = "";
    	//用户添加的不存类型  后台添加的存类型
    	//        $conditions['type'] = $type;
    	//        print_r($conditions);die;
    	foreach($value as $val)
    	{
    		$conditions['name'] = $val;
    		$insertId = $model->add($conditions);
    		if(insertId)
    		{
    			$idStr .= $insertId.',';
    		}
    	}
    	return trim($idStr,',');
    }
    
    /**
     *角色类型拼接（数据库存）
     * @param  Int    $cptype    角色类型 1=>cp  2=>np  3=>无cp
     * @param  Array  $gongArr  攻
     * @param  Array  $shouArr  受
     */
    public function handlerole($cptype,$gongArr,$shouArr)
    {
        if($cptype == 1)
        {
            //拼接cp
            $str2 = "";
            foreach($gongArr as $key=>$val)
            {
                foreach($shouArr as $k=>$v)
                {
                    if($key == $k)
                    {
                        $str2 .=trim($val).'&&&&'.trim($v).',';
                    }
                }
            }
            $cp = $str2;
        }
        if($cptype == 2)
        {
            //拼接np
            $str3 = "";
            $str4 = "";
            if(count($gongArr) < 2)
            {
                $str3 = $gongArr[0];
            }
            else
            {
                foreach($gongArr as $val)
                {
                    $str3 .= trim($val).',';
                }
                $str3 = trim($str3,',');
            }
            if(count($shouArr) < 2)
            {
                $str4 = $shouArr[0];
            }
            else
            {
                foreach($shouArr as $val)
                {
                    $str4 .= trim($val).',';
                }
                $str4 = trim($str4,',');
            }
            $cp = $str3."&&&&".$str4;
        }
        if($cptype == 3)
        {
            $type = '';
            foreach($gongArr as $val)
            {
                $type .= trim($val).',';
            }
            $cp = $type;
        }
        return trim($cp,',');
    }
    
    /**
     * 文库在数据库里查询tagid的
  
     */
    function pingtagid(){
    	$tag = M('mf_tag');
    	$xiaotag=$this->jsontoarray($_POST['xiaotag'],'xiaotag');
    	$str="";
    	foreach ($xiaotag as $v)
    	{
    		$tagid=$tag->field('id')->where("name='$v'")->find();
    		if($tagid)
    		{
    			$tagidid=$tagid['id'];

    			$str=$str."FIND_IN_SET($tagidid,t.tagid) or ";

    		}

    	}

    	return trim($str,"or ");
    	
    	}
    
    	
    	/**
    	 * 回复
    	 */
    	public function appreply()
    	{
    		$url="";
    		file_put_contents("D:/heyman/heyman/dump20160629r.txt", date("Y-m-dH:i:s").PHP_EOL,FILE_APPEND);
    		file_put_contents("D:/heyman/heyman/dump20160629r.txt", var_export($_POST,TRUE).PHP_EOL,FILE_APPEND);
    		if($_POST)
    		{

    				if($_POST['theme'] == 1)
    				{
    					$topic = M('xq_topic');
    				}
    				else if($_POST['theme'] == 2)
    				{
    					$topic = M('mf_topic');
    				}
    				else if($_POST['theme']== 3 ||$_POST['theme']== 4 )
    				{
    					$topic = M('xq_notice');
    				}
    				$rules = array(
    						//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
    						array('nickname','1,15','昵称1-15位！',1,'length'),
    						array('content','1,10000','内容1-10000位！',1,'length'),
    				);
    				if($topic->validate($rules)->create()===false){
    					$this->appjsonReturn(0, $topic->getError(), "");
    				}
    				$conditions['headimg'] = $_POST['systemHead'];
    				$conditions['userid'] = $_SESSION['user']['id']?$_SESSION['user']['id']:null;
    				$conditions['nickname'] = $_POST['nickname'];
    	
    				$conditions['content'] =  $_POST['content'];
    				//$conditions['content'] =str_replace(array("&nbsp;"), " ", $conditions['content']);
    				$data['type'] = $conditions['type'] = $_POST['theme'];
    				$conditions['first'] = 2;
    				$conditions['shield'] = 2;
    				$conditions['createip'] = get_client_ip();
    				$data['tid'] = $conditions['tid'] = $_POST['tid'];
    				$conditions['createtime'] = time();//最新回复时间
    				$model = M('xq_reply');
    				//根据tid找到主帖
    				$countPosition = $topic->field('replies')->where('id ='.$_POST['tid'])->find();//在回复数上加1作为楼层，避免楼层被删出现楼层错误
    				$conditions['position'] = $countPosition['replies']+1;//最新楼层
    				$conditions['cryptonym'] = $_POST['isCryptonym'];
    				$conditions['isadmin'] = $_POST['isAdmin']?$_POST['isAdmin']:null;//该字段临时用来判断回复贴书否属于管理员
    	
    				$model->startTrans();
    				$through = true;
    				//            $sql = 'SET NAMES UTF8mb4';
    				//            mysqli_query($sql);
    				$insert = $model->data($conditions)->add();
    	
    				if($insert)
    				{
    					//拿到添加回复的id之后，，要查出当前这条回复数与第几页，前台要定位到第几页具体楼层
    					$data['id'] = array('lt',$insert);
    					$update = $topic->where('id = '.$_POST['tid'])->setField('lasttime',$conditions['createtime']);
    					if($update>0 || $update ===0 )
    					{
    						$through = true;
    					}
    					else
    					{
    						$through = false;
    					}
    				}
    	
    				if($through)
    				{
    					$replies = $topic->where('id='.$_POST['tid'])->setInc('replies');
    					if($replies>0 || $replies ===0 )
    					{
    						$through = true;
    					}
    					else
    					{
    						$through = false;
    					}
    				}
    	
    				if($through)
    				{
    					$model->commit();
    					$datareply['louceng']=$conditions['position'];
    					$this->appjsonReturn(1, "ok", $datareply);
    				}
    				else
    				{
    					$model->rollback();
    				}

    		}
    		else
    		{
    			$this->appjsonReturn(0, "回复失败", "");
    		}
    	}
    	 
    	
    	
    	
    	/*
    	 * 发表回复之后，查询该条回复属于第几页，前台页面跳转需要调转到指定页和定位到指定楼层
    	* param  array  $data  条件（回复表中的type，tid）
    	*/
    	function replyPosition($data)
    	{
    		$pageNum = 99;  //暂定回复详情页面是100楼
    		$m = M('xq_reply');
    		$result = $m->where($data)->count();
    		$p = ceil($result/$pageNum);
    		return $p;
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
    	
    	/**
    	 * 给手机端headimg 拼链接
    	 * @param string  $list  文章列表
    	 * @param int     $isDetail  1=>标题   2=>详情
    	 */
    	function pinheadimg(&$list)
    	{
    		//查出当前属性下的文章标签
    	
    		foreach($list as $key=>$val)
    		{
    			if($val['headimg'] != null)
    			{
    				$list[$key]['headimg']='http://192.168.1.66'.$list[$key]['headimg'];
    			}
    		}
    	
    		//    return $list;
    	}
    	
    	/**
    	 * 给手机端去除/r/n/t之类的

    	 */
    	function quchuhuiche(&$list)
    	{
    		//查出当前属性下的文章标签
    		 
    		foreach($list as $key=>$val)
    		{
    			if($val['content'] != null)
    			{
    				$list[$key]['content']=str_replace(array("\r\n", "\r", "\n"), "<br/>", $list[$key]['content']);
    				$list[$key]['content']=str_replace(array("\t"), " ", $list[$key]['content']);
    			}
    		}
    		 
    		//    return $list;
    	}
    	 
    	/**
    	 * 给张爽补全data['other']
    	
    	 */
    	function perfect(&$list)
    	{
    		//全局全站公告，字段补全完善
    		 
    		foreach($list as $key=>$val)
    		{
    			if($val['content'] != null)
    			{
    				$list[$key]['shield']=2;
    				$list[$key]['isexcellent']=1;
    				$list[$key]['isred']=1;
    				$list[$key]['cptype']="";
    				$list[$key]['authortype']="";
    				$list[$key]['cp']="";
    			}
    		}
    		 
    		//    return $list;
    	}
    
    	/**
    	 * 给张爽补全data['other']
    	  
    	 */
    	function perfect2(&$list)
    	{
    		//全局全站公告，字段补全完善
    		 
    		foreach($list as $key=>$val)
    		{
    			if($val['content'] != null)
    			{
    				$list[$key]['cptype']="";
    				$list[$key]['authortype']="";
    				$list[$key]['cp']="";
    			}
    		}
    		 
    		//    return $list;
    	}
    	
    	
    
}