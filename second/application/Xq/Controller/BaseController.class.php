<?php
namespace Xq\Controller;
use Common\Controller\HomebaseController;
class BaseController extends HomebaseController
{
    //后台登陆界面
    public function login()
    {
        if(isset($_SESSION['ADMIN_ID']))
        {//已经登录
            $this->success('登录成功',U("Admin/index"));
        }
        else
        {
            $this->display(":Admin/login");
        }
    }

    public function logout()
    {
        session('ADMIN_ID',null);
        $this->redirect("base/login");
    }


    public function dologin()
    {
        $name = I("post.username");
        $pass = I("post.password");
        $where['user_login']=$name;
        $user = D("Common/Users");
        $result = $user->where($where)->find();
        if(!empty($result) && $result['user_type']==1){
            if(sp_compare_password($pass,$result['user_pass']))
            {
                //登入成功页面跳转
                $_SESSION["ADMIN_ID"]=$result["id"];
                $_SESSION['name']=$result["user_login"];
                $result['last_login_ip']=get_client_ip(0,true);
                $result['last_login_time']=date("Y-m-d H:i:s");
                $user->save($result);
                setcookie("admin_username",$name,time()+30*24*3600,"/");
                $this->success('登录成功',U("Admin/index"));
            }
            else
            {
                $this->error('账户密码错误');
            }
        }
    }
}