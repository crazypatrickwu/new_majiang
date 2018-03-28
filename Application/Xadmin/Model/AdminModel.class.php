<?php
/**
 * Created by MY_MIND.
 * User: WDJ
 * Date: 2018/3/27 0027
 * Time: 17:30
 */
namespace Xadmin\Model;

use Common\Model\BottomModel;

class AdminModel extends BottomModel
{
    /*
     *  初始化 开启session
     */
    public function _initialize()
    {
        parent::_initialize();
        session_start();
    }

    /**
     * 管理员登录
     */
    public function adminLogin()
    {
        // 记住密码
        $rememberPassword   =   $this->post['rememberPassword'];
        if ($rememberPassword == '1') {
            $nextWeekTime = 3600 * 24 * 7;
            session_cache_expire($nextWeekTime / 60);
            session_set_cookie_params($nextWeekTime);
        }
        $account    =   $this->post['account'];
        $password   =   $this->post['password'];
        // 采用系统加密
        $password = think_ucenter_md5($password);

        $admin = M('admin')->field('id, is_lock, admin_account')->where(array('admin_account' => $account, 'admin_password' => $password))->find();
        if ($admin['is_lock'] == '1') {
            return ['false' , '账户被锁定'];
        }

        if (!empty($admin)) {
            session('adminId', $admin['id']);
            session('adminAccount', $admin['admin_account']);
            if ($rememberPassword == '1') {
                session('rememberPassword', 1);
            }

            return ['true' , '登录成功'];
        } else {
            return ['false' , '密码或用户出错'];
        }
    }

    /**
     *
     */
}