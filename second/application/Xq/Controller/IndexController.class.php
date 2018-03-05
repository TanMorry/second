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

class IndexController extends HomebaseController
{
    /**
     * 闲情首页 默认腐向
     */
    public function index()
    {
        $this->login = $this->isLogin();
//        $topic = M('xq_topic t');
        $topic = 'xq_topic t';
        $tagContent = xqTag(0,1);//显示全部标签
        //tag标签筛选
        if($_POST['tagId'])//点击筛选
        {
            $tagId = $_POST['tagId'];
            $this->tagArr = $tagId;
            $this->assign('jsonTagArr',json_encode($tagId));
        }
        if($_GET['tag'])//分页跳转带参数
        {
            $tag = json_decode($_GET['tag']);
            $this->tagArr = $tag;
            $this->assign('jsonTagArr',$_GET['tag']);
        }
        $this->searchField = $this->searchField();
        $attr = $_REQUEST['attr']?$_REQUEST['attr']:2;// 1=>大众  2=>广付肉  3=>百合
        $status = $_REQUEST['status']?$_REQUEST['status']:1;
        $this->status = $status;
        $conditions = $this->handle($tagId,$attr,$status);//查询条件
        $conditions['r.type'] = 1;
        $p = $_GET['p']?$_GET['p']:1;
        $conditions['t.shield'] = 2;
        $conditions['r.first'] = 1;

        $order = 't.sort desc,t.lasttime desc';//排序方式
        $list = $this->selectInvitation($topic,$conditions,$p,$pageSize=30,$order,$group='t.id');//查询的帖子按时间排序 倒序
        $list2 = $this->showTag($list,1);
        $show = getPage($topic,$conditions,$pageSize=30);//分页显示
        $result = addPhoto($list2);//取出图片地址

        $this->page = $show->show();// 分页显示输出
        $this->lastInv = $this->lastUpdate($attr);//文库最新更新
        //全局公告
        $this->notice = $this->globalnotice();
        //滚动公告
        $this->rollNotice = $this->rollnotice();
        $this->attr = $attr;
        $this->status = $status;
        $this->xqTag = $tagContent;//闲情首页包含的标签结果集
        $this->AllTopic = $result;//闲情首页的所有帖子的结果集
        $this->display(":trleisure_wdr");
    }

    /**
     * 腐向文库
     */
    public function trleisure_library()
    {
        $this->login = $this->isLogin();
//        $topic = M('mf_topic t');
        $topic = 'mf_topic t';
        $tagContent = xqTag(0,2);//显示全部标签
        //tag标签筛选
        if($_POST['tagId'])
        {
            $tagId = $_POST['tagId'];
            $this->libTagArr = $tagId;
            $this->assign('jsonTagArr',json_encode($tagId));
        }
        if($_GET['tag'])//分页跳转带参数
        {
            $tag = json_decode($_GET['tag']);
            $this->libTagArr = $tag;
            $this->assign('jsonTagArr',$_GET['tag']);
        }
        $this->searchField = $this->searchField(1);
        $attr = $_GET['attr'];// 1=>大众  2=>广付肉  3=>百合
        $status = $_REQUEST['status']?$_REQUEST['status']:1;
        $this->status = $status;
        $conditions = $this->handle($tagId,$attr,$status);//查询条件
        $conditions['r.type'] = 2;
        $p = $_GET['p']?$_GET['p']:1;
        $conditions['t.shield'] = 2;
        $conditions['r.first'] = 1;
        $order = 't.sort desc,t.lasttime desc';//排序方式；
        $list = $this->selectInvitation($topic,$conditions,$p,$pageSize=30,$order,$group='t.id');//查询的帖子按时间排序 倒序
        $list2 = $this->showTag($list,2);
        showCharacter($list2,2);

//        if($attr == 1 || $attr == 3)
//        {
//            $type = 1;
//        }
//        else
//        {
//            $type = 2;
//        }
        articleTag($list2,1);
        $show = getPage($topic,$conditions,$pageSize=30);//分页显示
        $this->page = $show->show();// 分页显示输出
        //全局公告
        $this->notice = $this->globalnotice();
        //滚动公告
        $this->rollNotice = $this->rollnotice();
        $this->attr = $attr;
        $this->status = $status;
        $this->libraryTag = $tagContent;//文库页包含的标签结果集
        $this->AllArticle = $list2;//文库包含的所有文章结果集
        $this->display(":trleisure_library");
    }

    /**
     * 当前用户所发过的帖子/文章（区分）
     */
    public function selfInvitation()
    {
        if (!isset($_SESSION['user'])) {
            $this->error('请先登录',U('index/index'));
            return false;
        }
        if(!$_GET['lib'])
        {
//            $topic = M('xq_topic t');
            $topic = 'xq_topic t';
            $type = 1;
        }
        else
        {
            $this->lib = $_GET['lib'];//用来判断是否从文库传来的
//            $topic = M('mf_topic t');
            $topic = 'mf_topic t';
            $type = 2;
        }
        $conditions = array();
        $userId = $_SESSION['user']['id'];
        $userId?$conditions['r.userid'] = $userId:"";
        $p = $_GET['p']?$_GET['p']:1;
        $conditions['t.shield'] = 2;
        $conditions['r.first'] = 1;
        $conditions['r.type'] = $type;
        $order = 't.sort desc,t.lasttime desc';//排序方式；
        $list = $this->selectInvitation($topic,$conditions,$p,$pageSize=30,$order);//查询的帖子按时间排序 倒序
        if($type == 2)
        {
            showCharacter($list,2);
            articleTag($list,1);
        }
        $result = $this->showTag($list,$type);
        $show = getPage($topic,$conditions,$pageSize=30);//分页显示
        $this->page = $show->show();// 分页显示输出
        $this->AllTopic = $result;
        $this->display(':trleisure_myInvitation');
    }


    /**
     * 查询帖子
     * @param  tale    $model       模型
     * @param  array   $conditions  查询条件
     * @param  int     $p           页数
     * @param  int     $pageNum     显示条数
     * @param  string  $order       排序方式
     */
    public function selectInvitation($model,$conditions,$p,$pageNum,$order,$group)
    {
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


        $list = M("$model")
            ->field($str.'r.id rid,r.tid,r.nickname rnickname,r.content,r.type rtype,r.first,r.createip,r.createtime rcreatetime,r.position,r.shield rshield,r.isadmin,r.hiddentext,r.headimg')
            ->join('left join '.C('DB_PREFIX').'xq_reply r on t.id = r.tid')
            ->where($conditions)
            ->group($group)
            ->order($order)
            ->page($p,$pageNum)
            ->select();
//        echo M()->_sql();
        return $list;
    }

    /**
     * 跳转到发表新贴/发表新文章页面
     */
    public function newInvitation()
    {
        if(!$_GET['lib'])
        {
            $tagArr = XqTag(0,1);//标签
            $this->attr = $_GET['attr'];// 1=>大众   2=>广付肉   3=>百合
            array_pop($tagArr);
            $this->tagArr = $tagArr;
            $new = 'trleisure_newInvitation';
        }
        else
        {
            $this->tagArr = array_pop(XqTag(0,2));//标签
            $this->attr = $_GET['attr'];// 1=>大众   2=>广付肉   3=>百合
            $this->lib = $_GET['lib'];
            $new = 'trleisure_newArticle';
        }
        $this->title_tag = $this->bubble($_GET['attr']);//标题下面的标签(文库)
        $this->json_tag = json_encode($this->bubble($_GET['attr']),JSON_UNESCAPED_UNICODE);
        $this->login = $this->isLogin();
        $this->nickname = $_SESSION['user']['user_nicename'];
        $this->Ip = get_client_ip();
        $this->display(':'.$new.'');
    }

    /**
     * 查询表中已存在的文章小标签
     * @param $type   1,3=> 大众&百合  2=>腐向
     */
    public function bubble($type,$id)
    {
        $model = M('mf_tag');
        $conditions = "";
        if($type == 1 || $type == 3)
        {
            $conditions .= 'type=1';
        }
        else
        {
            $conditions .= 'type=2';
        }
        if($id)
        {
            $conditions .= ' or id in('.$id.')';
        }
        $list = $model->field('id,name')->where($conditions)->select();
        return $list;
    }

    /**
     * 搜索
     */
    public function xqTrleisureSearch()
    {
        $libraryTag = XqTag(0,2);//文库获取固定标签
        array_pop($libraryTag);
        $this->libTag = $libraryTag;
        $this->lib = $_REQUEST['lib'];
        $this->titleType = $_REQUEST['titleType'];
        $this->info = $_REQUEST['searchInfo'];
        $this->titleFiled = $this->searchField($_REQUEST['lib']);//搜索字段
        $conditions = $this->distribution($_REQUEST['lib'],$_REQUEST['titleType'],trim($_REQUEST['searchInfo']));//分配字段搜索
        $p = $_GET['p']?$_GET['p']:1;
        $order = 't.sort desc,t.lasttime desc';//排序方式；
        $conditions['t.shield'] = 2;
        $conditions['t.attr'] = $_REQUEST['attr'];

        if(!$_REQUEST['lib'])
        {
//            $topic = M('xq_topic t');
            $topic = 'xq_topic t';
            $conditions['r.type'] = $type = 1;//闲情
            $list = $this->selectInvitation($topic,$conditions,$p,$pageNum=30,$order,$group='t.id');
        }
        else
        {
            $topic = 'mf_topic t';
            $conditions['r.type'] = $type = 2;//文库
            if($_REQUEST['libSearch'])
            {
//                $this->libSearch = $_REQUEST['libSearch'];//分页需要带的参数
                if($_REQUEST['tagsId'])
                {
                    $this->tagsArr = $_REQUEST['tagsId'];
                    $this->jsontagsArr = json_encode($_REQUEST['tagsId']);
                    $tagsId = implode(',',$_REQUEST['tagsId']);
                    $conditions['m.id'] = array('in',$tagsId);
                }
                else
                {
                    $this->tagsArr = 0;
                }
                if($_REQUEST['fixedTag'])
                {
                    $this->fixTagArr = $_REQUEST['fixedTag'];
                    $this->jsonfixTagArr = json_encode($_REQUEST['fixedTag']);
                    $fixedTag = implode(',',$_REQUEST['fixedTag']);
                    $conditions['t.type'] = array('in',$fixedTag);
                }
            }
            if($_GET['fixTagStr'])//分页跳转带参数
            {
                $fixTagStr = json_decode($_GET['fixTagStr']);
                $this->fixTagArr = $fixTagStr;
                $this->assign('jsonfixTagArr',$_GET['fixTagStr']);
                $conditions['t.type'] = array('in',implode(',',$fixTagStr));
            }
            if($_GET['tagsStr'])//分页参数
            {
                $tagsStr = json_decode($_GET['tagsStr']);
                $this->tagsArr = $tagsStr;
                $this->assign('jsontagsArr',$_GET['tagsStr']);
                $conditions['m.id'] = array('in',implode(',',$tagsStr));
            }
            $list = $this->librarySearch($conditions,$p,$pageNum=30,$order);
            showCharacter($list,2);
            articleTag($list,1);
        }

        if($_REQUEST['attr'])//文章属性
        {
            $this->attr = $_REQUEST['attr'];
            $this->bubble = $this->bubble($_REQUEST['attr']);//文章小标签
        }
        $list2 = $this->showTag($list,$type);
        if($_REQUEST['lib'])
        {
            $show = getPage($topic,$conditions,$pageSize=30,$type=true);//type文库搜索（要关联标签表）
        }
        else
        {
            $show = getPage($topic,$conditions,$pageSize=30);//分页显示
        }
        //全局公告
        $this->notice = $this->globalnotice();
        //滚动公告
        $this->rollNotice = $this->rollnotice();
        $this->page = $show->show();// 分页显示输出
        $this->countList = count($list);
//        echo "<pre>";
//        print_r($list2);die;
        $this->list = $list2;
        $this->display(":trleisure_search");
    }

    /**
     * 文库搜索页单独搜索
     * @param  $conditions  条件
     * @param  $p           页数
     * @param  $pageNum     条数
     * @param  $order       排序方式
     */
    public function librarySearch($conditions,$p,$pageNum,$order){

        $list = M('mf_topic t')
            ->field('t.id,t.userid,t.attr,t.nickname,t.title,t.type,t.tagid,t.createtime,t.replies,t.lasttime,t.isexcellent,t.isred,t.shield,t.sort,t.authortype,t.authorname,t.cp,t.cptype,r.id rid,r.tid,r.nickname rnickname,r.content,r.type rtype,r.first,r.createip,r.createtime rcreatetime,r.position,r.shield rshield')
            ->join('left join '.C('DB_PREFIX').'xq_reply r on t.id = r.tid')
            ->join('left join '.C('DB_PREFIX').'mf_tag m on FIND_IN_SET(m.id,t.tagid)')
            ->where($conditions)
            ->group('t.id')
            ->order($order)
            ->page($p,$pageNum)
            ->select();
        return $list;
    }

    /**
     * 分配查询字段
     * @param $lib  存在显示文库搜索字段，反之闲情搜索字段
     * @param $titleType  查询类型
     * @param $val  查询信息
     */
    function distribution($lib,$titleType,$val)
    {
        $conditions = array();
        if(!$lib)
        {
            if($titleType == 1)
            {
                $conditions['t.title'] = array('like','%'.$val.'%');//帖子主题
            }
            elseif($titleType == 2)
            {
                $conditions['r.content'] = array('like','%'.$val.'%');//主题帖内容
                $conditions['r.first'] = 1;
            }
            elseif($titleType == 3)
            {
                $conditions['t.nickname'] = array('like','%'.$val.'%');//主题帖发帖人
            }
            elseif($titleType == 4)
            {
                $conditions['r.content'] = array('like','%'.$val.'%');//跟帖内容
                $conditions['r.first'] = 2;
            }
            elseif($titleType == 5)
            {
                $conditions['r.nickname'] = array('like','%'.$val.'%');//跟帖发帖人
                $conditions['r.first'] = 2;
            }
        }
        else
        {
            $conditions['r.first'] = 1;
            if($titleType == 1)
            {
                $conditions['t.title'] = array('like','%'.$val.'%');//文章名
            }
            elseif($titleType == 2)
            {
                $conditions['r.content'] = array('like','%'.$val.'%');//文章内容
            }
            elseif($titleType == 3)
            {
                $conditions['t.authorname'] = array('like','%'.$val.'%');//原作名称
            }
            elseif($titleType == 4)
            {
                $conditions['t.cp'] = array('like','%'.$val.'%');//角色
            }
            elseif($titleType == 5)
            {
                $conditions['t.nickname'] = array('like','%'.$val.'%');//作者
            }
        }
        return $conditions;
    }

    /**
     * 帖子/文章详情
     */
    public function topicDetail()
    {
        if($_GET['ttype'])//后台板块公告详情
        {
            if($_GET['ttype'] == 3)//文库公告（板块公告）
            {
                $topic = 'mf_topic t';
                $conditions['r.type'] = $data['r.type'] = 2;
                $this->type = 2;//前台区分文库还是闲情 全站还是全局
            }
            if($_GET['ttype'] == 6)//闲情公告（板块公告）
            {
                $topic = 'xq_topic t';
                $conditions['r.type'] = $data['r.type'] = 1;
                $this->type = 1;//前台区分文库还是闲情 全站还是全局
            }
            $conditions['r.tid'] = $data['r.tid'] = $_GET['id'];

        }
        else
        {
            //闲情或文库
            if($_GET['type'] == 1)
            {
                $conditions['r.tid'] = $data['r.tid'] = $_GET['id'];
                $topic = 'xq_topic t';
                if(!$_GET['isAdmin'])
                {
                    $conditions['t.shield'] = 2;//后台发表的时候看全不得包括屏蔽的
                }
            }
            if($_GET['type'] == 2)
            {
                $conditions['r.tid'] = $data['r.tid'] = $_GET['id'];
                $topic = 'mf_topic t';
                if(!$_GET['isAdmin'])
                {
                    $conditions['t.shield'] = 2;
                }
            }
            if($_GET['type'] == 3 || $_GET['type'] == 4)//公告详情
            {
                $topic = 'xq_notice t';
                if($_GET['isAdmin'])
                {
                    $id = $_GET['id'];
                    $conditions['r.tid'] = $data['r.tid'] = $_GET['id'];
                }
                else
                {
                    $id = $_GET['noticeId'];//用于前台判断显示标签
                    $conditions['r.tid'] = $data['r.tid'] = $_GET['noticeId'];
                }
                $this->noticeId = $id;
            }
            $conditions['r.type'] = $data['r.type'] = $_GET['type'];//回复表中的类型  1 闲情  2 文库   3 全站公告  4全局公告
            $this->type = $_GET['type'];//前台区分文库还是闲情 全站还是全局
        }

        $this->login = $this->isLogin();
        $order = 'r.position ASC';//排序方式；
        $conditions['r.first'] = 2;
        $p = $_GET['p']?$_GET['p']:1;

        $reply = $this->selectInvitation($topic,$conditions,$p,$pageNum=99,$order);//查询回复
        $data['r.first'] = 1;
        $list = $this->selectInvitation($topic,$data,$p=null,$pageNum=null,$order=null,$group='t.id');//单独查一次一楼内容(不许除第一页的其他页也显示1楼（未）)
        if(!$list)//帖子被屏蔽或者删除  1楼肯定不存  所以这里判断一楼的搜索结果
        {
            $this->replyError($_GET['address'],$_GET['tzAttr']);
            die;
        }
        if($_GET['type'] == 2)
        {
            showCharacter($list,1);
        }

        $list[0]['tagname'] = XqTag($list[0]['type'],$_GET['type']);
        articleTag($list,2);

        /*-------仅用来做地址链接跳转------------*/
        $this->tzAttr = $_GET['tzAttr'];//1 大众  2 广付肉  3 百合
        $this->address = $_GET['address'];//仅用来做页面左上角的链接跳转  1=>闲情  2=>文库
        $this->gb = $_GET['gb'];//用来到前台区分公告显示左上角的地址
        /*-------仅用来做地址链接跳转 end------------*/
        $show = getPage($topic,$conditions,$pageSize=99);//分页显示
        $this->page = $show->show();// 分页显示输出
        $this->nickname = $_SESSION['user']['user_nicename'];
        $this->head = $list;
        $this->p = $_GET['p']?$_GET['p']:1;//(前台用来只有在第一页才显示1楼)
        $this->isAdmin = $_GET['isAdmin'];
        $this->reply = $reply;
        $this->display(':trleisure_login_details');
    }

    /**
     * 是否登录
     */
    function isLogin()
    {
        if(isset($_SESSION['user']))
        {
            //登录状态
            $login = 1;
        }
        else
        {
            //未登录
            $login = 2;
        }
        return $login;
    }

    /**
     * 组装帖子查询条件
     * @param $tagId    标签
     * @param $attr     属性
     * @param $status   判断加精还是套红等状态（暂时两种  2=>加精  3=>套红）
     */
    public function handle($tagId,$attr,$status)
    {
        $conditions = array();
        if($tagId)
        {
            $conditions['t.type'] = array('in',$tagId);//tag标签筛选
        }
        if($_SESSION['user'])
        {
            $condition['t.userid'] = $_SESSION['user']['id'];//用户是否登录
        }
        if($status == 2)
        {
            $conditions['t.isred'] = 1;//加精
        }
        if($status == 3)
        {
            $conditions['t.isexcellent'] = 1;//套红
        }
        $conditions['t.attr'] = $attr?$attr:2;//三大属性（默认广付肉）
        $conditions['t.shield'] = 2;//默认显示不屏蔽
//		print_r($conditions);
        return $conditions;

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
                array('nickname', 'require', '请输入发帖人名称！', 0),
                array('nickname', '0,30', '发帖人名称过长！', 1, 'length'),
                array('title', 'require', '请输入标题！', 0),
                array('post[post_content]', 'require', '请输入文章内容！', 0),
                array('title','0,90','文章标题过长！', 1, 'length'),
            );
            if(!$topic->validate($rules)->create())
            {
                $topic->getError();
            }
            $data = array();
            $data2['createtime'] = $data['createtime'] = $data['lasttime'] = time();
            $data2['nickname'] = $data['nickname'] = htmlspecialchars(trim($_POST['nickname']));
            $data['attr'] = $_POST['attr'];
            $data['type'] = $_POST['tag'];
            $data2['shield'] = $data['shield'] = 2;
            $data2['cryptonym'] = $_POST['isCryptonym'];//是否匿名
//            echo "<pre>";
//            print_r($_POST);die;
            $data2['title'] = $data['title'] = htmlspecialchars(trim($_POST['theme']));
//            $data2['content'] = $_POST['content'];
            $data2['createip'] = $data['createip'] = get_client_ip();//获取ip
            $data2['type'] = 1;
            $data2['headimg'] = $_POST['systemHead'];
            $data['isexcellent'] = 2;
            $data['replies'] = 0;
            $data['isred'] = 2;
            $data['isclosed'] = 2;
            $data2['userid'] = $data['userid'] = $_SESSION['user']['id']?$_SESSION['user']['id']:null;
            $data2['content'] = $_POST['post'][post_content];//过滤敏感字段
            $data2['first'] = 1;
            $data2['position'] = 0;
//            echo "<pre>";
//            var_dump($data);die;
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
                $this->redirect(U('index/topicDetail',array('type'=>1,'id'=>$insertId,'tzAttr'=>$_POST['attr'],'address'=>1)));
            }
            else
            {
                M()->rollback();
            }
        }
        else
        {
            $this->error('发布失败');
        }
    }

    /**
     * 新增文章
     */
    public function createArticle()
    {
        if($_POST)
        {
            $topic = M('mf_topic');
            $reply = M('xq_reply');
            $model = M('mf_tag');//需要筛选的table
            $rules = array(
                array('nickname', 'require', '请输入发帖人名称！', 0),
                array('nickname', '0,30', '发帖人名称过长！', 1, 'length'),
                array('authorName[]', 'require', '请输入原作名！', 0),
                array('gong[]', 'require', '请输入角色名！', 0),
                array('title', 'require', '请输入标题！', 0),
                array('post[post_content]', 'require', '请输入文章内容！', 0),
                array('title','0,90','文章标题过长！', 1, 'length'),
            );
            if(!$topic->validate($rules)->create())
            {
               $topic->getError();
            }
            $model->startTrans();//开启事物
            $through = true;
            if ($_POST['tag'] != "") {
                $tags = explode(',', $_POST['tag']);
                $newTagArr = $this->handleTags($tags, $model);
                $oldTagId = $newTagArr['id'];
                $attr = ($_POST['attr'] == 1 || $_POST['attr'] == 3) ? 1 : 2;//三大类
                $newTagId = $this->insertTag($newTagArr['name'], $model, $attr);//返回的是一个id字符串
            }

            //$conditions => 存入mf_topic表    $conditions2 => 存入xq_reply表
            $conditions = array();
            $conditions2 = array();
            if ($_POST['authortype'] == 1)
            {
                $str = '';
                foreach ($_POST['authorName'] as $val)
                {
                    $str .= trim($val) . '&&&&';
                }
                $conditions['authorname'] = trim($str, '&&&&');
            }
            else
            {
                $conditions['authorname'] = trim($_POST['authorName'][0]);
            }

            $conditions['cptype'] = $_POST['cptype'];
            $conditions['tagid'] = ($oldTagId == "" || $newTagId == "") ? $oldTagId . $newTagId : $oldTagId . "," . $newTagId;
            $conditions['attr'] = $_POST['attr'];
            $conditions2['nickname'] = $conditions['nickname'] = htmlspecialchars(trim($_POST['nickname']));
            $conditions['type'] = $_POST['type'];//文章类型
            $conditions['authortype'] = trim($_POST['authortype']);
            $conditions2['title'] = $conditions['title'] = htmlspecialchars(trim($_POST['title']));
            $conditions2['hiddentext'] = $_POST['post']['edit_content'];
            $conditions2['content'] = $_POST['post'][post_content];
            $conditions2['createip'] = $conditions['createip'] = $_POST['createIp'];
            $conditions2['headimg'] = $_POST['systemHead'];

            /*-----一些发帖的默认状态---------*/
            $conditions2['userid'] = $conditions['userid'] = $_SESSION['user']['id'] ? $_SESSION['user']['id'] : null;
            //            $conditions['attr'] = $_POST['attr'];//文章大类
            $conditions2['first'] = 1;
            $conditions2['cryptonym'] = $_POST['isCryptonym'];//是否匿名

            $conditions2['position'] = 0;
            $conditions2['type'] = 2;
            if (!$_POST['topicId']) {
                $conditions['isexcellent'] = 2;
                $conditions['isred'] = 2;
                $conditions['isclosed'] = 2;
                $conditions['replies'] = 0;
                $conditions2['shield'] = $conditions['shield'] = 2;
                $conditions2['createtime'] = $conditions['createtime'] = $conditions['lasttime'] = time();
            } else {
                $list = $topic->where('id = '.$_POST['topicId'])->find();
                $conditions['isexcellent'] = $list['isexcellent'];
                $conditions['isred'] = $list['isred'];
                $conditions['isclosed'] = $list['isclosed'];
                $conditions['replies'] = $list['replies'];
                $conditions2['shield'] = $conditions['shield'] = $list['shield'];
                $conditions2['edittime'] = $conditions['edittime'] = time();
            }

            /*-----一些发帖的默认状态---------*/
            $gongArr = array_values(array_filter($_POST['gong']));//过滤掉数组中的空值
            $shouArr = array_values(array_filter($_POST['shou']));//过滤掉数组中的空值
            $conditions['cp'] = $this->handlerole($_POST['cptype'], $gongArr, $shouArr);//处理角色
            //                echo "<pre>";
            //                print_r(array_filter($_POST['gong']));die;
            if ($through) {
                if ($_POST['topicId']) {
                    $conditions['id'] = $_POST['topicId'];
                    $modified = $topic->data($conditions)->save();
                    if (false !== $modified) {
                        $through = true;
                    } else {
                        $through = false;
                    }
                    $tid = $_POST['topicId'];
                } else {
                    $tid = $topic->data($conditions)->add();
                    if (!$tid) {
                        $through = false;
                    }
                }
            }

            if ($through) {
                if ($_POST['topicId']) {
                    $conditions2['tid'] = $_POST['topicId'];
                    $where['tid'] = $_POST['topicId'];
                    $where['type'] = 2;//回复表中2代表文库
                    $where['first'] = $_POST['first'];
                    $modified = $reply->where($where)->data($conditions2)->save();
                    if (false !== $modified) {
                        $through = true;
                    } else {
                        $through = false;
                    }
                } else {
                    $conditions2['tid'] = $tid;
                    $result = $reply->data($conditions2)->add();
                    if (!$result) {
                        $through = false;
                    }
                }
            }
            if ($through) {
                $model->commit();
                $this->redirect('index/topicDetail', array('type' => 2, 'id' => $tid, 'tzAttr' => $_POST['attr'], 'address' => 2));
            } else {
                $model->rollback();
                $this->error('发布失败');
            }
        }
        else
        {
            $this->error('发布失败');
        }
    }

    /*
     * 暂时先文库编辑
     */
    public function editArticle()
    {
        $id = $_GET['id'];
        $type = $_GET['type'];
        if($type == 1)
        {
            $model = 'xq_topic t';//闲情
        }
        else
        {
            $model = 'mf_topic t';//文库
        }
        $condition['r.type'] = $type;
        $condition['t.id'] = $id;
        $condition['r.first'] = 1;
        $result = $this->selectInvitation($model,$condition);
        $this->tagId = $result[0]['tagid'];
        $this->title_tag = $this->bubble($result[0]['attr'],$result[0]['tagid']);//标题下面的标签(文库)
        $this->json_tag = json_encode($this->bubble($result[0]['attr']),JSON_UNESCAPED_UNICODE);
        $this->attr = $result[0]['attr'];
        //将原作，cp处理成json传到前台进行编辑
        if(stripos($result[0]['authorname'],'&&&&'))
        {
            $author = $this->editHandle($result[0]['authorname']);
            if(count($author)<3)
            {
                $this->authorNum = $author;
            }
            $this->jsonAuthor = json_encode($author,JSON_UNESCAPED_UNICODE);
        }
        $editCpType = $this->editHandle($result[0]['cp'],$result[0]['cptype']);//存在二维数组空数组
        if($result[0]['cptype'] == 2)
        {
            $this->editGong = $editCpType[0][0];
            $this->editShou = $editCpType[1][0];
        }
        elseif($result[0]['cptype'] == 1)
        {
            $this->editGongShou1 = $editCpType[0];
            $this->editGongShou2 = $editCpType[1];
        }
        elseif($result[0]['cptype'] == 3)
        {
            $this->editNocp = $editCpType;
        }
        $this->jsonEditCpType = json_encode($editCpType,JSON_UNESCAPED_UNICODE);
        $this->edit = $result;

        $this->display(':trleisure_newArticle');
    }


    /**
     * 处理文库编辑
     * @param  string  $name  名称（原作，角色，cp）
     * @param  int     $type  类型（CP,NP,无CP）
     */
    public function editHandle($name,$type)
    {
        if($type)
        {
            if($type == 1 )//普通cp
            {
                $split = explode(',',$name);

                foreach($split as $key=>$val)
                {
                    $cpArr[$key] = explode('&&&&',$val);
                }
            }
            elseif($type == 2)//NP
            {
                $split = explode('&&&&',$name);
                $cpArr[0] = explode(',',$split[0]);
                $cpArr[1] = explode(',',$split[1]);
            }
            elseif($type == 3)//无CP
            {
                $cpArr = explode(',',$name);
            }
            else
            {
                $this->error('没有该角色类型');
            }
            return $cpArr;
        }
        else
        {
            $authorArr = explode('&&&&',$name);
            return $authorArr;
        }
    }

    /*
     * 文库编辑更改tag标签
     */
    public function ajaxTag()
    {
        $type = ($_GET['type'] == 1 || $_GET['type'] == 3)?1:2;
        $result = M('mf_tag')->where('type = '.$type)->select();
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
    }

    /**
     * Ajax 判断发表/回复时间是否小于设置时间和当前帖子是否存在（屏蔽和删除）  1=>发表  2=回复
     */
    public function ajaxInterval()
    {
        $isSet['id'] = array('eq',$_GET['id']);
        if($_GET['lib'] ==1)
        {
            $model = M('xq_topic' );//闲情数据表
            $isSet['shield'] = array('eq',2);
        }
        else if($_GET['lib'] ==2)
        {
            $model = M('mf_topic');//文库数据表
            $isSet['shield'] = array('eq',2);
        }
        else//全站全局公告
        {
            $model = M('xq_notice');
            $isSet['isclosed'] = array('eq',2);
        }
        if($_GET['type'] == 1)
        {
            $_SESSION['user']['id']?$condition['userid'] = $_SESSION['user']['id']:$condition['createip'] = get_client_ip();; //登录用查登录id，未登录查ip
            $field = 'createtime';
            $gapField = 'report_gap';
            $order = ''.$field.' desc';
        }
        else
        {
            $condition['id'] = $_GET['id'];
            $field = 'lasttime';
            $gapField = 'reply_gap';
            //回复之前先判断该帖是否存在
            $isExist = $model->where($isSet)->find();
            if(!$isExist)
            {
                $newArr['isExist'] = 1;
                $this->ajaxReturn($newArr);
                die;
            }
        }

        //这里先判断有没有禁言和禁ip
        if(!$_SESSION['user']['id'])
        {
            $result = M('xq_ip')->where('ip = '."'".$condition['createip']."'")->find();
            if($result)
            {
                if($result['bannedtime']>time())
                {
                    $newArr['info'] = 2;
                    $this->ajaxReturn($newArr);
                    die;
                }
                else
                {
                    M('xq_ip')->where('id = '.$result['id'])->delete();
                    $newArr['info'] = 3;
                    $this->ajaxReturn($newArr);
                    die;
                }
            }
        }

        $gapTime = M('set')->field($gapField)->find();//查询时间间隔
        $lasttime = $model->field($field)->where($condition)->order($order)->find();//当前用户最新一条帖子的时间;
        $result = time()-$lasttime[$field];

        if($result<=$gapTime[$gapField])
        {
            $newArr['info'] = 1;
            $newArr['time'] = $gapTime[$gapField];
            $this->ajaxReturn($newArr);
        }
    }

    /*
     * 页面错误过渡页
     * @$address  int   类型  1 闲情  2 文库
     * @tzAttr    int   属性  1 大众  2 腐向  3 百合
     */
    function replyError($address,$tzAttr)
    {
        if($_POST)//回复
        {
            if($_POST['address'] == 1)
            {
                $this->error('该贴已不存在',U('index/index',array('attr'=>$_POST['tzAttr'])));
            }
            else
            {
                $this->error('该贴已不存在',U('index/trleisure_library',array('attr'=>$_POST['tzAttr'])));
            }
        }
        else
        {
            //标题跳转
            if($address == 1)
            {
                $this->error('该贴已不存在',U('index/index',array('attr'=>$tzAttr)));
            }
            else
            {
                $this->error('该贴已不存在',U('index/trleisure_library',array('attr'=>$tzAttr)));
            }
        }
    }

    /**
     * 搜索字段
     * @param $lib  存在显示文库搜索字段，反之闲情搜索字段
     */
    public function searchField($lib)
    {
        if(!$lib)
        {
            $Field = array(1=>'帖子主题',2=>'主题帖内容',3=>'主题帖发帖人',4=>'跟帖内容',5=>'跟帖发帖人');
        }
        else
        {
            $Field = array(1=>'文章名',2=>'文章内容',3=>'原作名称',4=>'角色名',5=>'作者');
        }
        return $Field;
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
                if($v['name'] == trim($val))
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
     * 处理文章/帖子详情
     */
//    function handleCon($list)
//    {
//        $newArr = array();
//        $newArr[0] = $list[0];
//        for ($i=1;$i<=count($list);$i++)
//        {
//            $new[] = $list[$i];
//        }
//        $newArr[1] = array_filter($new);
//        return $newArr;
//    }

    /**
     * 回复
     */
    public function reply()
    {
        $url="";
        if($_POST)
        {
            if($_POST['type'] == 1)
            {
                $topic = M('xq_topic');
                $url .= '/tongrenmiao/index.php?g=xq&m=index&a=topicDetail&type='.$_POST[type].'&id='.$_POST[tid].'&isAdmin='.$_POST['isAdmin'].'&address='.$_POST['address'].'&tzAttr='.$_POST['tzAttr'];
            }
            else if($_POST['type'] == 2)
            {
                $topic = M('mf_topic');
                $url .= '/tongrenmiao/index.php?g=xq&m=index&a=topicDetail&type='.$_POST[type].'&id='.$_POST[tid].'&isAdmin='.$_POST['isAdmin'].'&address='.$_POST['address'].'&tzAttr='.$_POST['tzAttr'];
            }
            else
            {
                $topic = M('xq_notice');
                if($_POST['type'] == 3)
                {

                    $url .= '/tongrenmiao/index.php?g=xq&m=index&a=topicDetail&noticeId='.$_POST['tid'].'&isglobal=1&type='.$_POST['type'].'&isAdmin='.$_POST['isAdmin'];
                }
                else
                {

                    $url .= '/tongrenmiao/index.php?g=xq&m=index&a=topicDetail&noticeId='.$_POST['tid'].'&type='.$_POST['type'].'&isAdmin='.$_POST['isAdmin'];
                }
            }
            $rules = array(
                array('nickname', 'require', '请输入名字！', 0),
                array('nickname', '0,30', '名字过长！', 1, 'length'),
                array('post[post_content]', 'require', '请输入回复内容！', 0),
            );
            if(!$topic->validate($rules)->create())
            {
                $topic->getError();
            }
            $conditions['headimg'] = $_POST['systemHead'];
            $conditions['userid'] = $_SESSION['user']['id']?$_SESSION['user']['id']:null;
            $conditions['nickname'] = htmlspecialchars(trim($_POST['nickname']));

            $conditions['content'] =  $_POST['post'][post_content];
            $data['type'] = $conditions['type'] = $_POST['type'];
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
                $p = $this->replyPosition($data);
                if($p>1)
                {
                    $url .= '&p='.$p;
                }
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
                $url .= '#dw'.$conditions['position'];//回复定位具体页数具体楼层
                $model->commit();
                $this->redirect($url);
            }
            else
            {
                $model->rollback();
            }
        }
        else
        {
            $this->error('回复失败');
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
     * 显示主题标签
     * @param  Array  $list  结果集
     * @param  int    $type  1=>闲情  2=>文库
     */
    function showTag($list,$type)
    {
        if(!$list)
        {
            return false;
        }
        foreach ($list as $key=>$val)
        {
            $list[$key]['typename'] = XqTag($val['type'],$type);
        }
        return $list;
    }

    /**
     * 全局公告
     * @param  int  全局公告id
     */
    function globalnotice($id)
    {
        $model = M('xq_notice t');
        if($id)
        {
            $condition['t.id'] = array('eq',$id);
        }
        $condition['t.type'] = 2;
        $condition['r.type'] = 4;
        $list = $model
            ->field('t.id,t.title,t.replies,t.sort,t.isroll,t.createtime,t.nickname,t.type,t.isclosed,t.lasttime,r.content,r.position,r.createtime,r.type rtype')
            ->join('left join '.C('DB_PREFIX').'xq_reply r ON t.id = r.tid')
            ->where($condition)
            ->group('t.id')
            ->order('t.sort desc,t.createtime desc')
            ->select();
        return $list;
    }

    /**
     * 全站公告
     * @param  int  全站公告
     */
    function rollnotice()
    {
        $model = M('xq_notice t');
        $condition['t.isroll'] = 1;
        $condition['t.type'] = 1;
        $condition['r.type'] = 3;

        $list = $model
            ->field('t.id,t.title,t.replies,t.sort,t.isroll,t.createtime,t.nickname,t.type,t.isclosed,t.lasttime,r.content,r.position,r.createtime,r.type rtype')
            ->join('left join '.C('DB_PREFIX').'xq_reply r ON t.id = r.tid')
            ->where($condition)
            ->group('t.id')
            ->order('t.createtime desc')
            ->select();
        return $list;
    }

    /**
     * 文库最新更新
     */
    function lastUpdate($attr)
    {
        $condition['attr'] = $attr;
        $condition['type'] = array('neq',3);
        $condition['shield'] = array('neq',1);
        $condition['isclosed'] = array('neq',1);
        $list = M('mf_topic')->where($condition)->order('createtime desc')->find();
        return $list;
    }

    //分享页面
    public function share()
    {
        //  1=>闲情   2=>文库
        $type = $_GET['type'];
        $id = $_GET['id'];
        //闲情或文库
        if($type == 1)
        {
            $topic = 'xq_topic t';
        }
        if($type == 2)
        {
            $topic = 'mf_topic t';
        }
        if($type == 3 || $type == 4)//公告详情
        {
            $topic = 'xq_notice t';
        }
        $conditions['r.tid'] = $data['r.tid'] = $id;
        $conditions['r.type'] = $data['r.type'] = $type;//回复表中的类型  1 闲情  2 文库   3 全站公告  4全局公告
        $order = 'r.position ASC';//排序方式；
        $conditions['r.first'] = 2;
        $p = $_GET['page']?$_GET['page']:1;
        $pageNum=19;
        //先算出总页数  不够一页的话前台不显示翻页
        $count = M('xq_reply')->where('type = '.$type.' and tid = '.$id)->count();
        $countPage = ceil($count/$pageNum)>1?ceil($count/$pageNum):0;
//        echo "<pre>";
//        print_r($conditions);die;
        $reply = $this->selectInvitation($topic,$conditions,$p,$pageNum,$order);//查询回复
        foreach($reply as $key=>$val)
        {
            $reply[$key]['replyDate'] = date('Y-m-d H:i:s',$val['rcreatetime']);
        }
        $data['r.first'] = 1;
        $list = $this->selectInvitation($topic,$data,$p=null,$pageNum=null,$order=null,$group='t.id');
        if(!$list)//帖子被屏蔽或者删除  1楼肯定不存在  所以这里判断一楼的搜索结果
        {
            return false;
        }
        if($_GET['type'] == 2)
        {
            showCharacter($list,1);
            $list[0]['newTitle'] = $list[0]['author'].$list[0]['cpname'].$list[0]['title'];
        }
        else
        {
            $list[0]['newTitle'] = $list[0]['title'];
        }
        $list[0]['tagname'] = XqTag($list[0]['type'],$_GET['type']);
        $list[0]['zhutiDate'] = date('Y-m-d H:i:s',$list[0]['createtime']);
        articleTag($list,2);
        $newArr['reply'] = $reply;
        $newArr['zhuti'] = $list[0];
        $newArr['pageNum'] = $countPage;
        $this->ajaxReturn($newArr);
//        $this->type = $_GET['type'];//前台区分文库还是闲情 全站还是全局
//        $this->display(':fenxiang');
    }

    public function ajax_poster()
    {
        $name = trim($_GET['name']);
        $result = M('users')->where('user_nicename = '."'".$name."'")->find();
        if($result)
        {
            echo true;
        }
        else
        {
            echo false;
        }

    }


    /*-----------------临时用于更改数据库错误数据（仅此而已）-----------------*/
    //更新数据（统计当前帖子的数目，更新主题帖的回复数字段，使其一致）
//    function updateErrorReplies()
//    {
//        $topic = $_GET['biao'];
//        $type = $_GET['cate'];
//        //查出回复表中根据tid分类的所有的数据
//        $sql1 = "select type,tid,count(id) as num from cmf_xq_reply where type = ".$type." GROUP BY tid ";
////        echo $sql1;die;
//        $reply = M()->query($sql1);
//        foreach($reply as $key=>$val)
//        {
//
//            $new1[]=$val[tid];
//            $new2[]=$val[num];
//        }
//        $new3  = array_combine($new1,$new2);//利用两个数组一个做键，一个做值
//        //根据查出来的tid进行更改topic，mf_topic中的reolies字段
//        $ids = implode(',', array_keys($new3));//拿出来新数组中的键也就是需要更新的数据的id
//        $sql = "UPDATE ".$topic." SET replies = CASE id ";
//        foreach ($new3 as $id => $ordinal) {
//            $sql .= sprintf("WHEN %d THEN %d ", $id, $ordinal);
//        }
//        $sql .= "END WHERE id IN ($ids)";
//        $ok = M()->execute($sql);//执行写入操作用excute
//        if($ok >0 || $ok === 0 )
//        {
//           echo 'OK';die;
//        }
//    }
//
//    //将回复表中的楼层数重新整理顺序 1-N
//    function newOrder()
//    {
//        $topic = $_GET['biao'];
//        $type = $_GET['cate'];
//        //定义一个变量
//        $j=1;
//        //先查出tid的数量循环
//        $sql = 'SELECT id from '.$topic;
//        $tid = M()->query($sql);
//        $countTid = count($tid);
//        for($i=0;$i<$countTid;$i++)
//        {
//            $sql2 = 'SELECT id FROM cmf_xq_reply WHERE tid = '.$tid[$i]['id'].' AND type = '.$type.'  order by createtime asc';
//            $position = M()->query($sql2);
//            $countPos = count($position);
//            for($b=0;$b<$countPos;$b++)
//            {
//                $sql3 = 'update cmf_xq_reply SET position = '.$j.' where id = '.$position[$b]['id'];
////                echo $sql3;die;
//                $ok = M()->execute($sql3);
//                $j++;
//            }
//            //将变量重新赋值
//            $j = 1;
//        }
//    }
    /*-----------------临时用于更改数据库错误数据（仅此而已）-----------------*/
}


















