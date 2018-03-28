<?php
/**
 * Created by MY_MIND.
 * User: WDJ
 * Date: 2018/3/27 0027
 * Time: 17:21
 */
namespace Xadmin\Controller;

use Think\Controller;
use Xadmin\Model\AdminModel;

class LoginController extends BaseController
{
    /**
     * 空操作的话直接跳转首页
     */
    public function _empty()
    {
        redirect(U('login'));
    }
    /**
     * 登录操作
     * 成功后直接跳转
     */
    public function login()
    {
        if(IS_POST){
            /**
             * 调用模型中的方法判断是否成功
             */
            $admin  =   new AdminModel();
            list($flag , $message) = $admin ->adminLogin();
            if($flag){
                $this->success($message, U('Index/statistics'));
            }else{
                $this->error($message, U('Login/login'));
            }
        }else{
            $this->display('login');
        }
    }

    /**
     * 退出操作
     */
    public function logout()
    {
        session('adminId', null);
        session('adminAccount', null);
        session('rememberPassword', null);

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }

        $this->success('退出成功！', U('Login/login'));
    }
}