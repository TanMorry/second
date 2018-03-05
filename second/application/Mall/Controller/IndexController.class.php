<?php
/**
 * Created by PhpStorm.
 * User: kidstone、
 * Date: 2016/6/6
 * Time: 15:30
 */
namespace Mall\Controller;
use Common\Controller\HomebaseController;
header("Content-type: text/html; charset=utf-8");

class IndexController extends HomebaseController
{
    protected $customCateArr = array();
    protected $pro_ids = "";
    protected $setmeal_ids = "";
    public function main()
    {
        $weekTime = $this->timeBucket(1);//一周的时间

        $newArr['new'] = $this->newProduct(1,$weekTime);//最近一周内点击量最高的6个(新品推荐)
        $newArr['moods'] = $this->moodsProduct(1,$weekTime);//最近一周内点击量最高的6个(人气同人本)
//        $newArr['maxSales'] = $this->monthMaxSalesOrder(); //一个月内订单量最大的商品
        //人气周边
        $newArr['PopularityRound'] = $this->PopularityRound(1);
        $ids = "";
        foreach($newArr as $key=>$val)
        {
            foreach($val as $v)
            {
                $ids .= $v['id'].',';
            }
        }
        $ids = trim($ids,',');
        //将以上三种类型的id拿出来，不显示在第四种类型下
        $newArr['moodsAndAround'] = $this->moodsAndAround($ids);
        $rank = M('cp_ranklist','mallbuilder_','DB_MALLBUILDER');
        //查询出每个类别下周剁手数最高的前五条记录
        $sql = 'select cpname,mcut,mchange,cptype from mallbuilder_cp_ranklist a where 5>(select count(cpname) from mallbuilder_cp_ranklist where cptype=a.cptype and mcut>a.mcut) ORDER BY a.cptype,a.weekcut desc
';
        $newArr['rank_list'] = $this->every_type($rank->query($sql));
        $this->switchJson($newArr);
    }

    /*
     * 处理排行榜
     */
    function every_type($list)
    {
        $newArr = array();
        $i = 0;
        foreach($list as $key=>$val)
        {
            if($val['cptype'] == $i)
            {
                $newArr[$i][] = $val;
                continue;
            }
            else
            {
                $newArr[$i+1][] = $val;
            }
            $i++;
        }
        if($i <= 4)
        {
            return $newArr;
        }
    }
    
    /*
     * 排行榜详情
     */
    public function rank_detail()
    {
        $rank = M('cp_ranklist','mallbuilder_','DB_MALLBUILDER');
        $condition = array();
        $order = '';
        if(!empty($_GET['rank_type']))
        {
            $condition['cptype'] = $_GET['rank_type'];
        }
        if(!empty($_GET['time_order']))
        {
            $order .= $_GET['time_order'];
        }
        //默认
        $order .= ' desc';
        $result_rank['Info'] = $rank->where($condition)->order($order)->limit(30)->select();
        $this->switchJson($result_rank);
    }

    /**
     * IP胜战
     */
    public function IpWar()
    {
        $rank = M('ip_ranklist','mallbuilder_','DB_MALLBUILDER');
        $condition = array();
        if($_GET['work_type'] != -1)
        {
            $condition['iptype'] = $_GET['work_type'];
        }
        else
        {
            $condition['iptype'] = array('in',array(0,1,2,3));
        }
        if(!empty($_GET['time_order']))
        {
            $order = $_GET['time_order'].' desc';
        }
        $result_rank['Info'] = $rank->where($condition)->order($order)->limit(30)->select();
        $this->switchJson($result_rank);
    }

    /**
     * 新品推荐
     * @param  int  $type       1=>只显示6条  2=>详情显示
     * @param  int  $startTime  存在的话在首页显示最近一周内点击量最高的6个，不存在的话是详情页面
     */
    public function newProduct($type=null)
    {
        $pointGoods = M('product','mallbuilder_','DB_MALLBUILDER');
        $detail = $type?$type:($_GET['detail']?$_GET['detail']:false);//首页传1  详情页传2
        $startTime = $this->timeBucket(1);
        $conditions = array();
        $field = 'id,name,brand,price,pic';
        $order = 'clicks desc,uptime desc';//先按点击量从高到低，在按上传时间从早到晚(时间暂用start_time)
        $conditions['uptime'] = array('between',$startTime);
        $conditions['is_shelves'] = 1;
        if($detail == 1)
        {
            $pageSize = 6;//首页只显示6条
            return $this->selectProduct($pointGoods,$field,$conditions,$limit=0,$pageSize,$order);
        }
        elseif($detail == 2)
        {
            //更多（条件暂无）
            $page = $_GET['page']?$_GET['page']:1;
            $pageSize = 50;
            if($page>1)
            {
                $limit = ($page-1)*$pageSize;
            }
            else
            {
                $limit = 0;
            }
            //统计总页数
            $newArr['countPage'] = ceil($this->countNum($pointGoods,$conditions)/$pageSize);
            $newArr['newProduct'] = $this->selectProduct($pointGoods,$field,$conditions,$limit,$pageSize,$order);
            $this->switchJson($newArr);
        }
        else
        {
            return false;
        }
    }

    /**
     * 人气同人本
     * @param  int  $type       1=>只显示6条  2=>更多
     * @param  int  $startTime  存在的话在首页显示最近一周内点击量最高的6个，不存在的话是详情页面
     */
    public function moodsProduct($type=null,$startTime=null)
    {
        $pointGoods = M('product','mallbuilder_','DB_MALLBUILDER');
        $detail = $type?$type:($_GET['detail']?$_GET['detail']:false);
        $conditions = array();
        $field = 'id,name,brand,price,pic';//暂时先筛选这些字段
        $order = 'clicks desc,uptime desc';//先按点击量从高到低，在按上传时间从早到晚(时间暂用start_time)
        $conditions['type'] = 0;//暂定商品类型为0的时候是人气同人
        $conditions['is_shelves'] = 1;

        if($detail == 1)
        {
            $pageSize = 6;//首页只显示6条(首页ajax请求)
            $conditions['uptime'] = array('between',$startTime);
            return $this->selectProduct($pointGoods,$field,$conditions,$limit=0,$pageSize,$order);
        }
        elseif($detail == 2)
        {
            //更多(更多页ajax请求)
            $weekTime = $this->timeBucket(1);//一周的时间
            $conditions['uptime'] = array('between',$weekTime);
//            $page = $_GET['page']?$_GET['page']:1;
            $pageSize = 50;//更多里面的显示条件暂无，暂定显示50条
            $limit = 0;
            $newArr['moods'] = $this->selectProduct($pointGoods,$field,$conditions,$limit,$pageSize,$order);
            $this->switchJson($newArr);
        }
        else
        {
            return false;
        }
    }

    /**
     * 人气周边
     */
    public function PopularityRound($detail=null)
    {
        $pointGoods = M('product','mallbuilder_','DB_MALLBUILDER');
//        $goodCate = M('product_cat','mallbuilder_','DB_MALLBUILDER');
        $field = 'id,name,brand,price,pic';//暂时先筛选这些字段
        $order = 'clicks desc,uptime desc';//先按点击量从高到低，在按上传时间从早到晚(时间暂用start_time)
        //人气周边  查询分类id为1005（暂定）
        $conditions['catid'] = array('like','%1005%');
        $conditions['is_shelves'] = 1;

        if($detail == 1)
        {
            $limit = 6;//每次刷新页面都随机出现6个;
            $result = $pointGoods->field($field)->where($conditions)->order('rand()')->limit($limit)->select();
            return $result;
        }
        else
        {
            $pageSize = 50;//更多里面的显示条件暂无，暂定显示50条
            $limit = 0;
            $result['PopularityRound'] = $this->selectProduct($pointGoods,$field,$conditions,$limit,$pageSize,$order);
            $this->switchJson($result);
        }
    }

    /**
     * 一个月内订单量最大的商品（首次加载10条，每次新加载10条）
     */
//    public function monthMaxSalesOrder()
//    {
//        $pointGoods = M('product','mallbuilder_','DB_MALLBUILDER');
//        $page = intval($_GET['page'])?intval($_GET['page']):1;
//        $monthTime = $this->timeBucket(2);
//        $conditions['start_time'] = array('between',$monthTime);
//        $pageSize = 4;//显示10条，数据不够，暂定4条
//        //从第几条记录开始
//        if($page>1)
//        {
//            $limit = ($page-1)*$pageSize;
//        }
//        else
//        {
//            $limit = 0;
//        }
//        //查询的时间一定要从过去到现在，不然查询不出来结果
//        $field = 'id,name,brand,price,pic';
//        $order = 'sales desc,start_time desc';//先按销售量从高到低，由于一开始没有销售量所以在按时间有早到晚排序（时间暂用start_time）
//        $list = $this->selectProduct($pointGoods,$field,$conditions,$limit,$pageSize,$order);
//        if($_GET['more'])
//        {
//            $newArr['maxSales'] = $list;
//            $this->switchJson($newArr);
//        }
//        else
//        {
//            return $list;
//        }
//    }

    /**
     * 人气和周边
     * @param $ids 用于筛选掉的id
     */
    public function moodsAndAround($ids)
    {
        $pointGoods = M('product','mallbuilder_','DB_MALLBUILDER');
        $field = 'id,name,brand,price,catid';
//        $condition['is_shelves'] = $condition1['is_shelves'] = 1;
        $condition['type'] = 0;
        //$ids数据不够暂不用
//        $condition['id'] = $condition1['id'] = array('not in',$ids);
        $tong = $pointGoods->field($field)->where($condition)->limit(4)->order('rand()')->select();
        $condition1['catid'] = array('like','%1005%');

        $around = $pointGoods->field($field)->where($condition1)->limit(4)->order('rand()')->select();
        $newArr = array();
        for($i=0;$i<count($tong);$i++)
        {
            $newArr[] = $tong[$i];
            if($i%2!=0)
            {
                $j = count($newArr);//需要重新统计新数组的长度
                $newArr[$j+1] = $around[$i-1];
                $newArr[$j+2] = $around[$i];
            }
        }

        return $newArr;
    }

//    /**
//     * 爱的属性和物的类型
//     */
//    public function loveList()
//    {
//        $pointGoods = M('product','mallbuilder_','DB_MALLBUILDER');
//        $field = 'id,name,brand,price';
//        $condition = array();//爱的属性的查询条件(首次请求)
//        $condition2 = array();//物的类型的查询条件(首次请求)
//        $conditions = array();
//        $conditions['price'] = $condition['price'] = $condition2['price'] = array('gt',0);
//        $page = $_GET['page']?$_GET['page']:1;
//        $pageSize = 5;//一页显示50，数据不够暂时显示5条
//        if($page>1)
//        {
//            $limit = ($page-1)*$pageSize;
//        }
//        else
//        {
//            $limit = 0;
//        }
//        //缺少区分类型的条件
//        //具体类别下的属性  cp圈，原作，CrossOver等
//        if(!$_GET['cate'])
//        {
//            //$condition[] = $condition2[] = 1;//默认是第一个属性
//            $condition['cate'] = 1;
//            $condition2['cate'] = 2;
//            $loveCountNum = $this->countNum($pointGoods,$condition);
//            $kindCountNum = $this->countNum($pointGoods,$condition2);
//            $loveList = $this->selectProduct($pointGoods,$field,$condition,$limit,$pageSize);
//            $matter = $this->selectProduct($pointGoods,$field,$condition2,$limit,$pageSize);
//            $new['matter'] = $matter;
//            $new['loveList'] = $loveList;
//            $new['loveCountNum'] = ceil($loveCountNum/$pageSize);//$loveCountNum;
//            $new['kindCountNum'] = ceil($kindCountNum/$pageSize);//$kindCountNum;
////            $new['loveCountNum'] = 51;//$loveCountNum;
////            $new['kindCountNum'] = 51;//$kindCountNum;
//        }
//        else
//        {
//            //爱的属性or物的类型
////            $conditions['cate'] = $_GET['cate'];
//            //类型下面的具体属性
//            //$conditions[''] = $_GET['attr'];
//            $count = $this->countNum($pointGoods,$conditions);
//            $list = $this->selectProduct($pointGoods,$field,$conditions,$limit,$pageSize);
////            $new['count'] = 51;//$count
//            $new['totalPage'] = ceil($count/$pageSize);//总页数
//            $new['currentList'] = $list;
//        }
//        $this->switchJson($new);
//    }

    /**
     * 爱的属性和物的类型
     */
    public function loveList()
    {
        $pointGoods = M('product','mallbuilder_','DB_MALLBUILDER');
        $field = 'id,name,brand,price';
        $condition = array();//爱的属性的查询条件(首次请求)
        $condition2 = array();//物的类型的查询条件(首次请求)
        $conditions = array();
        $conditions['price'] = $condition['price'] = $condition2['price'] = array('gt',0);
        $page = $_GET['page']?$_GET['page']:1;
        $pageSize = 5;//一页显示50，数据不够暂时显示5条
        $like = $_GET['catid'];
        if($page>1)
        {
            $limit = ($page-1)*$pageSize;
        }
        else
        {
            $limit = 0;
        }
        //缺少区分类型的条件
        //具体类别下的属性  cp圈，原作，CrossOver等
        if(!$_GET['cate'])
        {
            //$condition[] = $condition2[] = 1;//默认是第一个属性
//            $condition['cate'] = 1;
//            $condition2['cate'] = 2;
            $loveCountNum = $this->countNum($pointGoods,$condition);
            $kindCountNum = $this->countNum($pointGoods,$condition2);
            $loveList = $this->selectProduct($pointGoods,$field,$condition,$limit,$pageSize);
            $matter = $this->selectProduct($pointGoods,$field,$condition2,$limit,$pageSize);
            $new['matter'] = $matter;
            $new['loveList'] = $loveList;
            $new['loveCountNum'] = ceil($loveCountNum/$pageSize);//$loveCountNum;
            $new['kindCountNum'] = ceil($kindCountNum/$pageSize);//$kindCountNum;

            /*查找属性分类：推荐cp圈等*/
            $productCat = M('product_cat','mallbuilder_','DB_MALLBUILDER');
            $field2 = 'catid,cat';
            $new['productCat']['books'] = $productCat->field($field2)->where('catid=1003 OR catid=1004' )->select();
            $new['productCat']['periphery'] = $productCat->field($field2)->where('catid=1005 OR catid=1006' )->select();
            $new['productCat']['money'] = $productCat->field($field2)->where('catid>100200 AND catid<100300' )->select();

        }
        else if($_GET['cate'] == 1){
            if($_GET['pid'] == 1){

            }
            if($_GET['pid'] == 2){

            }
            if($_GET['pid'] == 3){
                $conditions['twotype'] = $_GET['catid'];
            }
            $count = $this->countNum($pointGoods,$conditions);
            $list = $this->selectProduct($pointGoods,$field,$conditions,$limit,$pageSize);
            $new['totalPage'] = ceil($count/$pageSize);//总页数
            $new['currentList'] = $list;
        }
        else{
            $conditions['catid'] = array('like',$like.'%');
            $count = $this->countNum($pointGoods,$conditions);
            $list = $this->selectProduct($pointGoods,$field,$conditions,$limit,$pageSize);
            $new['totalPage'] = ceil($count/$pageSize);//总页数
            $new['currentList'] = $list;
        }

        $this->switchJson($new);
    }

    /*-------------------------商品详情-------------------------*/
    /**
     * 商品详情
     */
    public function goodsDetails()
    {
        $id = $_GET['id'];
        //商品表
        $pointGoods = M('product','mallbuilder_','DB_MALLBUILDER');
        //套餐表
        $setmeal = M('product_setmeal','mallbuilder_','DB_MALLBUILDER');
        //产品评论表
        $comment = M('product_comment','mallbuilder_','DB_MALLBUILDER');
        //店铺表
        $shop = M('shop','mallbuilder_','DB_MALLBUILDER');
        //商品信息
        $list = $pointGoods
                    ->join('left join mallbuilder_product_detail on mallbuilder_product.id = mallbuilder_product_detail.proid where mallbuilder_product.id = '.$id)
                    ->find();
        $newArr['info'] = $list;
        //店铺基本简单信息
        $shopInfo = $shop->where('userid = '.$list['member_id'])->find();
//        echo $shop->_sql();die;
        $newArr['shopInfo'] = $shopInfo;
//        echo "<pre>";
//        print_r($newArr);die;
        //统计该店铺的全部宝贝
        $shopCon['member_id'] = $list['member_id'];
        $shopCon['is_shelves'] = 1;
        $newArr['shopInfo']['babyNum'] = $this->countNum($pointGoods,$shopCon);
        $newArr['shopInfo']['shopId'] = $list['member_id'];
        //找出该店铺销售量最多的前两个产品
        $hotBaby = $pointGoods->field('id,member_id,name,price,pic')->where('id <> '.$list['id'].' and catid like '."'".'%'.$list['catid'].'%'."'")->order('sales desc')->limit(2)->select();
        $newArr['hotBaby'] = $hotBaby;
        //----------------------------------用户区获取
        $newArr['user_ip']=convertip('218.79.42.255');//convertip(getip());
//        echo $list['freight_id'];die;
        if($newArr['user_ip']=='- LAN') $newArr['user_ip']='上海';
        //----------------------------------跟据所在地自动算出的运费
        $newArr['info']['freight_count']= $this->get_log_price($list['freight_id'],$newArr['user_ip']);//,$type=1
        //----------------------------------
        //商品套餐
        $list['porperty'] = $setmeal->where("pid = ".$id." and property_value_id !=''")->select();

        //商品总评论
        $newArr['comcount'] = $this->commentCount($comment,$id);
        //最新商品的内容和规格
        $newArr['latestCom'] = $this->latestComment($comment,$id);

        $result = $this->productSpec($list['porperty']);
        $newArr['setmeal'] = $result['setmeal']?$result['setmeal']:"";
        $newArr['spec_val'] = $result['spec_val']?$result['spec_val']:"";
        $newArr['spec'] = $result['spec']?$result['spec']:"";

        //每次访问产品详情页将该商品存入表中，作为提供猜你喜欢的依据，每个用户十条
        $guess_love = M('guess_love','mallbuilder_','DB_MALLBUILDER');
        $love_condition['pid'] = $_GET['id'];
        $love_condition['userid'] = $_GET['userid'];
        $love_condition['catid'] = $list['catid'];
//        $love_condition['product_name'] = $list['name'];
        $love_condition['createtime'] = time();
        //查询猜你喜欢表中有没有相同的产品
        $love_result = $guess_love->field('pid')->select();
        $count_result = count($love_result);
        $isset = true;
        //判断该产品是否已经存在
        foreach($love_result as $val)
        {
            if($val['pid'] == $_GET['id'])
            {
                $isset = false;
                break;
            }
        }
        if(!$isset)
        {
            //数据库中存在该产品，更改该产品的时间
            $guess_love->where('pid='.$_GET['id'].' and userid='.$_GET['userid'])->setField('createtime',time());
        }
        else
        {
            if($count_result < 100)
            {
                //少于100条，添加该条
                $guess_love->data($love_condition)->add();
            }
            else
            {
                //超过100条，删掉最早的一条，添加该条
                $delete_id = $guess_love->field('id')->order('createtime asc')->find();
                $delete_result = $guess_love->where('id='.$delete_id['id'])->delete();
                if($delete_result)
                {
                    $guess_love->data($love_condition)->add();
                }
            }
        }

        if(count($newArr)<1)
        {
            $newArr = 0;
        }
        $this->switchJson($newArr);
    }

    /*
     * 商品规格
     */
    public function productSpec($list)
    {
        foreach($list as $key=>$val)
        {
            $a=explode(',',$val['setmeal']);
            $b[$key] = $val['setmeal'];
            $c[$b[$key]]['id'] = $val['id'];
            $c[$b[$key]][] = $val['price'];
            $c[$b[$key]][] = $val['stock'];
            $c[$b[$key]][] = $val['sku'];//货号

            $newArr['setmeal'] = $c;
            foreach($a as $k=>$v)
            {
                $spec_val[$k][]=$a[$k];
            }
        }
        $newArr['setmeal'] = $c;

        //去除套餐重复
        foreach($spec_val as $val)
        {
            $newArr['spec_val'][] = array_values(array_unique($val));
        }
        $newArr['spec'] = explode(',',$list[0]['spec_name']);
        return $newArr;
    }

    /**
     * 商品评论统计总数(包括好评总数，中评总数，差评总数) + 一条最新的评价
     * @param   $comment   数据表
     * @param   $id        产品id
     */
    public function commentCount($comment,$id)
    {
        $sql = 'select count(id) as countNum,sum(case when goodbad=1 then 1 else 0 end) bad,sum(case when goodbad=2 then 1 else 0 end) middle,sum(case when goodbad=3 then 1 else 0 end) good from mallbuilder_product_comment where pid = '.$id.' and goodbad>0';
        $result =$comment->query($sql);
        foreach($result[0] as $key=>$val)
        {
            if($val == "")
            {
                $result[0][$key] = 0;
            }
        }
        return $result[0];
    }

    /**
     * 查询最新一次的卖家评论和规格样式
     * @param   $comment   数据表
     * @param   $id        产品id
     */
    public function latestComment($comment,$id)
    {
        //最新一条评论（不区分评论程度）（给评论表添加订单号的字段  写评论的时候多存一个订单号）
        $result = $comment->where("pid = ".$id." and con != ''")->order('uptime desc')->find();
        if(!$result)
        {
            return "";
        }
        $newArr['name'] = $result['user'];
        $newArr['con'] = $result['con'];
        $newArr['date'] = date('Y-m-d',$result['uptime']);
        //用订单号+产品id+买家id直接找出当前订单的规格
        $order_pro = M('product_order_pro','mallbuilder_','DB_MALLBUILDER');
        $pro = $order_pro->where("buyer_id = ".$result['userid']." and pid = ".$result['pid']." and order_id = ".$result['order_id']." and spec_value !=''")->find();
        $newArr['spec'] = $this->handleResult($pro);
        return $newArr;
    }

    /**
     * 每次加载10条相同属性的产品（看了又看）条件都是暂定（数据库无字段）
     */
    public function seeAgain()
    {
        //获取传来原作和cp，作为推荐的条件
        //推荐与当前产品相同的属性的产品10个，每次加载10条  相同的属性包括（原作，cp）
        $pointGoods = M('product','mallbuilder_','DB_MALLBUILDER');
        //查询除了当前产品外的相关属性的产品
        $conditions['catid'] = array('like','%'.$_POST['catid'].'%');
        $conditions['id'] = array('neq',$_POST['pid']);

        if($_POST['loadType'] == 1)
        {
            //第一次上拉查出产品的具体信息
            $condition['id'] = $_POST['pid'];
            $detailInfo = $this->selectProduct($pointGoods,$field=null,$condition);
            $result['detailInfo'] = $detailInfo[0];
            $result['detailInfo']['picArr'] = explode(',',$detailInfo[0]['pic_more']);
            //第一次首先加载10条产品推荐

            $result['tuijian'] = $this->selectProduct($pointGoods,$field,$conditions,$limit=0,$pageSize=10,$order=null);

        }
        else
        {
            //第二次上拉就是加载相同属性的产品(相同分类)
            $field = 'id,name,price,pic';
            $page = $_POST['page']?$_POST['page']:1;
            $pageSize = 10;//每次加载10条
            if($page>1)
            {
                $limit = ($page-1)*$pageSize;
            }
            else
            {
                $limit = 0;
            }
            $result['tuijian'] = $this->selectProduct($pointGoods,$field,$conditions,$limit,$pageSize,$order=null);
        }
        $this->switchJson($result);
    }
    /*-------------------------商品详情 END-------------------------*/

    /*-------------------------评论详情-------------------------*/
    /**
     * 商品评论分类详情(全部评价)
     */
    public function commentDetail()
    {
        //用买家id和产品id关联mallbuilder_product_order_pro查询出每个买家买的商品的规格样式
        $type = intval($_GET['type']);
        $id = intval($_GET['id']);//产品id
        $comment = M('product_comment','mallbuilder_','DB_MALLBUILDER');
        //还是要请求计算一次评论数
        $result['num'] = $this->commentCount($comment,$id);
        //查询条件
        $conditions['a.con'] = array('neq','');
        $conditions['a.pid'] = array('eq',$id);
        if($type == 0)
        {
            $conditions['a.goodbad'] = array('in','1,2,3');
        }
        else
        {
            // 1=>差评   2=>中评   3=>好评
            $conditions['a.goodbad'] = array('eq',$type);
        }
        $field = 'a.fromid,a.pname ,a.pid,a.puid,a.uptime,a.goodbad,a.con,b.spec_name,b.spec_value,c.userid,c.user,c.logo';
        //查询的时候用order_id关联product_order_pro表
        $result['comInfo'] = $comment->alias('a')->field($field)->join('left join mallbuilder_product_order_pro b on a.order_id = b.order_id and a.fromid = b.buyer_id')->join('left join mallbuilder_member c on a.fromid = c.userid')->where($conditions)->order('a.uptime desc')->select();
        foreach($result['comInfo'] as $key=>$val)
        {
            $result['comInfo'][$key]['spec'] = $this->handleResult($val);
            $result['comInfo'][$key]['date'] = date('Y-m-d',$val['uptime']);
        }
        /*-----------产品规格----------*/
        $setmeal = M('product_setmeal','mallbuilder_','DB_MALLBUILDER');
        $list['porperty'] = $setmeal->where("pid = ".$id." and property_value_id !=''")->select();
        $specArr = $this->productSpec($list['porperty']);
        $result['setmeal'] = $specArr['setmeal'];
        $result['spec_val'] = $specArr['spec_val'];
        $result['spec'] = $specArr['spec'];
        /*-----------产品规格----------*/
        $this->switchJson($result);
    }
    /*-------------------------评论详情 END---------------------*/

    /*-------------------------商品筛选和搜索-----------------------------*/
    /*
     * 商品筛选和搜索
     */
    public function search()
    {
        $product = M('product','mallbuilder_','DB_MALLBUILDER');
        $shop = M('shop','mallbuilder_','DB_MALLBUILDER');
        $con = array();

        if($_GET['search_type'])
        {
            if($_GET['search_type'] == 1)
            {
                //同人铺
                $this->business($_GET['name'],1,$_GET['userid']);
            }
            elseif($_GET['search_type'] == 2)
            {
                //商业店
                $this->business($_GET['name'],2,$_GET['userid']);
            }
            elseif($_GET['search_type'] == 3)
            {
                //定制厂
                $field="userid id,company,main_pro,logo";//主键，店铺名称，主营产品
                $condition['company'] = array('like',"%".$_GET['name']."%");
                $shop_result['Info'] = $shop->field($field)->where($condition)->select();
                $this->switchJson($shop_result);
                die;
            }
            else
            {
                //商品
                $con['p.name'] = array('like',"%".$_GET['name']."%");
                $con['p.original'] = array('like',"%".$_GET['name']."%");
                $con['ps.setmeal'] = array('like',"%".$_GET['name']."%");//套餐表中setmeal作为圈子名称字段
                $con['p.roles'] = array('like',"%".$_GET['name']."%");
                $con['_logic'] = 'OR';
                $cons['_complex'] = $con;
            }
        }
        //商品排序
        if($_GET['filterType'])
        {
            if($_GET['filterType'] == 'clicks')
            {
                //人气排序
                $order = 'p.clicks desc';
            }
            else if($_GET['filterType'] == 'sort_asc')
            {
                //价格从低到高
                $order = 'p.price asc';
            }
            else if($_GET['filterType'] == 'sort_desc')
            {
                //价格从高到低
                $order = 'p.price desc';
            }
            else if($_GET['filterType'] == 'sales')
            {
                $order = 'p.sales desc';
            }
        }
        else
        {
            //人气排序
            $order = 'p.clicks desc';
        }

        if($_GET['shaixuan'])
        {
            //店铺类型 值跟数据库字段值对应（不需要组合）
            if($_GET['shaixuan']['typeShop'])
            {
                $cons['s.shop_type'] = array('in',$_GET['shaixuan']['typeShop']);
            }
            if($_GET['shaixuan']['typeTR'])
            {
                $cons['p.circletype'] = array('in',$_GET['shaixuan']['typeTR']);
            }
            if($_GET['shaixuan']['typeFavorite'])
            {
                $cons['p.twotype'] = array('in',$_GET['shaixuan']['typeFavorite']);
            }
            if($_GET['shaixuan']['chooseFoodsId'])
            {
                $cons['p.catid'] = array('in',$_GET['shaixuan']['chooseFoodsId']);
            }
        }
        //商品分类id
        if($_GET['cateid'])
        {
            $cons['p.catid'] = $_GET['cateid'];
        }
        //进入店铺搜索，加上该店铺的id
        if($_GET['shopid'])
        {
            $cons['s.userid'] = $_GET['shopid'];
        }
        $field = 'p.id,p.catid,p.member_id,p.name,p.price,p.pic,p.twotype,p.original,ps.setmeal circlename,s.shop_type';
        $pageSize = 10;
        $page = $_GET['page']?$_GET['page']:1;
        //总页数
        $page2 = $product->field($field)->alias('p')->join('LEFT JOIN mallbuilder_shop s ON  s.userid = p.member_id')->join('LEFT JOIN mallbuilder_product_setmeal ps ON ps.pid = p.id ')->where($cons)->count();
        $countPage = ceil($page2/$pageSize);
        $result['countPage'] = $countPage;
        if($page>1)
        {
            $limit = ($page-1)*$pageSize;
        }
        else
        {
            $limit = 0;
        }
        //店铺表关联商品表查询
        $result['Info'] = $product->field($field)->alias('p')->join('LEFT JOIN mallbuilder_shop s ON  s.userid = p.member_id')->join('LEFT JOIN mallbuilder_product_setmeal ps ON ps.pid = p.id ')->where($cons)->order($order)->limit($limit,$pageSize)->select();
        $this->switchJson($result);
    }

    /*-------------------------商品筛选和搜索 END-----------------------------*/

    /*-------------------------店铺信息-------------------------*/
    public function shopInfo()
    {
        $shop = M('shop','mallbuilder_','DB_MALLBUILDER');
        $shopId = $_GET['shopId'];
        $condition['userid'] = array('eq',$shopId);
//        $conditions = $con?$con:$condition;
        $field = "userid,user,company,shop_collect,shop_type,main_pro,FROM_UNIXTIME(create_time,'%Y-%m-%d') time,area,logo";
        $result['Info'] = $shop->field($field)->where($condition)->select();
        //查询该店铺是否被收藏
        $snsShop = M('sns_shareshop','mallbuilder_','DB_MALLBUILDER');
        $shopCollect = $snsShop->where('shopid = '.$shopId.' and uid = '.$_GET['uid'])->find();
        if($shopCollect)
        {
            $result['Info'][0]['collect'] = 1;
        }
        else
        {
            $result['Info'][0]['collect'] = 0;
        }
        $this->switchJson($result);
    }
    // 店铺商品
    public function shopBaby()//$con=null,$searchType=null,$sort=null
    {

        $product = M('product','mallbuilder_','DB_MALLBUILDER');
        $field = 'id,name,member_id,pic,brand,price,custom_cat_id,roles,circlename,original,pic';
//        if($con)
//        {
//            //搜索
//            $conditions = $con;
//            $limit = null;
//            $pageSize = null;
//            $order2 = $sort;
//        }
//        else
//        {

        if($_GET['shop_type'] != 3)
        {
            //根据四种状态进行商品的查询    1 => 销量  2=> 新品  3 =>价格（根据sort字段进行价格的高低查询）
            $order2 = $this->orderWay($_GET['type'],$_GET['sort']);
            $conditions['is_shelves'] = 1;//上架
            if(!$_GET['cateId'])
            {
                if($_GET['type'] == 2)
                {
                    //只推荐最近一个月的新品
                    $conditions['uptime'] = array('between',$this->timeBucket(2));//获取最近一个月的时间
                }
            }
            else
            {
                $conditions['custom_cat_id'] = $_GET['cateId'];
            }
            $conditions['member_id'] = $_GET['shopId'];
            $pageSize = 50;
            $page = $_GET['page']?$_GET['page']:1;
            //总页数
            $countPage = ceil($this->countNum($product,$conditions)/$pageSize);
            $newArr['countPage'] = $countPage;
            if($page>1)
            {
                $limit = ($page-1)*$pageSize;
            }
            else
            {
                $limit = 0;
            }
        }
        else
        {
            //定制厂
            $conditions['member_id'] = $_GET['shopId'];
        }

//        }
        $newArr['Info'] = $this->selectProduct($product,$field,$conditions,$limit,$pageSize,$order2);

//        if($searchType)
//        {
//            $newArr['type'] = $searchType;
//        }
        $this->switchJson($newArr);
    }

    //新品上架（一个月内的商品并按每天显示商品）
    public function new_product()
    {
        $product = M('product','mallbuilder_','DB_MALLBUILDER');
        $latestMonth = $this->timeBucket(2);
        $sql = 'select date_format(from_unixtime(uptime),"%m'.月.'%d'.日.'") as days,`name`,brand,keywords,price,pic from mallbuilder_product where is_shelves=1 and member_id='.$_GET['shopId'].' and uptime between '.$latestMonth[0].' and '.$latestMonth[1];
        $result = $product->query($sql);

        //组装数组（将时间作为数组的第一项，将当前时间的产品作为数组的第二项）
        foreach($result as $key=>$val)
        {
            //先循环将时间拿出来(去重复)
            $time[] = $val['days'];
            foreach(array_unique($time) as $v)
            {
                //然后将时间作为键，当前时间内的商品作为值
                if($val['days'] == $v)
                {
                    $everyDay['data'][$v][] = $val;
                }
            }
        }
        $this->switchJson($everyDay);
    }

    /*
     * 商业店铺和当前店铺最新上架的3件商品（由于店铺搜索出来的也是要带商品，此方法也用于店铺的搜索，不带商品的话不用此方法）
     * @param  $search  搜索名称
     * @param  $type    店铺类型  1=>同人铺  2=>商业店  3=>定制厂
     * @param  $uid     用户id
     */
    public function business($search=null,$type=null,$uid=null)
    {
        $newType = $type?$type:$_GET['type'];
        $userid = $_GET['uid']?$_GET['uid']:$uid;
        if($newType == 2)
        {
            $condition ='shop_type = 2 ';//商业店字段为2
        }
        else
        {
            $condition ='shop_type = 1 ';//同人铺字段为1
        }
        if($search)
        {
            //搜索内容
            $condition .= 'and company like '."'".'%'.$search.'%'."'";
        }
        $product = M('product','mallbuilder_','DB_MALLBUILDER');
        $shop = M('shop','mallbuilder_','DB_MALLBUILDER');
        //先查询所有店铺和该店铺的总宝贝数（可放搜索条件）
        $shop_sql = $this->search_shop_sql($condition);

        $shop_result = $shop->query($shop_sql);

//        echo "<pre>";
//        print_r($shop_result);die;
        //查询当前用户收藏的店铺
        $collectShop = M('sns_shareshop','mallbuilder_','DB_MALLBUILDER');
        $userCollectedId = $collectShop->where('uid = '.$userid)->select();
//        dump($userCollectedId);die;
        if(!$shop_result)
        {
            $shop_result['Info'] = [];
            //没有店铺前台返回空
            $this->switchJson($shop_result);
            die;
        }
        $result['type'] = $newType;
        $ids = '';
        foreach($shop_result as $key=>$val)
        {
            $ids .= $val['userid'].',';
            foreach($userCollectedId as $k=>$v)
            {
                if($val['userid'] == $v['shopid'])
                {
                    //用于查询页面，收藏按钮是否亮
                    $shop_result[$key]['collectStatus'] = 1;
                }
            }
        }
        if($newType == 2)
        {
            $sql = $this->latest_product_sql(trim($ids,','));
            $product_result = $product->query($sql);
            //拼接店铺和商品

            $result['Info'] = $this->spliceArr($shop_result,$product_result);
        }
        else
        {
            $result['Info'] = $shop_result;
        }
        $this->switchJson($result);
    }

    /**
     * //查询店铺和店铺的总宝贝数
     * @param $condition
     * @return sql;
     */
    function search_shop_sql($condition)
    {
        $sql = 'select a.userid,a.company,a.logo,a.main_pro,a.shop_collect,b.member_id,count(b.id)as allBaby from mallbuilder_shop a left join mallbuilder_product b on a.userid=b.member_id where '.$condition.' GROUP BY a.userid ';
        return $sql;
    }

    /**
     * //查询商品表中每个member_id 最新上架的商品(由于时间字段未确定，先用clicks字段)
     * @param   $ids
     * @return  sql
     */
    function latest_product_sql($ids)
    {
        $sql = 'select a.id,a.member_id,a.name,a.brand,a.keywords,a.pic from mallbuilder_product a where 3>(select count(*) FROM mallbuilder_product where member_id=a.member_id and clicks>a.clicks) AND a.member_id in('.$ids.') ORDER BY a.member_id';
        return $sql;
    }

    /*
     * 将两个二维数组进行组装
     * @param  $arr1  作为主数组
     * @param  $arr2  添加到主数组中作为分支数组
     */
    public function spliceArr($arr1,$arr2)
    {
        foreach($arr1 as $key=>$val)
        {
            foreach($arr2 as $k => $v)
            {
                if($val['userid'] == $v['member_id'])
                {
                    $arr1[$key]['product'][$k] = $v;
                }
                else
                {
                    continue;
                }
            }
        }
        return $arr1;
    }

    /*-------------------------店铺信息 END-------------------------*/

    /*-------------------------商品分类页---------------------------*/
    /*
     * 商品分类
     */
    public function cateList()
    {
        $catTable = M('product_cat','mallbuilder_','DB_MALLBUILDER');
        //先查出顶级分类
        $newArr['one'] = $catTable->field('catid,cat,pic')->where('catid < 9999')->order('catid')->select();
        if($_GET['fistLevel'])
        {
            $level = $_GET['fistLevel'];
        }
        else
        {

            $level = $newArr['one'][0]['catid'];
        }
        $newArr['twoAndMore'] = $this->childrenCate($level);
        $this->switchJson($newArr);

    }

    /**
     * 子级分类
     */
    public function childrenCate($level)
    {
        $catTable = M('product_cat','mallbuilder_','DB_MALLBUILDER');
        $s = $level.'00';
        $b = $level.'99';

        $result = $catTable->field('catid,cat')->where('catid between '.$s.' and '.$b)->order('catid')->select();
        if($result)
        {
            foreach($result as $key=>$val)
            {
                $result[$key]['prevcatid'] = $level;
                if(count($val)>0)
                {
                    $result[$key]['three'] = $this->childrenCate($val['catid']);
                }
            }
            return $result;
        }
    }

    /*-------------------------商品分类页 END---------------------------*/

    /*-------------------------购物车---------------------------*/
    public function addCar()
    {
        $product_car = M('product_cart','mallbuilder_','DB_MALLBUILDER');
        if($_GET['setmealId'])
        {
            $conditions['spec_id'] = $_GET['setmealId'];
        }
        $conditions['product_id'] = $_GET['pid'];
        $conditions['buyer_id'] = $_GET['userid'];
        $conditions['seller_id'] = $_GET['shopId'];
        $quantity = $_GET['quantity']*1;
        $price = $_GET['price']*1;
        //表中是否存在该条商品的信息
        $isHaveProduct = $product_car->where($conditions)->find();

        if(!$isHaveProduct)
        {
            $conditions['create_time'] = time();
            $conditions['quantity'] = $quantity;
            $conditions['price'] = $price;
            //添加该商品的信息
            $result = $product_car->data($conditions)->add();
        }
        else
        {
            //更新该条商品的信息
            $data['quantity'] = $quantity+$isHaveProduct['quantity']*1;
//            $data['price'] = $price*$quantity+$isHaveProduct['price']*1;
            $data['create_time'] = time();
            $result = $product_car->where($conditions)->save($data);
        }
        if($result)
        {
            $newArr['Info'] = 1;
            $newArr['mes'] = '加入购物车成功';
        }
        else
        {
            $newArr['Info'] = 2;
            $newArr['mes'] = '加入购物车失败';
        }
        $this->switchJson($newArr);
    }

    /*
     * 购物车列表页
     */
    public function shopCarList()
    {
        $product_car = M('product_cart','mallbuilder_','DB_MALLBUILDER');
        $userid = $_GET['userid'];
        $condition['c.buyer_id'] = $userid;
        $field = 'c.id carid,c.spec_id,c.quantity,c.price,s.userid shopid,s.company,p.id pid,p.name,p.pic,p.is_virtual,m.id setmealid,m.setmeal,m.stock mstock,p.stock pstock';
        $newArr = $this->car_product_info($product_car,$condition,$field,$order=null);
        $like_possible = $this->doYouLike($userid,$num=10);

        $result['Info'] = $newArr;
        $result['like'] = $like_possible;
        $this->switchJson($result);
    }

    //ajax 修改数量加减
    public function upAndDown()
    {
        $car = M('product_cart','mallbuilder_','DB_MALLBUILDER');
        $id = $_GET['id'];
        $num = $_GET['num']*1;
        if($num<0)
        {
            $result['mes'] = 1;
            $this->switchJson($result);
            die;
        }
        $result = $car->where('id='.$id)->setField('quantity',$num);
        $this->switchJson($result);
    }

    //结算时将购物车中的产品id存到session中，立即购买同样如此
    public function saveSession()
    {
        if(isset($_SESSION['buy']))
        {
            unset($_SESSION['buy']);
        }
        if($_GET['jiesuan'])
        {
            //购物车结算
            $ids = implode(',',$_GET['ids']);
            $_SESSION['buy']['ids'] = $ids;
        }
        else
        {
            //立即购买
            $_SESSION['buy']['pid'] = $_GET['pid'];
            $_SESSION['buy']['shopId'] = $_GET['shopId'];
            $_SESSION['buy']['userid'] = $_GET['userid'];
            $_SESSION['buy']['quantity'] = $_GET['quantity'];
            $_SESSION['buy']['price'] = $_GET['price'];
            if($_GET['setmealId'])
            {
                $_SESSION['buy']['setmealId'] = $_GET['setmealId'];
            }
            $_SESSION['buy']['price'] = $_GET['price'];
        }
        $newArr['Info'] = 1;
        $this->switchJson($newArr);
    }

    /*删除购物车宝贝*/
    public function deleteCart()
    {
        $myLoader = M('product_cart','mallbuilder_','DB_MALLBUILDER');
        $conditions['id'] = $_GET['id'];
        //      $result['id'] = $conditions['id'];
        $result['addressList'] = $myLoader->where($conditions)->delete();
        $this->switchJson($result);
    }
    /*-------------------------购物车 END---------------------------*/

    /*-------------------------确认订单页面-------------------------*/
    public function confirmOrder()
    {
        if($_SESSION['buy']['ids'])
        {
            $product_car = M('product_cart','mallbuilder_','DB_MALLBUILDER');
            $ids = $_SESSION['buy']['ids'];

            $field = 'c.id carid,c.quantity,c.price,c.seller_id,c.spec_id,s.userid shopid,s.company,p.freight_id,p.catid pcatid,p.freight_type,p.id pid,p.is_virtual,p.name,p.pic,p.stock pstock,m.id setmealid,m.setmeal,m.spec_name setmealname,m.stock mstock';

            $condition['c.id'] = array('in',$ids);
            $newArr = $this->car_product_info($product_car,$condition,$field,$order=1);
            $result['Info'] = $newArr;
            $result['pro_ids'] = $this->pro_ids;
            $result['setmeal_ids'] = $this->setmeal_ids;
            $result['car_ids'] = $_SESSION['buy']['ids'];
            $result['isCar'] = 1;
        }
        else
        {
            //立即购买
            $result = $this->BuyItNow();
            $result['isCar'] = 2;
        }
		$myLoader = M('delivery_address','mallbuilder_','DB_MALLBUILDER');
        $condition2['userid'] = $_GET['userid'];
        if($_GET['addressId'])
        {
            $condition2['id'] = $_GET['addressId'];
        }
        else
        {
            $condition2['default'] = 2;
        }

        $field2 = 'name,mobile,area,address';
        $result['addressList'] = $myLoader->field($field2)->where($condition2)->find();
//		echo $myLoader->_sql();die;
        $this->switchJson($result);
    }

    //立即支付
    function payPromptly()
    {
        $order = M('product_order','mallbuilder_','DB_MALLBUILDER');
        $order_pro = M('product_order_pro','mallbuilder_','DB_MALLBUILDER');
        $product = M('product','mallbuilder_','DB_MALLBUILDER');
        $product_mel = M('product_setmeal','mallbuilder_','DB_MALLBUILDER');
        $result = $this->passStock($_POST,$product,$product_mel);
        $info = 1;
        if(empty($result['Info']))
        {
            $info = 2;
            $this->switchJson($info);
            die;
        }
//        echo "<pre>";
//        print_r($_POST);die;
        $consignee_address = $result['address'];
        $consignee_mobile = $result['consignee_mobile'];
        $consignee = $result['consignee'];
        $buyer_id = $result['buyerid'];
        $Info = array_values($result['Info']);
        //先查询order表里面最新一条数据的id，将id和当前时间拼接作为订单号
        $latestId = $order->field('id')->order('id desc')->find();
        if(!$latestId)
        {
            $latestId['id'] = 1;
        }
        $order_sql = "INSERT INTO mallbuilder_product_order (id,userid,order_id,out_trade_no,buyer_id,seller_id,consignee,consignee_address,consignee_tel,consignee_mobile,product_price,logistics_type,logistics_price,logistics_name,invoice_no,invoice_title,payment_name,status,return_status,buyer_comment,seller_comment,des,create_time,payment_time,deliver_time,uptime,time_expand,voucher_price,discounts,is_virtual,dist_user_id,is_line,deduction) values";
        $pro_sql = "INSERT INTO mallbuilder_product_order_pro (id,order_id,buyer_id,pid,pcatid,name,pic,price,num,time,setmeal,spec_name,spec_value,status,freight,is_tg,is_gift,reason,discountRate,deduction,amount,taxRate,tax,taxAmount,des,is_offline,commission) values";
        $pro_update = 'UPDATE mallbuilder_product SET stock = CASE ';
        $update_setmeal = 'UPDATE mallbuilder_product_setmeal SET stock = CASE ';
        //产品id
        $update_ids = "";
        //库存id
        $update_setmealIds = "";
        $strtotime = time();
        $time = date('ymdHis',$strtotime);
        $countLen = count($Info);
        $issetmeal = 1;
        for($i=0;$i<$countLen;$i++)
        {
            $order_id = $time.($latestId['id']+1);
            //order  sql插入语句
            $order_sql .= " ('',{$buyer_id},{$order_id},null,0,{$Info[$i]['shopId']},"."'".$consignee."'".","."'".$consignee_address."'".",'',{$consignee_mobile},{$Info[$i]['proTotalMoney']},"."'".$Info[$i]['logistics_type']."'".",{$Info[$i]['logistics_price']},null,null,'',null,1,0,0,0,"."'".$Info[$i]['remark']."'".",{$strtotime},null,null,{$strtotime},10,0,0,{$Info[$i]['is_virtual']},0,0,0),";
            $order_sql .= " ('',{$Info[$i]['shopId']},{$order_id},null,{$buyer_id},0,"."'".$consignee."'".","."'".$consignee_address."'".",'',{$consignee_mobile},{$Info[$i]['proTotalMoney']},"."'".$Info[$i]['logistics_type']."'".",{$Info[$i]['logistics_price']},null,null,'',null,1,0,0,0,"."'".$Info[$i]['remark']."'".",{$strtotime},null,null,{$strtotime},10,0,0,{$Info[$i]['is_virtual']},0,0,0)";
            if($i < $countLen-1)
            {
                $order_sql.=',';
            }

            $count_pro = count($Info[$i]['detail']);
            for($j=0;$j<$count_pro;$j++)
            {
                //order_pro  sql插入语句

                if($Info[$i]['detail'][$j]['prosetmealId'] == 'null')
                {
                    $Info[$i]['detail'][$j]['prosetmealId'] = 0;
                }
                if($Info[$i]['detail'][$j]['prospec_name'] == 'null')
                {
                    $Info[$i]['detail'][$j]['prospec_name'] = " ";
                }
                if($Info[$i]['detail'][$j]['prospec_val'] == 'null')
                {
                    $Info[$i]['detail'][$j]['prospec_val'] = " ";
                }
                $pro_sql .= " ('',{$order_id},{$buyer_id},{$Info[$i]['detail'][$j]['proid']},{$Info[$i]['detail'][$j]['procatid']},"."'".$Info[$i]['detail'][$j]['proname']."'".","."'".$Info[$i]['detail'][$j]['propic']."'".",{$Info[$i]['detail'][$j]['proprice']},{$Info[$i]['detail'][$j]['pronum']},{$strtotime},{$Info[$i]['detail'][$j]['prosetmealId']},"."'".$Info[$i]['detail'][$j]['prospec_name']."'".","."'".$Info[$i]['detail'][$j]['prospec_val']."'".",1,0,false,0,null,0,0,0,0,0,0,null,0,0),";
                //产品库存  sql更新语句
                $pro_update .= " WHEN id={$Info[$i]['detail'][$j]['proid']} AND stock>0 THEN stock-{$Info[$i]['detail'][$j]['pronum']} ";
                $update_ids .= $Info[$i]['detail'][$j]['proid'].',';

                //规格表中库存  sql更新语句
                if($Info[$i]['detail'][$j]['prosetmealId'] != 0)
                {
                    $issetmeal+=1;
                    $update_setmeal .= " WHEN id={$Info[$i]['detail'][$j]['prosetmealId']} AND stock>0 THEN stock-{$Info[$i]['detail'][$j]['pronum']}";
                    $update_setmealIds .= $Info[$i]['detail'][$j]['prosetmealId'].',';
                }
            }
        }
        $pro_ids = rtrim($update_ids,',');
        $setmeal_ids = rtrim($update_setmealIds,',');
        $pro_update .= " ELSE false END where id in(".$pro_ids.")";
        //插入order表
        $result_order = $order->execute($order_sql);
        //插入order_pro表
        $result_order_pro = $order_pro->execute(rtrim($pro_sql,','));
        //更新产品总库存
        $result_product = $product->execute($pro_update);
        //更新规格库存
        if($issetmeal > 1)
        {
            $update_setmeal .= " ELSE false END where id in(".$setmeal_ids.")";
            $result_product = $product_mel->execute($update_setmeal);
        }

        //生成订单之后，要删除购物车表的数据，立即购买则不需要
        $product_car = M('product_cart','mallbuilder_','DB_MALLBUILDER');
        if($_SESSION['buy']['ids'])
        {
            $del_car = $product_car->delete($_SESSION['buy']['ids']);
        }
        $this->switchJson($info);
    }

    /*
     * 判断购买数量是否查出库存
     * @param  $data         订单数据
     * @param  $product      产品表
     * @param  $product_mel  产品规格表
     */
    function passStock($data,$product,$product_mel)
    {
        if(!$data)
        {
            return false;
        }
        //先查出产品的库存和套餐的库存
        if($data['setmeal_ids'] != "")
        {
            $stock_mel = $product_mel->field('id,stock')->where('id in('.$data['setmeal_ids'].')')->select();
        }
        $stock_pro = $product->field('id,stock')->where('id in('.$data['pro_ids'].')')->select();
        foreach($data['Info'] as $key=>$val)
        {
            foreach($val['detail'] as $k=>$v)
            {
                foreach($stock_pro as $ke=>$va)
                {
                    if($va['id'] == $v['proid'])
                    {
                        if($va['stock'] <= 0)
                        {
                            unset($data['Info'][$key]);
                        }
                    }
                }
                foreach($stock_mel as $a=>$b)
                {
                    if($b['id'] == $v['prosetmealId'])
                    {
                        if($b['stock'] <= 0)
                        {
                            unset($data['Info'][$key]);
                        }
                    }
                }
            }
        }
        return $data;
    }

    /*-------------------------确认订单页面 END-------------------------*/

    /**
     * 立即购买查询
     */
    public function BuyItNow()
    {
        $product = M('product','mallbuilder_','DB_MALLBUILDER');
        $result['pro_ids'] = $conditions['p.id'] = $_SESSION['buy']['pid'];
        $conditions['p.member_id'] = $_SESSION['buy']['shopId'];

//        $field = 'p.id,p.stock pstock,p.freight_id,p.freight_type,s.company,s.userid shopid';
        $field = 's.userid shopid,s.company,p.freight_id,p.catid pcatid,p.freight_type,p.id pid,p.is_virtual,p.name,p.pic,p.stock pstock';
        //先查询出该商品的基本信息和该商品的所属商铺
        $Info = $product->alias('p')->field($field)->join('left join mallbuilder_shop s on p.member_id = s.userid')->where($conditions)->find();
        if($Info['freight_type'] == 0)
        {
            $detail['yunfei'] = 0;
        }
        else
        {
            $userIp=convertip('218.79.42.255');//convertip(getip());
            if($userIp =='- LAN') $userIp = '上海';
            $detail['yunfei'] = $this->get_log_price($Info['freight_id'],$userIp);//,$type=2
        }

        if($_SESSION['buy']['setmealId'])
        {
            //查出该商品的规格和该规格的库存
            $setmeal = M('product_setmeal','mallbuilder_','DB_MALLBUILDER');
            $setmealInfo = $setmeal->field('id,setmeal,spec_name,stock')->where('id = '.$_SESSION['buy']['setmealId'])->find();
            $newquantity = $setmealInfo['stock'];
        }
        else
        {
            $newquantity = $Info['pstock'];
        }
        if($newquantity > $_SESSION['buy']['quantity'])
        {
            $Info['newquantity'] = $_SESSION['buy']['quantity'];
            //查出库存按最大库存算
            $detail['total'] = ($_SESSION['buy']['quantity']*1)*($_SESSION['buy']['price']*1)+$detail['yunfei'];
        }
        else
        {
            $Info['newquantity'] = $newquantity;
            $detail['total'] = ($newquantity*1)*($_SESSION['buy']['price']*1)+$detail['yunfei'];
        }

        $Info['price'] = $_SESSION['buy']['price'];
        $Info['mstock'] = $setmealInfo['stock'];
        $Info['setmeal'] = $setmealInfo['setmeal'];
        $Info['setmealname'] = $setmealInfo['spec_name'];
        $result['setmeal_ids'] = $Info['setmealid'] = $setmealInfo['id'];
        $detail['detail'][0] = $Info;
        $detail['name'] = $Info['company'];
        $detail['productNum'] = 1;

        if($Info['is_virtual'] == 1)
        {
            $detail['virtualNum'] = 1;
        }
        else
        {
            $detail['virtualNum'] = 0;
        }

        $result['Info'][0] = $detail;
        $result['Info'][0]['shopid'] = $Info['shopid'];
        $result['Info'][0]['isCar'] = 2;
        return $result;
    }

    /*
     * 查询购物车中的商品信息
     * @param  $product_car  数据表
     * @param  $condition    查询条件
     * @param  $field        限制字段
     */
    public function car_product_info($product_car,$condition,$field,$order)
    {
        $product_list = $product_car->alias('c')
            ->field($field)
            ->join('left join mallbuilder_shop s on c.seller_id = s.userid')
            ->join('left join mallbuilder_product p on s.userid = p.member_id AND c.product_id = p.id')
            ->join('left join mallbuilder_product_setmeal m on c.spec_id = m.id')
            ->where($condition)
            ->order('c.create_time desc')
            ->select();

        if($order == 1)
        {
            //----------------------------------用户区获取
            $userIp=convertip('218.79.42.255');//convertip(getip());
            if($userIp =='- LAN') $userIp = '上海';
            //----------------------------------跟据所在地自动算出的运费
            $pro_ids = '';
            $setmeal_ids = '';
            foreach($product_list as $key=>$val)
            {
                $pro_ids .= $val['pid'].',';
                if($val['setmealid'] != "")
                {
                    $setmeal_ids .= $val['setmealid'].',';

                }
                $shopArr[] = $val['seller_id'];
            }
            $this->pro_ids = rtrim($pro_ids,',');
            $this->setmeal_ids = rtrim($setmeal_ids,',');

            foreach(array_unique($shopArr) as $k=>$v)
            {
                $proNum = 0;
                $virtualNum = 0;
                foreach($product_list as $ke=>$va)
                {
                    if($v == $va['seller_id'])
                    {
                        if($va['freight_type'] == 0)
                        {
                            //卖家承担运费
                            $yunfeiArr[$va['seller_id']]['yunfei'][] = 0;
                        }
                        else
                        {
                            //买家承担运费
                            $yunfeiArr[$va['seller_id']]['yunfei'][] = $this->get_log_price($va['freight_id'],$userIp);//,$type=2
                        }
                        if(!$va['setmealid'])
                        {
                            $newquantity = $va['pstock'];
                        }
                        else
                        {
                            $newquantity = $va['mstock'];
                        }
                        if($newquantity > $va['quantity'])
                        {
                            $product_list[$ke]['newquantity'] = $va['quantity'];
                            //查出库存按最大库存算
                            $yunfeiArr[$va['seller_id']]['total'] += $va['quantity']*$va['price'];//每家商铺的总金额
                        }
                        else
                        {
                            $product_list[$ke]['newquantity'] = $newquantity;
                            if($newquantity*1 <= 0)
                            {
                                $yunfeiArr[$va['seller_id']]['total'] += 0;//每家商铺的总金额
                            }
                            else
                            {
                                $yunfeiArr[$va['seller_id']]['total'] += $newquantity*$va['price'];//每家商铺的总金额
                            }
                        }
                        $proNum++;
                        $yunfeiArr[$va['seller_id']]['productNum'] = $proNum;
                        if(intval($va['is_virtual']) == 1)
                        {
                            $virtualNum++;
                            $yunfeiArr[$va['seller_id']]['virtualNum'] = $virtualNum;
                        }
                    }
                    else
                    {
                        $proNum = 0;
                        $virtualNum = 0;
                    }
                }
            }

            //找出每家店的运费最高价
            foreach($yunfeiArr as $k=>$b)
            {
                $yunfeiArr[$k]['yunfei'] = max($b['yunfei']);
            }
        }
        foreach($product_list as $key=>$val)
        {
            if($val['shopid'] != "")
            {
                $newArr[$val['shopid']]['name'] = $val['company'];
                $newArr[$val['shopid']]['shopid'] = $val['shopid'];

                if($order == 1)
                {
                    foreach($yunfeiArr as $ke=>$va)
                    {
                        if($ke == $val['shopid'])
                        {
                            $newArr[$val['shopid']]['yunfei'] = $va['yunfei'];
                            $newArr[$val['shopid']]['total'] = $va['total']+$va['yunfei'];
                            $newArr[$val['shopid']]['productNum'] = $va['productNum'];
                            $newArr[$val['shopid']]['virtualNum'] = $va['virtualNum'];
                            $newArr[$val['shopid']]['isCar'] = 1;
                        }
                    }
                }
            }
            foreach($newArr as $k=>$v)
            {

                if($k == intval($val['shopid']))
                {
                    $newArr[$k]['detail'][] = $val;
                }
            }
        }

        return $newArr;
    }

    /**
     * 店铺内商品的排序方式
     * @param  int  $type  排序的方式
     * @param  int  $sort  价格排序
     */
    public function orderWay($type,$sort)
    {
        if($type)
        {
            if($type == -1)
            {
                //按点击量从高到低
                $order = 'clicks desc';
            }
            elseif($type == 1)
            {
                //销量
                $order = 'sales desc';
            }
            elseif($type == 2)
            {
                //新品
                $order = 'uptime desc';//暂定上架时间为uptime
            }
            else
            {
                //价格
                if($sort == 'desc')
                {
                    //价格从高到低
                    $order = 'price desc';
                }
                else
                {
                    //价格从低到高
                    $order = 'price asc';
                }
            }
        }
        return $order;
    }

    /*
     * 猜你喜欢
     * @param   $userid   用户id
     * @param   $typenum  查询的种类数
     * return   array
     */
    function doYouLike($userid,$typenum)
    {
        $guess_love = M('guess_love','mallbuilder_','DB_MALLBUILDER');
        $product = M('product','mallbuilder_','DB_MALLBUILDER');
        $cate_result = $guess_love->field('catid')->where('userid='.$userid)->order('createtime desc')->group('catid')->limit($typenum)->select();
        if(!$cate_result)
        {
            return false;
        }
        $catids = '';
        foreach($cate_result as $val)
        {
            $catids .= $val['catid'].',';
        }
        //查询出相应种类数的商品作为推荐商品
//        $condition['catid'] = array('in',trim($catids,','));
        $condition['is_shelves'] = 1;
        $sql = 'select a.id,a.member_id,a.catid,a.price,a.name,a.brand,a.keywords,a.pic from mallbuilder_product a where 2>(select count(*) FROM mallbuilder_product where catid=a.catid and id>a.id) AND a.catid in('.trim($catids,',').') ORDER BY a.catid';
        $finsh_result = $product->query($sql);
        return $finsh_result;
    }

    /**
     * 需要查询的时间段
     * @param  int  $cate  区分时间段   1=>最近一周   2=>最近一个月
     * return  $data  查询的时间条件
     */
    public function timeBucket($cate)
    {
        $data = array();
        $start = strtotime(date('y-m-d 00:00:00',time()));//当前日期的0点
        if($cate == 1)
        {
            //最近一周
            //先算出当日的0点，然后在减去7天，
            $end = $start-7*24*3600;
            //SELECT * FROM tablename where DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= date(时间字段名)
        }
        elseif($cate == 2)
        {
            $end = strtotime($this->latestMonthToday($start));
            //SELECT * FROM tablename where DATE_SUB(CURDATE(), INTERVAL 1 MONTH) <= date(时间字段名)
        }
        $data[0] = $end;//历史日期
        $data[1] = $start;//当前日期
        return $data;
    }

    /**
     * 计算上个月的今天，没有返回上个月的最后一天
     * @param   int  $time     当前日期的时间戳（0点）
     * @return  int  $lastTime 最近一个月的开始时间（日期格式）
     */
    public function latestMonthToday($time)
    {
        $last_month_time = mktime(date("G", $time), date("i", $time),date("s", $time), date("n", $time), 0, date("Y", $time));
        $last_month_t =  date("t", $last_month_time);//上个月最后一天的天数
        //date("j", $time)  当前日期的天数
        if ($last_month_t < date("j", $time))
        {
            return date("Y-m-t", $last_month_time);
        }
        return date(date("Y-m", $last_month_time) . "-d", $time);
    }

    /**
     * 查询总记录数
     * @param  $model      数据表
     * @param  $condition  查询条件
     */
    public function countNum($model,$condition)
    {
        $count = $model->where($condition)->count();
        return $count;
    }

    /**
     * 产品查询
     * @param     $model      数据表
     * @param     $field      筛选字段
     * @param     $condition  查询条件
     * @param     $limit      从第几个显示
     * @param     $pageSize   显示几条
     * @param     $order      排序
     */
    public function selectProduct($model,$field=null,$condition,$limit=null,$pageSize=null,$order=null)
    {
        $result = $model->field($field)->where($condition)->limit($limit,$pageSize)->order($order)->select();
        return $result;
    }

    /**
     * 处理规格结果
     * @param   $list   结果集
     */
    public function handleResult($list)
    {
        $spec_name = explode(',',$list['spec_name']);
        $spec_value = explode(',',$list['spec_value']);
        $spec = '';
        foreach($spec_name as $key=>$val)
        {
            $spec .= $val.':'.$spec_value[$key].'/';
        }
        $spec = trim($spec,'/');
        return $spec;
    }


    /**
     * 根据当前地址算出运费
     * @param  $lgid  运费
     * @param  $area  地址
     * @param  $type  1=>文字加价格  2=>纯价格
     * @return string
     */
    function get_log_price($lgid,$area)//,$type
    {
        $lgstempcon = M('logistics_temp_con','mallbuilder_','DB_MALLBUILDER');
        if(strlen($area)>6) $city=substr($area,6,strlen($area)-6);
        else $city=$area;
        $city=$city?','.$city:$city;
        $sql="select * from mallbuilder_logistics_temp_con where temp_id='$lgid' and define_citys like '%$city%' and logistics_type='mail'";
        $result = $lgstempcon->query($sql);
        if($result[0]['id'])
        {
            $str=$result[0]['default_price']?$result[0][default_price]:"";
            return $str;
        }
        if(empty($result[0]['id']))
        {	//没有为城市定价
            $sql="select * from mallbuilder_logistics_temp_con where temp_id='$lgid' and define_citys='default' and logistics_type='mail'";
            $result = $lgstempcon->query($sql);
            if($result)
            {
                $str=$result[0]['default_price']?$result[0][default_price]:"";
                return $str;
            }
        }

//        if($type == 1)
//        {
//            $str=$result[0]['default_price']?"平邮:".$result[0][default_price]."元 ":"";
//        }
//        else
//        {
//            $str=$result[0]['default_price']?$result[0][default_price]:"";
//        }

        $sql="select * from mallbuilder_logistics_temp_con where temp_id='$lgid' and define_citys like '%$city%' and logistics_type='express'";
        $result = $lgstempcon->query($sql);
        if(empty($result[0]['id']))
        {	//没有为城市定价
            $sql="select * from mallbuilder_logistics_temp_con where temp_id='$lgid' and define_citys='default' and logistics_type='express'";
            $result = $lgstempcon->query($sql);
            if($result)
            {
                $str=$result[0]['default_price']?$result[0][default_price]:"";
                return $str;
            }
        }

//        if($type == 1)
//        {
//            $str.=$result[0]['default_price']?"快递:".$result[0][default_price]."元 ":"";
//        }
//        else
//        {
//            $str=$result[0]['default_price']?$result[0][default_price]:"";
//        }

        $sql="select * from mallbuilder_logistics_temp_con where temp_id='$lgid' and define_citys like '%$city%' and logistics_type='ems'";
        $result = $lgstempcon->query($sql);
        if(empty($result[0]['id']))
        {	//没有为城市定价
            $sql="select * from mallbuilder_logistics_temp_con where temp_id='$lgid' and define_citys='default' and logistics_type='ems'";
            $result = $lgstempcon->query($sql);
            if($result)
            {
                $str=$result[0]['default_price']?$result[0][default_price]:"";
                return $str;
            }
        }
//        if($type == 1)
//        {
//            $str.=$result[0]['default_price']?"EMS:".$result[0][default_price]."元 ":"";
//        }
//        else
//        {
////            $str.=$result[0]['default_price']?$result[0][default_price]:"";
//            $str=$result[0]['default_price']?$result[0][default_price]:"";
//        }
//        return $str;
    }
    /**
     * 将数据转换成json传到前台
     * @param  array  $list  数据
     */
    function switchJson($list)
    {
        $this->ajaxReturn($list);
    }

    /*
     * 宝贝分类页面
     */
    public function babyCate()
    {
        //根据店铺将该店铺所有的自定义分类查询出来
        $custom = M('custom_cat','mallbuilder_','DB_MALLBUILDER');
        $conditions['userid'] = $condition['userid'] = $_GET['shopId']?$_GET['shopId']:1;//暂用1号店铺 页面改好 get获取
        $condition['pid'] = array('gt',0);
        $conditions['pid'] = 0;
        //查询顶级
        $AllCustom['cate'] = $custom->field('id,pid,name,userid')->where($conditions)->select();
        //查询所有的子级
        $AllPart = $custom->field('id,pid,name,userid')->where($condition)->select();
        $this->customCateArr = $AllPart;
        //将一级的分类和二级的分类组装成一个数组
        foreach($AllCustom['cate'] as $ke=>$va)
        {
            $newArr = $this->build_tree($va['id']);
            foreach($newArr as $k=>$v)
            {
                if($v['pid'] == $va['id'] )
                {
                    $AllCustom['cate'][$ke]['part'][] = $v;
                }
            }
        }
//        dump($AllCustom);
        $this->switchJson($AllCustom);
    }

    /*
     * 查询子级
     */
    function findChild(&$arr,$id){
        $childs=array();
        foreach ($arr as $k => $v){
            if($v['pid']== $id){
                $childs[]=$v;
            }
        }
        return $childs;
    }

    /*
     * 该方法可用来查询多级的分类（但现在只查询两级）
     */
    function build_tree($root_id){
        $rows = $this->customCateArr;//获取数组
        $childs=$this->findChild($rows,$root_id);
        if(empty($childs)){
            return null;
        }
        foreach ($childs as $k => $v){
            $rescurTree= $this->build_tree($v[id]);
            if( null != $rescurTree){
                $childs[$k]['childs']=$rescurTree;
            }
        }
        return $childs;
    }


    /*-----------------------------定制厂待修改-------------------*/
    /**
     * 进入定制厂
     */
    public function dingzhichang()
    {
        $model= M('shop','mallbuilder_','DB_MALLBUILDER');
        $field="userid id,company,main_pro,logo";//主键，店铺名称，主营产品
        $page = $_GET['page'];
        $page=$page?$_GET['page']:1;
        $condition['shop_type'] = 3;//3是定制厂
        $result['Info'] = $model->field($field)->where($condition)->page($page,10)->order('create_time desc')->select();//默认一页10条，按点击率降序排
        $this->switchJson($result);
    }
    /**
     * 定制厂的本子信息
     */
    public function printBook()
    {
        if($_GET['userid']==""){$result['msg']="yonghuid request";$this->switchJson($result);}//userid 进入定制厂的时候传过去的
        $model= M('shop s','mallbuilder_','DB_MALLBUILDER');

        $field="s.shop_collect,s.user,s.company,p.id,p.name,p.pic";//店铺收藏数量，店铺拥有者，店铺名，本子的主键，本子名称，本子图片（暂时就传名字图片）
        $condition['s.shop_type']=5;
        $condition['s.userid']=$_GET['userid'];
        $result = $model->field($field)->where($condition)->join('left join mallbuilder_product p on s.userid=p.member_id')->order('p.uptime desc')->select();
        $this->switchJson($result);
    }

    /**
     * 定制厂的厂商信息
     */
    public function dingzhichangInfo()
    {
        if($_GET['userid']==""){
            $result['msg']="yonghuid request";$this->switchJson($result);
        }//userid 进入定制厂的时候传过去的
        $model= M('shop','mallbuilder_','DB_MALLBUILDER');

        $field="shop_collect,user,company,main_pro,addr,create_time,shop_auth_pic";//店铺收藏数量，店铺拥有者，店铺名，厂商介绍，厂商地址，开业时间，厂商实景(拿的店铺认证图片)
        $condition['shop_type']=5;
        $condition['userid']=$_GET['userid'];
        $result = $model->field($field)->where($condition)->find();
        $this->switchJson($result);
    }
    /*-----------------------------定制厂-------------------*/

    
    /*-------------------------我的-------------------------*/
    /*个人资料页*/
    public function personalInformation()
    {
        $personal = M('member','mallbuilder_','DB_MALLBUILDER');
        $personal2 = M('product_order','mallbuilder_','DB_MALLBUILDER');
        $personalId = $_GET['userid'];
        $field = 'name,area,logo';
        $result['personal'] = $personal->field($field)->where('userid = '.$personalId)->find();
//		$sql = 'select count(status) count,status from mallbuilder_product_order where userid='.$personalId.' and status !=0 group by status';
		$sql2 = 'select count(return_status) count,return_status from mallbuilder_product_order where userid='.$personalId.' and return_status !=0 group by return_status';
        $sql = 'select sum(case status when -1 then 1 else 0 end) cancel_already,
                       sum(case status when 0 then 1 else 0 end) deal_close,
                       sum(case status when 1 then 1 else 0 end) wait_pay,
                       sum(case status when 2 then 1 else 0 end) wait_send,
                       sum(case status when 3 then 1 else 0 end) wait_confirm,
                       sum(case status when 4 then 1 and buyer_comment=0 else 0 end) wait_rate,
                       sum(case status when 5 then 1 else 0 end) returns,
                       sum(case status when 6 then 1 else 0 end) complete_returns
                       from mallbuilder_product_order';
		$result['statusInfo']['status'] = $personal2->query($sql);
		$result['statusInfo']['return_status'] = $personal2->query($sql2);
        //猜你喜欢
        $result['like'] = $this->doYouLike($personalId,$num=6);
		
        $this->switchJson($result);
    }

    /*
     * 订单列表
     */
    public function order_list()
    {
        $order = M('product_order','mallbuilder_','DB_MALLBUILDER');
        $order_pro = M('product_order_pro','mallbuilder_','DB_MALLBUILDER');
        $conditions = array();
        $conditions['o.userid'] = $_GET['userid'];
        $search_status = $_GET['search_status'];
        if($search_status == 'wait_pay')
        {
            //等待付款
            $conditions['o.status'] = 1;
        }
        elseif($search_status == 'wait_send')
        {
            //等待发货
            $conditions['o.status'] = 2;
        }
        elseif($search_status == 'wait_confirm')
        {
            //等待收货
            $conditions['o.status'] = 3;

        }
        elseif($search_status == 'wait_rate')
        {
            //已完成带评价
            $conditions['o.status'] = 4;
        }
        elseif($search_status == 'returns')
        {
            //退货
        }
//        else
//        {
//            //全部订单
//        }
//        print_r($conditions);die;
        //先查出当前用户的订单  seller_id = 0 为买家的订单
        $conditions['seller_id'] = 0;
        $order_field = 'o.id,o.order_id,o.userid,o.seller_id,o.product_price,o.logistics_type,o.logistics_price,o.status,o.buyer_comment,s.userid sid,s.company';
        $pro_field = 'id,order_id,buyer_id,pid,name pname,pic,price,num,setmeal,spec_name,spec_value,status,reason';
        $orderList = $order->alias('o')->field($order_field)->join('left join mallbuilder_shop s on o.seller_id=s.userid')->where($conditions)->select();
        $order_ids = "";
        foreach($orderList as $val)
        {
            $order_ids .= $val['order_id'].',';
        }
        $condition['order_id'] = array('in',trim($order_ids,','));
        $orderList_pro = $order_pro->field($pro_field)->where($condition)->select();
        $result = $this->handleOrder($orderList,$orderList_pro);
        $this->switchJson($result);
    }

    /**
     * 处理订单
     * @param  $list_o   订单结果集
     * @param  $list_p   产品订单结果集
     */
    public function handleOrder($list_o,$list_p)
    {
        if(!$list_o || !$list_p)
        {
            return false;
        }
        foreach($list_o as $key=>$val)
        {
            $newArr['Info'][] = $val;
            $newArr['Info'][$key]['statusInfo'] = $this->order_status($val['status']);
            $num = 0;
            foreach($list_p as $ke=>$va)
            {
                if($val['order_id'] == $va['order_id'])
                {
                    $num +=1;
                    $newArr['Info'][$key]['detail'][] = $va;
                    $newArr['Info'][$key]['count'] = $num;
                }
            }
        }
        return $newArr;
    }

    /*
     * 订单状态
     */
    public function order_status($status)
    {
        if(!$status)
        {
            return false;
        }
        $Info ="";
        switch($status)
        {
            case -1:
                $Info = '已经取消';
                break;
            case 0:
                $Info = '交易关闭';
                break;
            case 1:
                $Info = '待付款';
                break;
            case 2:
                $Info = '待发货';
                break;
            case 3:
                $Info = '待收货';
                break;
            case 4:
                $Info = '已完成';
                break;
            case 5:
                $Info = '等待退货';
                break;
            case 6:
                $Info = '退货完成';
                break;
        }
        return $Info;
    }

    /**
     * 取消订单
     */
    public function cancel_order()
    {
        $order = M('product_order','mallbuilder_','DB_MALLBUILDER');
        $order_pro = M('product_order_pro','mallbuilder_','DB_MALLBUILDER');
        $order_id = $_GET['order_id'];
        $conditions['reason'] = $_GET['reason'];
        //将订单表中状态更改为已经取消（-1）
        $condition['status'] = 0;
        $order_res = $order->where('order_id='.$order_id)->save($condition);
        //在产品订购表中写入取消原因
        $order_res = $order_pro->where('order_id='.$order_id)->save($condition);
        $this->switchJson($order_res);
    }
	
	/*电子票未使用*/
	public function electronicTicket1()
    {
		
    }
	
	/*收藏*/
	public function collectTR()
    {
        $collect = M('sns_shareshop','mallbuilder_','DB_MALLBUILDER');
        $shop = M('shop','mallbuilder_','DB_MALLBUILDER');
        $product = M('product','mallbuilder_','DB_MALLBUILDER');
        $userid = $_GET['userid'];
        $shop_type = $_GET['shop_type'];

        $userCollectedId = $collect->field('shopid')->where('uid = '.$userid)->select();
        if(!$userCollectedId)
        {
            $result['Info'] = null;
            $this->switchJson($result);
            die;
        }
        $shop_ids = "";
        foreach($userCollectedId as $val)
        {
            $shop_ids .= $val['shopid'].',';
        }
        $condition = ' userid in('.trim($shop_ids,',').')';
        if($shop_type == 2)
        {
            $condition .= ' and shop_type = 2';
            //官方店
            $shop_sql = $this->search_shop_sql($condition);
            $shop_result = $shop->query($shop_sql);
            $sql = $this->latest_product_sql(trim($shop_ids,','));
            $product_result = $product->query($sql);
            $result['Info'] = $this->spliceArr($shop_result,$product_result);
        }
        else if($shop_type == 3)
        {
            //定制厂
            $condition .= ' and shop_type = 3';
            $shop_sql = $this->search_shop_sql($condition);
            $result['Info'] = $shop->query($shop_sql);
        }
        else
        {
            $condition .= 'and shop_type = 1';
            $shop_sql = $this->search_shop_sql($condition);
            $result['Info'] = $shop->query($shop_sql);
        }
        $result['like'] = $this->doYouLike($userid,$num=10);

        $this->switchJson($result);
    }

    /*
     * 取消收藏
     */
	public function deteleAjax()
	{
	  	$collect = M('sns_shareshop','mallbuilder_','DB_MALLBUILDER');
		$conditions['shopid'] = $_GET['shopid'];
		$conditions['uid'] = $_GET['userid'];
		$result = $this->del_single($collect,$conditions);
		if($result)
		{
			echo 1;
		}
		else
		{
			echo 2;
		}
	}

    /*
     * 历史纪录   最新的100条
     */
    public function userHistory()
    {
        $history = M('guess_love','mallbuilder_','DB_MALLBUILDER');
        $userid = $_GET['userid'];
        $history_result['Info'] = $history->alias('h')
                                          ->field('h.id,h.pid,FROM_UNIXTIME(h.createtime),p.name,p.price')
                                          ->join('left join mallbuilder_product p on h.pid=p.id')
                                          ->where('userid='.$userid)
                                          ->order('createtime desc')
                                          ->select();
        $this->switchJson($history_result);
    }

    /*
     * 历史删除
     */
    public function del_history()
    {
        $history = M('guess_love','mallbuilder_','DB_MALLBUILDER');
        $con['id'] = $_GET['id'];
        $result['Info'] = $this->del_single($history,$con);
        $this->switchJson($result);
    }

    /**
     * 单表删除
     * @param   $table   数据表
     * @param   $con     条件
     */
    public function del_single($table,$con)
    {
        $result = $table->where($con)->delete();
        return $result;
    }

    /**
     * 评价管理
     */
    public function assessment_manage()
    {
        $userid = $_GET['userid'];
        $order = M('product_order','mallbuilder_','DB_MALLBUILDER');
        $comment = M('product_comment','mallbuilder_','DB_MALLBUILDER');
        //根据订单统计评论数
        $sql = 'select sum(case buyer_comment when 1 then 1 else 0 end ) already_com,
                       sum(case buyer_comment when 0 then 1 else 0 end ) wait_com 
                       from mallbuilder_product_order where buyer_id='.$userid;
        $list_sql = "select c.id cid,FROM_UNIXTIME(c.uptime,'%Y-%m-%d') createtime,c.pid,c.order_id,c.con,op.setmeal spec_id,op.pic,op.name from mallbuilder_product_comment c left join mallbuilder_product_order_pro op on c.order_id = op.order_id where c.userid =".$userid;
//        echo $list_sql;die;
        $result["Info"] = $comment->query($list_sql);
        $result['status'] = $order->query($sql);
        //查询已经评价的列表（暂且不包含超时默认评价）
        $this->switchJson($result);
    }
	
	/*地址管理*/
    public function addressManagement()
    {
        $myLoader = M('delivery_address','mallbuilder_','DB_MALLBUILDER');
        $myLoaderId = $_GET['userid'];
        $field = 'id,name,mobile,zip,address,default';
        $result['addressList'] = $myLoader->field($field)->where('userid = '.$myLoaderId)->select(); 	
        $this->switchJson($result);
    }
	

	/*筛选*/
    public function searchFilter()
    {
        $myLoader = M('product_cat','mallbuilder_','DB_MALLBUILDER');
        $field = 'catid,cat';
        $result['product'] = $myLoader->field($field)->where()->select();
        $this->switchJson($result);
    }


	/*设置默认*/
    public function setDefault()
    {
        $myLoader = M('delivery_address','mallbuilder_','DB_MALLBUILDER');
        $conditions['userid'] = $_GET['userid'];
		$conditions['default'] = 2;
        $conditions['id'] = array('neq',$_GET['id']);
        $isDefaultId = $_GET['id'];
		$isDefault = $_GET['isDefault'];
		if($isDefault == 1)
		{
			//默认
			$default = 2;
		}
		else
		{
			//不默认
			$default = 1;
		}
//		$isNotDefaultId = $_GET['Default'];

		//将当前点击的地址设置为默认
		$setDefault = $myLoader->where('id = '.$isDefaultId)->setField('default',$default);
		$offDefault = $myLoader->where($conditions)->setField('default',1);
		if($offDefault !== false)
		{
			$info['error'] = 1;
		}
		else
		{
			$info['error'] = 2;
		}
        $this->switchJson($info);
    }

	/*删除地址*/
    public function deleteAddress()
    {
        $myLoader = M('delivery_address','mallbuilder_','DB_MALLBUILDER');
        $conditions['id'] = $_GET['id'];
		$array = $myLoader->field('userid')->where($conditions)->find();
        $result['userid'] = $array['userid'];
        $result['addressList'] = $myLoader->where($conditions)->delete();
        $this->switchJson($result);
    }


   
	/*编辑地址*/
	public function addressEdit()
    {
        $myLoader = M('delivery_address','mallbuilder_','DB_MALLBUILDER');
        $myLoaderId = $_GET['id'];
        $field = 'id,name,mobile,zip,address,default,provinceid,cityid,areaid,area';
        $result['addressList'] = $myLoader->field($field)->where('id = '.$myLoaderId)->find();
//		echo $myLoader->_sql();die;
        $this->switchJson($result);
    }

    /**
     * 保存意见反馈
     */
    public function save_feedback()
    {
        $feedback = M('feedback','mallbuilder_','DB_MALLBUILDER');
        $condition['userid'] = $_POST['userid'];
        if(!empty($_POST['pic']))
        {
            $condition['imgs'] = $_POST['pic'];
        }
        $condition['con'] = $_POST['con'];
        //查询有没有相同的数据
        $isHave = $feedback->where($condition)->select();
        if($isHave)
        {
            $mes['status'] = 2;
            $this->switchJson($mes);
            die;
        }
        $result = $feedback->data($condition)->add();
        if($result)
        {
            $mes['status'] = 1;
            $this->switchJson($mes);
        }
    }

    /*图片上传 （暂用于意见反馈）*/
    public function upload_pic_ajax()
    {
        $typeArr = array("jpg", "png", "gif");//允许上传文件格式
        $path = SITE_PATH."html/mobile/img/feedback/";//上传路径

        if (isset($_POST)) {
            $name = $_FILES['file']['name'];
            $size = $_FILES['file']['size'];
            $name_tmp = $_FILES['file']['tmp_name'];
            if (empty($name)) {
                echo json_encode(array("error"=>"您还未选择图片"),JSON_UNESCAPED_UNICODE);
                exit;
            }
            $type = strtolower(substr(strrchr($name, '.'), 1)); //获取文件类型

            if (!in_array($type, $typeArr)) {
                echo json_encode(array("error"=>"请上传jpg,png或gif类型的图片！"),JSON_UNESCAPED_UNICODE);
                exit;
            }
            if ($size > (2000 * 1024)) {
                echo json_encode(array("error"=>"图片大小已超过2M！"),JSON_UNESCAPED_UNICODE);
                exit;
            }

            $pic_name = time() . rand(10000, 99999) . "." . $type;//图片名称
            $pic_url = $path . $pic_name;//上传后图片路径+名称
//            echo "<pre>";
//            print_r($pic_url);die;
            if (move_uploaded_file($name_tmp, $pic_url)) { //临时文件转移到目标文件夹
                    echo json_encode(array("error"=>"0","pic"=>$pic_url,"name"=>$pic_name));
            } else {
                echo json_encode(array("error"=>"上传有误，清检查服务器配置！"),JSON_UNESCAPED_UNICODE);
            }
        }
    }

	
    //解析省市级json
//	public function LAreaData1()
//	{
//		$myLoader = M('district','mallbuilder_','DB_MALLBUILDER');
//        $field = 'id,name';
//        $result = $myLoader->field($field)->select();
//        foreach($result as $key=>$v)
//        {              
//            $items[$key+1] = $v;
//        }
//
//        $tree = array();
//        foreach($items as $item){
//            if(isset($items[$item['pid']])){
//                $items[$item['pid']]['child'][] = &$items[$item['id']];
//
//            }else{
//                $tree[] = &$items[$item['id']];
//            }
//        }
//        echo json_encode($tree);die;
//	}

	public function getAreaData()
	{
		$type = I("get.type");
		switch($type)
		{
			case 0:
				$myLoader = M('district','mallbuilder_','DB_MALLBUILDER');
        		$field = 'id,name';
				$id = $_GET['id'] ? $_GET['id'] :0 ;
        		$result['addressList'] = $myLoader->field($field)->where('pid='.$id)->select();
				$items =$result['addressList'];
				$tree = array();
				
				
				foreach($items as $item){
            		$item['child'] = '[{}]';
					array_push($tree,$item);
				}	

				exit(json_encode($tree));
				//break;
		}
	}

	/*地址保存*/
    public function addressSave()
    {
        $myLoader = M('delivery_address','mallbuilder_','DB_MALLBUILDER');
    	if( $_GET['id'] ){
            //编辑
    		$myLoaderId = $_GET['id'];
	        $conditionsDefault['id'] = array('neq',$_GET['id']);
    	}
		else
        {
            //添加
			$conditions['userid'] = $_GET['userid'];
		}
	    $conditionsDefault['userid'] = $_GET['userid'];
		$conditionsDefault['default'] = $conditions['default'] = $_GET['default'];
		$conditions['name'] = $_GET['name'];
		$conditions['mobile'] = $_GET['mobile'];
		$conditions['area'] = $_GET['area'];
		$conditions['address'] = $_GET['address'];
		$conditions['zip'] = $_GET['zip'];
		$conditions['provinceid'] = $_GET['provinceid'];
		$conditions['cityid'] = $_GET['cityid'];
		$conditions['areaid'] = $_GET['areaid'];
		if( $_GET['id'] ){
			$setDefault = $myLoader->where('id = '.$myLoaderId)->data($conditions)->save();//where('id = '.$myLoaderId)
		}
		else
        {
			$setDefault = $myLoader->data($conditions)->add();//where('id = '.$myLoaderId)
        	$conditionsDefault['id'] = array('neq',$setDefault);
		}
		$offDefault = $myLoader->where($conditionsDefault)->setField('default',1);
		if($offDefault !== false)
		{
//			$myLoader->commit();
			$info['error'] = 1;
			$info['userid'] = $conditionsDefault['userid'];
		}
		else
		{
//			$myLoader->rollback();
			$info['error'] = 2;
		}
        $this->switchJson($info);
    }

	/*店铺介绍*/
    public function introduction()
    {
        $myLoader = M('shop','mallbuilder_','DB_MALLBUILDER');
        $myLoaderId = $_GET['userid'];
        $field = "s.company,s.logo,s.main_pro,s.shop_collect,count(p.id) allBaby,s.area,s.stime";
//      $result['introduction'] = $myLoader->alias('c')->field($field)->join('left join mallbuilder_product p on c.userid = p.member_id')->where('userid = '.$myLoaderId)->find();
		$sql =  'select '.$field.' from mallbuilder_shop s left join mallbuilder_product p on s.userid = p.member_id where s.userid = '.$myLoaderId.' limit 1';
		$sql2 = 'select count(id) as countNum,sum(case when goodbad=1 then 1 end) bad,sum(case when goodbad=2 then 1 end) middle,sum(case when goodbad=3 then 1 end) good from mallbuilder_product_comment where puid = '.$myLoaderId.' and goodbad>0 limit 1';
		$result['introduction'] = $myLoader->query($sql);
		$result['introduction'][0]['ymd'] = $result['introduction'][0]['stime']?date('Y-m-d',$result['introduction'][0]['stime']):"";
		$result['myComment'] = $myLoader->query($sql2);
		for( $x = 0 ; $x < 4 ; $x++ ){
			$sqlItem[$x] = 'select count(id) as countNum,sum(case when item'.($x+1).'=1 then 1 end) star1,sum(case when item'.($x+1).'=2 then 2 end) star2,sum(case when item'.($x+1).'=3 then 3 end) star3,sum(case when item'.($x+1).'=4 then 4 end) star4,sum(case when item'.($x+1).'=5 then 5 end) star5 from mallbuilder_user_comment where userid = '.$myLoaderId.' limit 1';
			$array[$x] = $myLoader->query($sqlItem[$x]);
			$result['myComment'][0]['item'.($x+1).''] = $array[$x][0]['countnum'] ? sprintf("%.1f", ($array[$x][0]['star1'] + $array[$x][0]['star2'] + $array[$x][0]['star3'] + $array[$x][0]['star4'] + $array[$x][0]['star5'] )  / $array[$x][0]['countnum'] ) : 0;
		}
		
		//查询该店铺是否被收藏
        $snsShop = M('sns_shareshop','mallbuilder_','DB_MALLBUILDER');
        $shopCollect = $snsShop->where('shopid = '.$myLoaderId.' and uid = '.$_GET['uid'])->find();
        if($shopCollect)
        {
            $result['collect'] = 1;
        }
        else
        {
            $result['collect'] = 0;
        }
        $this->switchJson($result);
    }

	/*收藏操作*/
    public function isCollect()
    {
        $myLoader = M('sns_shareshop','mallbuilder_','DB_MALLBUILDER');
        $myLoader2 = M('shop','mallbuilder_','DB_MALLBUILDER');
        $isCollect = $_GET['collect'];
		$collect['shopid'] = $_GET['shopid'];
		$collect['uid'] = $_GET['userid'];
		$add['shopid'] = $_GET['shopid'];
		$add['shopname'] = $_GET['shopname'];
		$add['uid'] = $_GET['userid'];
		$add['uname'] = $_GET['uname'];
		$add['addtime'] = time();
        $field = 'count(id) collected';
		$collected = $myLoader->field($field)->where($collect)->find();
//        echo $myLoader->_sql();die;
		$num = 0;
		if($isCollect){
			if(!$collected['collected']){
				$num = 1;
				$result['add'] = $myLoader->data($add)->add();
			}
		}
		else{
			if($collected['collected']){
				$num = -1;
				$result['delete'] = $myLoader->where($collect)->delete();
			}
		}
		if($num == 1){
			$result['num'] = $myLoader2->where('userid='.$add['shopid'])->setInc('shop_collect');
		}
		else if($num == -1){
			$result['num'] = $myLoader2->where('userid='.$add['shopid'])->setDec('shop_collect');
		}
		//查询当前店铺的关注人数
        $attenNum = $myLoader2->field('shop_collect')->where('userid='.$add['shopid'])->find();
        $result['attenNum'] = $attenNum['shop_collect'];
        $result['collect'] = $isCollect;
        $result['collected'] = $collected['collected'];
        $this->switchJson($result);
    }



























}






















