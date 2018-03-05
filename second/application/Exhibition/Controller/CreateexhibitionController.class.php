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
class CreateexhibitionController extends HomebaseController {
	
    //首页
	public function index() {

		$id=session('id_sponsor');
        if(is_null($id)) {$this->error("无效空值！");} 
		$users_model=M("sponsor");
        
		$user=$users_model->find($id);//$id为null的时候读取的是第一位的数据
		//dump($user);
		file_put_contents("d:/20160328.txt", gettype($id).$users_model->getlastsql(),FILE_APPEND);

		//var_dump($user);
		if(empty($user)){
			$this->error("查无此人！");
		}
		$this->assign($user);
    	$this->display(":createexhibition/create1");
    }
    
 
    //创建展会信息1
    public function create1()
    {

    	if(isset($_SESSION['id_sponsor']))
    	{
    		$rules = array(
    				//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
    				array('coverimg', 'require', '展会封面不能为空！', 1 ),
    				array('name', 'require', '展会名称不能为空！', 1 ),
    				array('start', 'require', '开始时间不能为空！', 1 ),
    				array('end','require','结束时间不能为空！',1),
    				array('address','require','地点不能为空！',1),
    				array('description','require','展会描述不能为空！',1),
    				array('position','require','展会位置不能为空！',1),
    				array('traffic','require','交通方式不能为空！',1),
    				array('extype','require','展会类型不能为空！',1),
    				array('certifyimg','require','场地租借证明不能为空！',1),
    		);
    
    		$model = M("basic_exhibition");
    		//file_put_contents("d:/20160323.txt",'model过le'.$model->getlastsql().'------------------',FILE_APPEND);
    		   if($model->validate($rules)->create()===false){
    			$this->error($model->getError());
    		}
    		//file_put_contents("d:/20160323.txt",'creat过le'.$model->getlastsql().'------------------',FILE_APPEND);
    		 
    		
    		$datacreate1=array(
    				'coverimg'=>$_POST['coverimg'] ,
    				'name'=>$_POST['name'] ,
    				'start'=>$_POST['start'] ,
    				'end'=>$_POST['end'] ,
    				'address'=>$_POST['address'] ,
    				'description'=>$_POST['description'] ,
    				'position'=>$_POST['position'] ,
    				'traffic'=>$_POST['traffic'] ,
    				'extype'=>$_POST['extype'] ,
    				'certifyimg'=>$_POST['certifyimg'] ,
    				'id_sponsor'=>$_SESSION['id_sponsor'],
    
    		);
    		$id_basic_exhibition = $model->add($datacreate1);
    		file_put_contents("d:/20160323.txt",'add过le'.$id_basic_exhibition.$model->getlastsql().'------------------',FILE_APPEND);
    		if($id_basic_exhibition){
    			//登入成功页面跳转

                $_SESSION['id_basic_exhibition']=$id_basic_exhibition;
    			$this->success("填写成功！",U("Exhibition/createexhibition/create2tiaozhuang"));
    
    		}else{
    			$this->error("注册失败！",U("Exhibition/createexhibition/create1"));
    		}
    		 
    	}
    	else
    	{
    
    		$this->display(":sponsor/reg");
    	}
    
    }
    
    public function quxiaochuangjian(){
    	
    	
    }
    public function create2tiaozhuang(){ $this->display(create2); }
    public function create2()
    {
    //那个票种名称 是传到别人的数据库的
    	if(isset($_SESSION['id_sponsor']))
    	{
    		$rules = array(
    				//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
    				array('freight','number','运费必须数字！',1),
    				array('freight_threshhold','number','免运费阈值必须数字！',1),
    		);	
    
    		$model = M("ticket_saleinfo");
    		//file_put_contents("d:/20160323.txt",'model过le'.$model->getlastsql().'------------------',FILE_APPEND);
    		if($model->validate($rules)->create()===false){
    			$this->error($model->getError());
    		}
    		//file_put_contents("d:/20160323.txt",'creat过le'.$model->getlastsql().'------------------',FILE_APPEND);
    		 
    
    		$datacreate2=array(
    				'sale_description'=>$_POST['sale_description'] ,
    				'other_saleurl'=>$_POST['other_saleurl'] ,
    				'ise_ticket'=>$_POST['ise_ticket'] ,
    				'freight'=>$_POST['freight'] ,
    				'freight_threshhold'=>$_POST['freight_threshhold'] ,
    				'id_basic_exhibition'=>$_POST['id_basic_exhibition'] ,

    
    		);
    		$id_ticket_saleinfo = $model->add($datacreate2);
    		//file_put_contents("d:/20160323.txt",'add过le'.$id_basic_exhibition.$model->getlastsql().'------------------',FILE_APPEND);
    		if($id_ticket_saleinfo){
    			//登入成功页面跳转
    
    			$_SESSION['id_ticket_saleinfo']=$id_ticket_saleinfo;
    			$this->success("填写成功！",U("Exhibition/createexhibition/create3tiaozhuang"));
    
    		}else{
    			$this->error("注册失败！",U("Exhibition/createexhibition/create1"));
    		}
    		 
    	}
    	else
    	{
    
    		$this->display(":sponsor/reg");
    	}
    
    }
    
}


