<?php
namespace Xq\Controller;
use Xq\Controller\BaseController;
header("Content-type: text/html; charset=utf-8");
class AdminController extends BaseController
{
	public function _initialize()
	{
		if(!isset($_SESSION['ADMIN_ID']))
		{
			$this->error('请登录',U('Base/login'));
		}
	}

	public function index()
	{
		$this->display(":Admin/index");
	}

    /**
     * 主题帖和文章
     */
	public function article_list()
	{
		$this->type = $_GET['type'];
		if($_GET['type'] == 1)
		{
			$model = 'xq_topic';//闲情主题
			$conditions['t.type'] = array('neq',6);
		}
		else
		{
			$model = 'mf_topic';//文库
			$conditions['t.type'] = array('neq',3);
		}
		$conditions['r.type'] = $_GET['type'];
		$conditions['r.first'] = 1;
		$this->isXq = $_GET['type'];
		if($_REQUEST['filter']) //给一个隐藏域作为标识
		{
			//筛选
			if($_REQUEST['states']!=0)
			{
				$conditions = $this->filterState($_REQUEST['states']);
			}
			if($_REQUEST['attrs']!=0)
			{
				$conditions['t.attr'] = $_REQUEST['attrs'];
			}
			if($_REQUEST['tags']!=0)
			{
				$conditions['t.type'] = $_REQUEST['tags'];
			}

			$this->attr=$_REQUEST['attrs'];
			$this->tags=$_REQUEST['tags'];
			$this->state=$_REQUEST['states'];
		}
		if($_REQUEST['searchInfo'])//搜索
		{
			$this->searchInfo = $_REQUEST['searchInfo'];
			$search['t.nickname'] = array('like','%'.htmlentities(trim($_REQUEST['searchInfo'])).'%');
			$search['t.title'] = array('like','%'.htmlentities(trim($_REQUEST['searchInfo'])).'%');
			if($_POST['searchType'] == 2)
			{
				$search['t.cp'] = array('like','%'.htmlentities(trim($_REQUEST['searchInfo'])).'%');
				$search['t.authorname'] = array('like','%'.htmlentities(trim($_REQUEST['searchInfo'])).'%');
			}
			$search['_logic'] = 'or';
			$conditions['_complex'] = $search;
		}
		$this->tagList = XqTag(0,$_GET['type']);//筛选标签
		$field = 't.*,u.user_email,u.mobile,u.user_login,r.type rtype';
		$group = 't.id';//去重字段
		/*--------page---------*/
		$p = $_GET[p]?$_GET[p]:1;
		$pageModel = $model.' t';
		$list = $this->selectInfo($model,$conditions,$field,$group,$p,$pageNum=30);
		$show = getPage($pageModel,$conditions,$pageSize=30);//分页显示
		$this->page = $show->show();// 分页显示输出
		/*--------page---------*/
		$this->showTagName($list,$_GET['type']);
		$this->handle($list);
		$this->stateList = stateList();//筛选状态
		$this->xq_topic=$list;
		$this->display(":Admin/article_list");
	}

	/**
	 * Ajax更改当前帖子状态
	 */
	public function ajaxChangeState()
	{
		$id = $_POST['mesId'];//帖子/文章id
		$stateType = $_POST['stateType'];//区分当前点击要更改的状态(1=>精华  2=>套红  3=>屏蔽  4=>关闭)
		if($_POST['stateVal'] == 1)
		{
			$stateVal = 2;//要存入数据库的状态值
		}
		else
		{
			$stateVal = 1;//要存入数据库的状态值
		}
		$type = $_POST['type'];//1=>闲情  2=>文库  3=>回帖（只有屏蔽）
		if($type)
		{
			if($type == 1)
			{
				$model = 'xq_topic';
				$field = "isexcellent,isred,shield,isclosed";
			}
			if($type == 2)
			{
				$model = 'mf_topic';
				$field = "isexcellent,isred,shield,isclosed";
			}
			if($type == 3)
			{
				$model = 'xq_reply';
				$field = "shield";
			}
		}
		$result = $this->changeField($model,$id,$stateType,$stateVal);
		if($result)
		{
			$allStateVal = M($model)->field($field)->where('id in('.$id.')')->select();
			if(count($allStateVal)>1)
			{
				$result = 1;
			}
			else
			{
				$result = $allStateVal;
			}
			$this->ajaxReturn($result);
		}
	}

	/**
	 * 修改状态字段
	 * @param  model   $model      数据表
	 * @param  number  $id         帖子/文章id
	 * @param  number  $stateType  区分当前点击要更改的状态(1=>精华  2=>套红  3=>屏蔽  4=>关闭)
	 * @param  number  $stateVal   要存入数据库的状态值
	 */
	public function changeField($model,$id,$stateType,$stateVal)
	{
		if($stateType == 1)
		{
			$field = 'isexcellent';
		}
		elseif($stateType == 2)
		{
			$field = 'isred';
		}
		elseif($stateType == 3)
		{
			$field = 'shield';
		}
		elseif($stateType == 4)
		{
			$field = "isclosed";
		}
		else
		{
			$this->error('没有该状态');
		}
		$result = M($model)->where('id in('.$id.')')->setField($field,$stateVal);
		return $result;
	}

	/**
	 * 查询帖子或回复
	 * @param  model   $model    实例化模型
	 * @param  array   $where    查询条件
	 * @param  string  $field    查询字段
	 * @param  string  $group    去重字段
	 * @param  int     $p        页数
	 * @param  int     $pageNum  条数
	 */
	public function selectInfo($model,$where,$field,$group,$p,$pageNum)
	{
		$list = M($model.' t')
			->field($field)
			->join('left join '.C('DB_PREFIX').'xq_reply r ON t.id=r.tid')
			->join('left join '.C('DB_PREFIX').'users u ON r.userid=u.id')
			->where($where)
			->page($p,$pageNum)
			->group($group)
			->order('r.createtime desc')
			->select();
//		echo M()->_sql();
		return $list;
	}


	/**
	 * 发帖状态筛选
	 */
	function filterState($data)
	{
		if ($data)
		{
			if($data==1)//正常
			{
				$conditions['t.isexcellent'] = 2;
				$conditions['t.isred'] = 2;
				$conditions['t.shield'] = 2;
				$conditions['t.isclosed'] = 2;
			}
			else if($data==2)//加精
			{
				$conditions['t.isexcellent'] = 1;
				$conditions['t.isred'] = 2;
				$conditions['t.shield'] = 2;
				$conditions['t.isclosed'] = 2;
			}
			else if($data==3)//套红
			{
				$conditions['t.isexcellent'] = 2;
				$conditions['t.isred'] = 1;
				$conditions['t.shield'] = 2;
				$conditions['t.isclosed'] = 2;
			}
			else if($data==4)//加精套红
			{
				$conditions['t.isexcellent'] = 1;
				$conditions['t.isred'] = 1;
				$conditions['t.shield'] = 2;
				$conditions['t.isclosed'] = 2;
			}
			else if($data==5)//屏蔽
			{
				$conditions['t.shield'] = 1;
				$conditions['t.isexcellent'] = 2;
				$conditions['t.isred'] = 2;
			}
			else if($data==6)//已屏蔽（精，红）
			{
				$conditions['t.shield'] = 2;
				$conditions['t.isexcellent'] = 1;
				$conditions['t.isred'] = 1;
			}
			else if($data==7)//关闭
			{
				$conditions['t.isclosed'] = 1;
				$conditions['t.isexcellent'] = 2;
				$conditions['t.isred'] =2;
			}
			else if($data==8)//关闭（精，红）
			{
				$conditions['t.isclosed'] = 2;
				$conditions['t.isexcellent'] = 1;
				$conditions['t.isred'] =1;
			}
		}
		else
		{
			$this->error('非法操作');
		}
		return $conditions;
	}

	/**
	 * 主题帖/文章状态显示
	 */
	public function handle(&$data)
	{
		foreach ($data as $key => $value) {
			if($value["shield"]==1){
				$data[$key]["state"]='已屏蔽';
			}
			else if($value["isclosed"]==1){
				$data[$key]["state"]='已关闭';
			}
			else if($value["isred"]==1 && $value["isexcellent"]==1){
				$data[$key]["state"]='加精，套红';
			}
			else if($value["isexcellent"]==1){
				$data[$key]["state"]='加精';
			}
			else if ($value["isred"]==1) {
				$data[$key]["state"]='套红';
			}
			else{
				$data[$key]["state"]='正常';
			}
		}
	}

	/**
	 * 回帖
	 */
	public function reply()
	{
		$type = $_GET['type'];//1=>闲情  2=>文库
		$this->type = $type;
		if($type == 1)
		{
			$model = 'xq_topic';
			$conditions['t.type'] = array('neq',6);
		}
		else
		{
			$model = 'mf_topic';
			$conditions['t.type'] = array('neq',3);
		}

		if($_REQUEST['replyfilter'])
		{
			//筛选
			if($_REQUEST['states']!=0)
			{
				$conditions = $this->replyFilterState($_REQUEST['states']);
			}
			if($_REQUEST['attrs']!=0)
			{
				$conditions['t.attr'] = $_REQUEST['attrs'];
			}
			if ($_REQUEST['tags']!=0)
			{
				$conditions['t.type'] = $_REQUEST['tags'];
			}
			$this->attr=$_REQUEST['attrs'];
			$this->tags=$_REQUEST['tags'];
			$this->state=$_REQUEST['states'];
		}
		if($_REQUEST['searchInfo'])//搜索
		{
			$this->searchInfo = $_REQUEST['searchInfo'];//显示搜索框中
			$this->jsonSearchInfo = json_encode($_REQUEST['searchInfo'],JSON_UNESCAPED_UNICODE);//用于翻页
			$search['r.content'] = array('like','%'.htmlentities(trim($_REQUEST['searchInfo'])).'%');
			$search['r.nickname'] = array('like','%'.htmlentities(trim($_REQUEST['searchInfo'])).'%');
			$search['_logic'] = 'or';
			$conditions['_complex'] = $search;
		}
		$conditions['r.type'] = $type;
		$this->tagList = XqTag(0,$type);//筛选标签
		$field = 'r.id,r.tid,r.nickname,r.first,r.title,r.content,r.createip,r.createtime,r.position,r.shield,u.user_email,u.mobile,u.user_login,t.type,t.attr,r.type rtype,r.edittime';
//		$field = 'r.*,u.user_email,u.mobile,u.user_login,t.type atype,t.attr';
		$p = $_GET[p]?$_GET[p]:1;
		$result = $this->selectInfo($model,$conditions,$field,$group=null,$p,$pageNum=30);
		$pageModel = $model.' t';
		$show = getPage($pageModel,$conditions,$pageSize=30);//分页显示
		$this->page = $show->show();// 分页显示输出

		/*-----------后台回复详情跳转定位计算页数(优化)---------*/
		foreach($result as $key=>$value)
		{
			$data['r.type'] = $value['rtype'];
			$data['r.id'] = array('lt',$value['id']);
			$data['t.id'] = $value['tid'];
			$p = $this->Pagination($model,$data);
			$result[$key][p] = $p;
			$shortContent = handleReply($value['content']);
			$result[$key][newcontent] = $shortContent;
		}

		/*-----------后台回复详情跳转定位计算页数---------*/
		$this->showTagName($result,$type);
		$this->handle($result);

		$this->xq_reply=$result;
		$this->display(":Admin/reply");
	}

	/*
	 * 暂时文库回复编辑
	 */
		public function editReply()
		{
			if($_POST)
			{
				$type = $_POST['type'];
				$url = U('Admin/reply',array(type=>$type));
				$condition['content'] = $_POST['post']['post_content'];
				$condition['hiddentext'] = $_POST['post']['post_hiddentext'];
				$condition['id'] = $_POST['replyId'];
				$condition['edittime'] = time();
				$result = $this->updateReplyContent($condition);
				if($result)
				{
					$this->redirect($url);
				}
				else
				{
					$this->error('保存失败');
				}
			}
			else
			{
				$data['r.tid'] = $_GET['id'];//主题id
				$data['r.id'] = $_GET['rid'];//当前回复贴id
				if($_GET['type'] == 2)//文库
				{
					$model = 'mf_topic';
				}
				$field = 't.*,r.id rid,r.content,r.hiddentext,r.type rtype,u.user_email,u.mobile';
				$result = $this->selectInfo($model,$data,$field);
				$this->stateName = XqTag($result[0]['attr'],$_GET['type']);
				showCharacter($result,1);

//				if($result[0]['attr'] == 2)
//				{
//					$type = 2;
//				}
//				else
//				{
//					$type = 1;
//				}
				articleTag($result,2);
				$this->tagInfo = implode(',',$result[0]['tagInfo']);
				$this->result = $result;
			}
			$this->display('edit_reply');
		}

	/*
	 * 暂时更新文库内容
	 * @param  array  $condition
	 */
	public function updateReplyContent($condition)
	{
		$result = M('xq_reply')->data($condition)->save();
		if(false !== $result)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/*
	 * 暂时先循环给每个回复加上页数
	 */
	public function Pagination($model,$data)
	{
		$pageNum = 100;
		$replyNum = $this->selectInvitation2($model,$data);//查询回复
		$p = ceil(count($replyNum)/$pageNum);
		return $p;
	}


	function selectInvitation2($topic,$conditions,$order)
	{
		$model = $topic.' t';
		if($model == 'xq_topic t')
		{
			$str = 't.id,t.attr,t.isclosed,t.type,t.nickname,t.userid,t.title,t.createtime,t.replies,t.lasttime,t.isexcellent,t.isred,t.shield,t.sort,';
		}
		else if($model == 'mf_topic t')
		{
			$str = 't.id,t.userid,t.attr,t.isclosed,t.nickname,t.title,t.type,t.tagid,t.createtime,t.replies,t.lasttime,t.isexcellent,t.isred,t.shield,t.sort,t.authortype,t.authorname,t.cp,t.cptype,';
		}
		else if($model == 'xq_notice t')
		{
			$str = 't.id,t.isroll,t.sort,t.title,t.type,t.nickname,t.adminid,t.title,t.createtime,t.isclosed,t.replies,t.lasttime,';
		}

		$m = M("$model");
		$list = $m
			->field($str.'r.id rid,r.tid,r.nickname rnickname,r.content,r.type rtype,r.first,r.createip,r.createtime rcreatetime,r.position,r.shield rshield,r.isadmin')
			->join('left join '.C('DB_PREFIX').'xq_reply r on t.id = r.tid')
			->where($conditions)
			->order($order)
			->select();
		return $list;
	}

	/**
	 * 回帖状态筛选
	 */
	public function replyFilterState($data)
	{
		$conditions = array();
		if($data == 1)
		{
			$conditions['r.shield'] = 1;
		}
		if($data == 2)
		{
			$conditions['r.shield'] = 2;
		}
		return $conditions;
	}

	/**
	 * 显示标签名称
	 * @param  Array  $list  结果集
	 * @param  int    $type  1=>闲情  2=>文库
	 */
	function showTagName(&$list,$type)
	{
		if(!$list)
		{
			return false;
		}
		foreach ($list as $key=>$val)
		{
			$list[$key]['typename'] = XqTag($val['type'],$type);
			if($val['attr'] == 1)
			{
				$list[$key]['attrname'] = '大众';
			}
			if($val['attr'] == 2)
			{
				$list[$key]['attrname'] = '广付肉';
			}
			if($val['attr'] == 3)
			{
				$list[$key]['attrname'] = '百合';
			}
		}
		return $list;
	}


	/**
	 * 回帖详情页
	 */
	public function reply_detail()
	{
		$this->display(":Admin/reply_detail");
	}


/*---------------------------------------用户管理 start-------------------------------------------*/
	/**
	 * ip页面
	 */
	public function ip_manage()
	{
		$list = M('xq_ip i')->field('i.*,u.user_nicename')->join('left join '.C('DB_PREFIX').'users u ON i.b_adminid = u.id')->where('u.user_type=1')->order('begintime desc')->select();
		$this->list = $list;
		$this->display(':Admin/ip_manage');
	}

	/**
	 * 封禁Ip
	 */
	function banIp()
	{
		$model = M('xq_ip');
		$url = U('Admin/ip_manage');
		if($_GET['isRepeatIp'])
		{
			$count = $model->where('ip = '."'".trim($_GET['isRepeatIp'])."'")->find();
			$this->ajaxReturn($count);
			die;
		}
		$conditions['ip'] = trim($_POST['ip']);
		$ipInfo = $this->getIpInfo(trim($_POST['ip']));
		$conditions['area'] = $ipInfo['country'].'-'.$ipInfo['city'];
		$conditions['bannedtime'] = time() + ($_POST[banTime] * 86400);
		$conditions['begintime'] = time();
		$conditions['b_adminid'] = 1;//$_SESSION['ADMIN_ID'];
//			if($_POST['cate'])//但输入已经禁掉过的ip时，前台先给与提示，确认的话，根据ip在原来的时间上加上本次的时间 用cate这个参数作为标识
//			{
//				$addtime = $_POST[banTime] * 86400;
//				$result = $model->where('ip = '."'".trim($_POST['ip']."'"))->setInc('bannedtime',$addtime);
//			}
//			else
//			{
			$result = $model->data($conditions)->add();
//			}
		if($result)
		{
			$this->redirect($url);
		}
		else
		{
			$this->error('封禁失败');
		}

		$this->display(':Admin/ip_manage');
	}

	/**
	 * 批量解禁
	 */
	public function jieIp()
	{
		if($_POST['jieId'] == "")
		{
			$this->error('请选择要解禁的ip');
		}
		$url = U('Admin/ip_manage');
		$idArr = $_POST['jieId'];
		$condition['id'] = array('in',$idArr);
		$result = M('xq_ip')->where($condition)->delete();
		if($result)
		{
			$this->redirect($url);
		}
		else
		{
			$this->error('解禁失败');
		}
	}


	/**
	 * 用户管理
	 */
	function crime()
	{
		$this->display();
	}

	/**
	 * 通过IP获取对应城市信息(该功能基于淘宝第三方IP库接口)
	 * @param $ip IP地址,如果不填写，则为当前客户端IP
	 * @return  如果成功，则返回数组信息，否则返回false
	 */
	function getIpInfo($ip){
		if(empty($ip)) $ip=get_client_ip();  //get_client_ip()为tp自带函数，如没有，自己百度搜索。此处就不重复复制了
		$url='http://ip.taobao.com/service/getIpInfo.php?ip='.$ip;
		$result = file_get_contents($url);
		$result = json_decode($result,true);
		if($result['code']!==0 || !is_array($result['data'])) return false;
		return $result['data'];
	}











/*---------------------------------------用户管理 end-------------------------------------------*/



/*---------------------------------------公告 start-------------------------------------------*/
	/**
	 * 全站公告或全局公告  1=>全站 2=>全局
	 */
	public function global_notice()
	{
		$type = $_GET['type'];
		$this->type = $type;
		if($type == 1)
		{
			$conditions['r.type'] = 3;
		}
		else
		{
			$conditions['r.type'] = 4;
		}
		$conditions['t.type'] = $type;
		$model = 'xq_notice';
		$group = 't.id';//去重字段
		$field = $field = 't.*,r.content,r.type rtype';
//		$result = $this->selectNotice($model,$conditions,$field);
		$result = $this->selectInfo($model,$conditions,$field,$group);

//		$result = M('xq_notice')->where($conditions)->select();
		$this->xq_notice=$result;
		$this->display(":Admin/global_notice");
	}

	/**
	 * 板块公告
	 */
	public function part_notice()
	{
		$sql="SELECT t.id,attr,nickname,t.type,title,createtime,replies,lasttime,isclosed,sort from cmf_xq_topic t LEFT JOIN cmf_users b ON t.userid=b.id WHERE t.type=6 UNION SELECT t.id,attr,nickname,t.type,title,createtime,replies,lasttime,isclosed,sort FROM cmf_mf_topic t LEFT JOIN cmf_users b ON t.userid=b.id WHERE t.type=3";
//
		$M=M("xq_topic");
		$r=$M->query($sql);

		$this->handle_notice($r);
		$this->part_notice=$r;
		$this->display(":Admin/part_notice");
	}

	/**
	 * 公告状态更改
	 */
	public function AjaxNoticeState()
	{
		//查出公告限制数量
		$limitNum = M('set')->find();
		$conditions['id'] = $_POST['noticeId'];
		if($_POST['noticeType'] == 3)//文库
		{
			$str = 'id,sort,isclosed';
			$model = 'mf_topic';
			$limitNum = $limitNum['top_notice_num'];//板块第公告（文库）限制数
		}
		elseif($_POST['noticeType'] == 6)//闲情
		{
			$str = 'id,sort,isclosed';
			$model = 'xq_topic';
			$limitNum = $limitNum['top_notice_num'];//板块第公告（闲情）限制数
		}
		else
		{
			$str = 'id,sort,isroll,isclosed';
			$conditions['type'] = $_POST['noticeType'];//1=>全站  2=>全局(板块不传此值)
			$model = "xq_notice";
		}
		if($_POST['noticeCate'] == 1)//置顶/滚动
		{
			if($_POST['noticeType'] == '1')//全站公告
			{
				$limitNum = $limitNum['roll_notice_num'];//板块第公告（全站）限制数
				if($_POST['noticeState'] == 1)
				{
					$field = 'isroll';
					$state = '2';
				}
				else
				{
					$field = 'isroll';
					$state = '1';
					//搜索当前表中有多少条已经滚动的公告
					$status['isroll'] = 1;
					$status['type'] = 1;
					$rollNum = $this->noticeLimitNum($model,$field,$status);
					if($rollNum >= $limitNum)
					{
						$selectInfo['limit'] = true;
						$this->ajaxReturn($selectInfo);
						die;
					}
				}
			}
			else
			{
				if($_POST['noticeState'] == 2)//全局公告和版块都是置顶所以放在一起
				{
					$field = 'sort';
					$state = time();
					$status['sort'] = array('gt',0);
					$status['type'] = $_POST['noticeType'];
					if($_POST['noticeAttr'])
					{
						$status['attr'] = $_POST['noticeAttr'];
					}

					$rollNum = $this->noticeLimitNum($model,$field,$status);
					if($_POST['noticeType'] == '2')//全局公告
					{
						$limitNum = $limitNum['global_notice_num'];//全局公告限制数
					}
//					print_r($rollNum);die;

					if($rollNum >= $limitNum)
					{
						$selectInfo['limit'] = true;
						$this->ajaxReturn($selectInfo);
						die;
					}
				}
				else
				{
					$field = 'sort';
					$state = '0';
				}
			}
		}
		else
		{
			$field = 'isclosed';
			if($_POST['noticeState'] == 1)
			{
				$state = 2;
			}
			else
			{
				$state = 1;
			}
		}


		$result = $this->changeNoticeField($model,$conditions,$state,$field);
		if($result)
		{
			$selectInfo = M($model)->field($str)->where($conditions)->find();
			if($selectInfo['sort'] != '0')
			{
				$selectInfo['sort'] = date('Y-m-d H:i:s',$selectInfo['sort']);
			}
			$this->ajaxReturn($selectInfo);
		}
	}

	/**
	 * 状态更改
	 * @param  table   $model       数据表
	 * @param  array   $conditions  条件
	 * @param  string  $state       数据库中要更改的值
	 * @param  string  $field       需要更改的字段
	 */
	public function changeNoticeField($model,$conditions,$state,$field)
	{
		$result = M($model)->where($conditions)->setField($field,$state);
		return $result;
	}

	/*
	 * 公告数目限制
	 * @param  table   $model
	 * @param  string  $field
	 * @param  string  $state
	 * return  已滚动/已置顶公告数量
	 */
	//公告数目限制
	public function noticeLimitNum($model,$field,$state)
	{
		$num = M($model)->field($field)->where($state)->count();
//		echo M()->_sql();
		return $num;
	}

	/**
	 * 板块公告板块字段显示
	 * @param  [type] &$data [description]
	 */
	public function handle_notice(&$data)
	{
		foreach ($data as $key => $value) {
			if($value["type"]==6){
				$data[$key]["module"]='闲情';
			}
			else if($value["type"]==3){
				$data[$key]["module"]='文库';
			}
		}
	}

	/**
	 * 板块公告筛选
	 */
	public function filtering_notice()
	{
		$sql1="SELECT t.id,attr,nickname,t.type,title,createtime,replies,sort,lasttime from cmf_xq_topic t LEFT JOIN cmf_users b ON t.userid=b.id WHERE t.type=6";
		$sql2="SELECT t.id,attr,nickname,t.type,title,createtime,replies,sort,lasttime FROM cmf_mf_topic t LEFT JOIN cmf_users b ON t.userid=b.id WHERE t.type=3";
		if($_POST['searchInfo'])//搜索
		{
			$this->searchInfo = $_POST['searchInfo'];
			$search = " AND ( t.nickname LIKE '%".htmlentities(trim($_POST['searchInfo']))."%' OR t.title LIKE '%".trim(($_POST['searchInfo']))."%' )";
		}

		if($_POST['module']==1)
		{
			if ($_POST['attr'])
			{
				$sql1.=" and attr=".$_POST['attr'];
			}
			if ($_POST['isclosed'])
			{
				$sql1.=" and isclosed=".$_POST['isclosed'];
			}
			$sql1.=$search;
			$M=M("xq_topic");
			$r=$M->query($sql1);
		}
		else if ($_POST['module']==2)
		{
			if ($_POST['attr'])
			{
				$sql2.=" and attr=".$_POST['attr'];
			}
			if ($_POST['isclosed'])
			{
				$sql2.=" and isclosed=".$_POST['isclosed'];
			}
			$sql2.=$search;
			$M=M("mf_topic");
			$r=$M->query($sql2);
		}
		else
		{
			if ($_POST['attr'])
			{
				$sql1.=" and attr=".$_POST['attr'];
				$sql2.=" and attr=".$_POST['attr'];
			}
			if ($_POST['isclosed'])
			{
				$sql1.=" and isclosed=".$_POST['isclosed'];
				$sql2.=" and isclosed=".$_POST['isclosed'];
			}
			$sql1.=$search;
			$sql2.=$search;
			$M=M("xq_topic");
			$sql=$sql1." UNION ".$sql2;
			$r=$M->query($sql);
		}
		$this->handle_notice($r);
		$this->part_notice=$r;
		$this->attr=$_POST['attr'];
		$this->module=$_POST['module'];
		$this->isclosed=$_POST['isclosed'];
		$this->display(":Admin/part_notice");
	}

	/**
	 * 发布公告
	 */
	public function notice_post()
	{
		if($_POST)
		{
			$conditions = array();//存入属于自己的主表
			$conditions2 = array();//存入回复表

			//公告类型  1=>全站公告  2=>全局公告  3=>板块公告
			if($_POST['radio'] == 3 || $_POST['radio'] == 6)
			{
				$url = U('Admin/part_notice');
				if($_POST['module'] == 1)
				{
					$model = 'xq_topic';
					$conditions['type'] = 6;//闲情
				}
				if($_POST['module'] == 2)
				{
					$model = 'mf_topic';
					$conditions['type'] = 3;//文库
				}

				$conditions['attr'] = $_POST['attr']?$_POST['attr']:null;
				$conditions['shield'] = 2;
				$conditions2['type'] = $_POST['module'];
			}
			else
			{
				//发布全局公告或全站公告
				$model = 'xq_notice';
				$conditions['isroll'] = 2;
				if($_POST['radio'] == 1)
				{
					$conditions['type'] = 1;//公告表（全站）
					$conditions2['type'] = 3;//回复表（全站）
					$url = U('Admin/global_notice',array(type=>1));
				}
				if($_POST['radio'] == 2)
				{
					$conditions['type'] = 2;//公告表（全局）
					$conditions2['type'] = 4;//回复表（全局）
					$url = U('Admin/global_notice',array(type=>2));
				}
			}
			$conditions['adminid'] = $_SESSION['ADMIN_ID'];
			$conditions2['replies'] = $conditions['replies'] = 0;
			$conditions2['headimg'] = $_POST['systemHead'];
			$conditions2['first'] = 1;
			$conditions2['position'] = 0;
			$conditions2['shield'] = 2;
			$conditions['nickname'] = $conditions2['nickname'] = $_SESSION['name'];
			$conditions2['createtime'] = $conditions['createtime'] = time();
			$conditions['lasttime'] = $conditions['createtime'];
			$conditions['isclosed'] = $_POST['reply']?$_POST['reply']:2;
			$conditions['userid'] = $conditions2['userid'] = $_SESSION['ADMIN_ID'];
			$conditions2['title'] = $conditions['title'] = $_POST['tittle']?$_POST['tittle']:null;
			$conditions2['content'] = $_POST['post']['post_content'];

			//插入公告（包括回复表和发表帖子一样）,有id更新没id添加
			if($_POST['id'])
			{
				//更新
				$conditions['id'] = $_POST['id'];
			}
			//插入
			$this->insertBoardNotice($model,$conditions,$conditions2,$_POST['id'],$url);
		}
		else
		{
			$this->display(':Admin/notice_post');
		}
	}

	/**
	 * 插入/更新板块公告
	 * @param  table   $model        模型
	 * @param  array   $conditions   插入/更新信息
	 * @param  array   $conditions2  插入/更新回复表信息
	 * @param  int     $id           编辑
	 * @param  str     $url  		 成功之后的跳转地址
	 */
	public function insertBoardNotice($model,$conditions,$conditions2,$id,$url)
	{

		$topic = M($model);
		$reply = M('xq_reply');
		$topic->startTrans();
		$through = true;
		if($id)
		{
			$conditions['id'] = $id;
			$topicId = $topic->field('replies,lasttime',true)->data($conditions)->save();//更新过滤掉更新回复数和最新回复时间等字段（存在要更新的数据已经有回复数的可能）
			if($topicId<0)//内容相同影响行数为0
			{
				$through = false;
			}
			if($through)
			{
				$replyId = $reply->where('tid = '.$id)->data($conditions2)->save();

				if(!$replyId)
				{
					$through = false;
				}
			}
		}
		else
		{
			$topicId = $topic->data($conditions)->add();
			if(!$topicId)
			{
				$through = false;
			}
			if($through)
			{
				$conditions2['tid'] = $topicId;
				$replyId = $reply->data($conditions2)->add();
				if(!$replyId)
				{
					$through = false;
				}
			}
		}
		if($through)
		{
			$topic->commit();
			$this->redirect($url);
			//加一个跳转页面，跳回到板块公告
		}
		else
		{
			$topic->rollback();
			$this->error('发布失败');
		}
	}

	/**
	 * 公告编辑
	 */
	public function editNotice()
	{
		$this->type = $_GET['type'];//1=>全站  2=>全局  3,6=>板块
		$this->id = $_GET['id'];
		$conditions['r.first'] = 1;
		$conditions['t.id'] = $_GET['id'];
		$conditions['t.type'] = $_GET['type'];
		$field = 't.*,r.content,r.type rtype';

		if($_GET['type'] == 1 || $_GET['type'] == 2)
		{
			$conditions['r.type'] = ($_GET['type'] == 1)?3:4;
			$model = 'xq_notice';
		}
		else
		{
			if($_GET['type'] == 3)
			{
				$conditions['r.type'] = 2;//回复表中文库
				$model = 'mf_topic';
			}
			else
			{
				$conditions['r.type'] = 1;//回复表中闲情
				$model = 'xq_topic';
			}
		}
		//查询当前id记录
//		$result = $this->selectNotice($model,$conditions,$field,$_GET['type']);
		$result = $this->selectInfo($model,$conditions,$field);
		$this->list = $result;
		$this->display('notice_post');
	}


	/**
	 * 公告删除  (全站全局公告的回复全都放在reply中，删除父级公告，所有的子级回复全都删除)
	 */
	public function ajaxDel()
	{
		$id = $_REQUEST['id'];
		$type = $_REQUEST['type'];
		$condition2['tid'] = $condition['id'] = array('in',$id);
		if($_REQUEST['cate'])
		{
			//删除帖子
			if($type == 2)
			{
				$model = 'mf_topic';
				$condition2['type'] = 2;//回复表中文库的字段类型
			}
			else
			{
				$model = 'xq_topic';
				$condition2['type'] = 1;//回复表中闲情的字段类型
			}
		}
		else
		{
			//删除公告
			$condition['type'] = $type;
			if($type == 1)
			{
				$model = 'xq_notice';
				$condition2['type'] = 3;//回复表中全站的字段类型
			}
			elseif($type == 2)
			{
				$model = 'xq_notice';
				$condition2['type'] = 4;//回复表中全局的字段类型
			}
			elseif($type == 6)
			{
				$model = 'xq_topic';
				$condition2['type'] = 1;//板块公告（闲情）
			}
			elseif($type == 3)
			{
				$model = 'mf_topic';
				$condition2['type'] = 2;//板块公告（文库）
			}
		}
		$result = $this->del($model,$condition,$condition2);
		if($result)
		{
			if($_POST['idBatch'] == 1)//不做删除个数区分  只做功能点区分
			{
				echo 2;//批量删除  刷新页面
			}
			else
			{
				echo 1;//单点删除
			}
		}
	}

	/**
	 * 删除帖子或公告
	 * @param  $model       数据表
	 * @param  $condition   条件
	 * @param  $condition2  回复表条件
	 */
	public function del($model,$condition,$condition2)
	{
		M($model)->startTrans();
		$through = true;
		$result = M($model)
			->where($condition)
			->delete();
		if($result)
		{
			$final = M('xq_reply')->where($condition2)->delete();
			if(!$final)
			{
				$through = false;
			}
		}
		if($through)
		{
			M($model)->commit();
		}
		else
		{
			M($model)->rollback();
		}
		return $through;
	}

	//删除当前回复
	public function ajaxReplyDel()
	{
		//一楼的没有删除的按钮
		$condition['id'] = array('in',($_GET['id']));
		$delResult = M('xq_reply')->where($condition)->delete();
		if($delResult) 
		{
			if($_GET['idBatch'] == 1)//不做删除个数区分  只做功能点区分
			{
				echo 2;//批量删除  刷新页面
			}
			else
			{
				echo 1;//单点删除
			}
		}
	}

	/**
	 * 公告设置
	 */
	public function noticeSet()
	{
		$M=M('set');
		if($_POST)
		{
			$conditions['global_notice_num'] = $_POST['global']?$_POST['global']:null;//全局公告显示
			$conditions['roll_notice_num'] = $_POST['roll']?$_POST['roll']:null;//滚动公告显示
			$conditions['top_notice_num'] = $_POST['up']?$_POST['up']:null;//置顶公告显示
			if($_POST['id'])
			{
				$result = $M->where('id='.$_POST['id'])->data($conditions)->save();
				if($result == 0 || $result > 0)
				{
					$this->redirect(U('admin/noticeSet'));
				}
			}
			else
			{
				$result = $M->data($conditions)->add();
				if($result)
				{
					$this->redirect(U('admin/noticeSet'));
				}
			}
		}
		else
		{
			//查询各个公告已存在的数量，前台保存时不能少于当前已存在的数量，否则提示
			$num = array();
			$globalNoticeNum = M('xq_notice')->where('sort>0 and type=2')->count();//查询全局公告已置顶的数量
			$rollNoticeNum = M('xq_notice')->where('isroll=1 and type=1')->count();//查询全站公告已滚动的数量
			$sql = 'select count(id) as count from '.C('DB_PREFIX').'xq_topic where sort>0 AND type=6 GROUP BY attr';//查询闲情中不同属性的公告已置顶的数量
			$xqUpNoticeNum = M()->query($sql);
			$xqMaxNum = $this->maxNum($xqUpNoticeNum[0]['count'],$xqUpNoticeNum[1]['count'],$xqUpNoticeNum[2]['count']);
			$sql2 = 'select count(id) as count from '.C('DB_PREFIX').'mf_topic where sort>0 AND type=3 GROUP BY attr';//查询文库中不同属性的公告已置顶的数量
			$libUpNoticeNum = M()->query($sql2);
			$libMaxNum = $this->maxNum($libUpNoticeNum[0]['count'],$libUpNoticeNum[1]['count'],$libUpNoticeNum[2]['count']);
			$zhiDing = max($xqMaxNum,$libMaxNum);//闲情和文库取最大值作为前台置顶公告的最小数

			$num['global'] = $globalNoticeNum;
			$num['roll'] = $rollNoticeNum;
			$num['zhiding'] = $zhiDing;

			$result = $M->find();//查询set表中的已设置好的板块的限制数
			$this->limitNum = json_encode($num);//传到前台用于判断
			$this->list = $result;
		}
		$this->display(":Admin/notice_set");
	}

	//三个数取最大（php内置函数max）
	function maxNum($a,$b,$c)
	{
		return $a>$b?($a>$c?$a:$c):($b>$c?$b:$c);
	}

	/**
	 * 公告回复详情
	 */
	public function noticeReply()
	{
		//1=>板块公告（闲情）  2=>板块公告（文库）  3=>全站   4=>全局
		$type = $_GET['type'];
		$this->type = $type;
		if($type < 3)
		{
			$model = 'xq_notice';
			$conditions['r.type'] = ($_GET['type'] == 2)?4:3;
			$field='r.id,r.tid,r.nickname,r.first,r.title,r.content,r.createip,r.createtime,r.position,r.shield,u.user_email,u.mobile,u.user_login,t.type,r.type rtype';
		}
		else
		{
			if($type == 3)
			{
				$model = 'mf_topic';
			}
			else
			{
				$model = 'xq_topic';
			}
			$conditions['r.type'] = ($_GET['type'] == 6)?1:2;
			$this->attr = $_GET['attr'];//属性
			$field = 'r.id,r.tid,r.nickname,r.first,r.title,r.content,r.createip,r.createtime,r.position,r.shield,u.user_email,u.mobile,u.user_login,t.type,t.attr,r.type rtype';
		}
		$conditions['r.tid'] = $_GET['id'];

//		$field = 'r.*,u.user_email,u.mobile,u.user_login,t.type atype,t.attr';
		$p = $_GET[p]?$_GET[p]:1;
		$result = $this->selectInfo($model,$conditions,$field,$group=null,$p,$pageNum=30);
		$conditions['r.first'] = 1;
		$head = $this->selectInfo($model,$conditions,$field,$group=null,$p=null,$pageNum=null);

		$pageModel = $model.' t';
		$show = getPage($pageModel,$conditions,$pageSize=30);//分页显示
		$this->page = $show->show();// 分页显示输出

		/*-----------后台回复详情跳转定位计算页数(优化)---------*/
		foreach($result as $key=>$value)
		{
			$data['r.type'] = $value['rtype'];
			$data['r.id'] = array('lt',$value['id']);
			$data['t.id'] = $value['tid'];
			$p = $this->Pagination($model,$data);
			$result[$key][p] = $p;
			$shortContent = handleReply($value['content']);
			$result[$key][newcontent] = $shortContent;
		}
		/*-----------后台回复详情跳转定位计算页数---------*/
		$this->showTagName($result,$type);
		$this->handle($result);
		$this->noticeHead = $head;
		$this->notice_reply=$result;
		$this->display(":Admin/notice_reply");
	}
/*---------------------------------------公告 end-------------------------------------------*/


}


