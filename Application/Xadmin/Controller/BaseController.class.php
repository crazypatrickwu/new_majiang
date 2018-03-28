<?php
/**
 * Created by MY_MIND.
 * User: WDJ
 * Date: 2018/3/27 0027
 * Time: 17:13
 */
namespace Xadmin\Controller;

use Common\Controller\BottomController;

class BaseController extends BottomController
{
    //登录用户的信息
    protected $admin_info;
    /**
     * 对基类控制器进行一些初始化
     * 视图主题、权限认证
     */
    public function _initialize()
    {
        parent::_initialize();
//        cookie('referer' , __SELF__  , 3600);
        //判断是否是wap
        if(iswap()){
            C('DEFAULT_THEME','wap');
        }else{
            C('DEFAULT_THEME','default');
        }
        if(CONTROLLER_NAME != 'Login'){
            if(!$this->isLogin()){
                header('LOCATION:' . U('Login/login'));
                exit();
            }else{
                $this->admin_info   =   M('admin')->where(['is_lock' => 0 , 'id' => session('adminId')])->find();
                $this->getAuth();
                $getAuthMenu = $this->getAuthMenu();
                //顶部菜单
                $this->admin_menu_list = $getAuthMenu['admin_menu_list'];
                //左侧菜单
                $this->left_menu = $getAuthMenu['left_menu'];
            }
        }

    }

    /**
     * 判断是否登录
     */
    protected function isLogin()
    {
        $adminId 	  = session('adminId');         // 管理员用户id
        $adminAccount = session('adminAccount');    // 管理员用户名

        // 其他条件
        $otherCondition = !in_array(CONTROLLER_NAME , array('Login'));
        if ( empty($adminId) && empty($adminAccount) && $otherCondition ) {
            return false;
        }else{
            return true;
        }
    }

    /**
     * 权限认证
     */
    protected function getAuth()
    {
        $auth       =   new \Think\Auth();
        $adminId    =   session('adminId');
        /**
         * 如果是管理员就获得所有权限
         */
        $group   =   M('admin_auth_group_access')->where(['uid' => $this->admin_info['id']])->find();
        if($group['group_id'] != 1){
            // 普通用户得到权限列表
            $getAuthList = $auth->getAuthList($adminId , 1);

            if ( empty($getAuthList) ) {
                session(null);
                $this->error('你的账号没任何操作权限！', U('Login/login'));
            }
        }else{
            $rules = M('admin_auth_rule')->where(['status' => 1])->field('name')->select();
            foreach($rules as $rule){
                $getAuthList[] = strtolower($rule['name']);
            }
            return true;
        }
//        session('authList', $getAuthList);

        if ( !$auth->check(CONTROLLER_NAME.'-'.ACTION_NAME , $adminId) ) {

            // 无访问权限的时候才跳转，白名单
            $white = in_array(CONTROLLER_NAME.'-'.ACTION_NAME, array(
                'Login-login','Index-index','Login-logout'
            ));

            $preJumpUrl = session('preJumpUrl');
            if ( $white || empty($preJumpUrl) ) {
                // 找出可直接跳转的权限地址
                $canJumpList = M('admin_auth_rule')->where(array('direct_jump'=>'1'))->order('sort DESC')->getField('name', true);
                foreach ($getAuthList as $value) {
                    foreach ( $canJumpList as $jumpValue ) {
                        if ( strtolower($value) == strtolower($jumpValue) ) {
                            $url = str_replace('-', '/', $jumpValue);
                            session('preJumpUrl', $url);
                            header('LOCATION:' . U($url));
                            exit();
                        }
                    }
                }
            } else {
                header("Content-type:text/html;charset=utf-8");
                exit('你没有足够的权限访问该地址！<a href="' . U($preJumpUrl) . '">跳转到可访问页面</a>');
            }
        }
    }

    //获取菜单
    protected function getAuthMenu(){
        $adminId 	  = session('adminId');         // 管理员用户id
        $adminAccount = session('adminAccount');    // 管理员用户名
        $auth        = new \Think\Auth();
        // 普通用户得到权限列表

        //顶部菜单
        $Groups = $auth->getGroups($adminId);
        $rules_menu = explode(',', trim($Groups[0]['rules_menu']));
        $admin_where = array();
        $admin_where['status'] = 1;
        $admin_where['level'] = 1;
        if ($Groups[0]['group_id'] != 1) {
            if (!empty($rules_menu)) {
                $admin_where['id'] = array('in',$rules_menu);
            }else{
                $admin_where['id'] = 0;
            }
        }
        $admin_menu_list = M('admin_menu')->where($admin_where)->field(true)->select();


        //左侧菜单
        $where_left_menu = array();
        $where_left_menu['status'] = 1;
        $where_left_menu['level'] = array('gt',1);
        $where_left_menu['m_controller'] = CONTROLLER_NAME;
        if ($Groups[0]['group_id'] != 1) {
            if (!empty($rules_menu)) {
                $where_left_menu['id'] = array('in',$rules_menu);
            }else{
                $where_left_menu['id'] = 0;
            }
        }

        $left_menu = M('admin_menu')->where($where_left_menu)->field(true)->select();
        $left_big_menu = array();
        foreach ($left_menu as $key => $value) {
            if ($value['level'] == 2) {
                $left_big_menu[]['big'] = $value;
            }elseif ($value['level'] == 3) {
                $left_son_menu[] = $value;
            }
        }

        foreach ($left_son_menu as $k1 => $v1) {
            foreach ($left_big_menu as $k2 => $v2) {
                if ($v1['pid'] == $v2['big']['id']) {
                    if (!empty($v1['m_param'])) {
                        $v1['params_arr_tmp'] = explode('&', $v1['m_param']);
                        foreach ($v1['params_arr_tmp'] as $k3 => $v3) {
                            $param_v = explode('=', $v3);
                            $v1['params_arr'][$param_v[0]] = $param_v[1];
                        }
                    }
                    $left_big_menu[$k2]['son'][] = $v1;
                }
            }
        }
        return array('admin_menu_list'=>$admin_menu_list,'left_menu'=>$left_big_menu);
    }

    //获取左侧菜单
    protected function getLeftMenu(){
        $where_left_menu = array('status'=>1,'level'=>array('gt',1),'m_controller'=>CONTROLLER_NAME);
        $left_menu = M('admin_menu')->where($where_left_menu)->field(true)->select();
        $left_big_menu = array();
        foreach ($left_menu as $key => $value) {
            if ($value['level'] == 2) {
                $left_big_menu[]['big'] = $value;
            }elseif ($value['level'] == 3) {
                $left_son_menu[] = $value;
            }
        }

        foreach ($left_son_menu as $k1 => $v1) {
            foreach ($left_big_menu as $k2 => $v2) {
                if ($v1['pid'] == $v2['big']['id']) {
                    if (!empty($v1['m_param'])) {
                        $v1['params_arr_tmp'] = explode('&', $v1['m_param']);
                        foreach ($v1['params_arr_tmp'] as $k3 => $v3) {
                            $param_v = explode('=', $v3);
                            $v1['params_arr'][$param_v[0]] = $param_v[1];
                        }
                    }
                    $left_big_menu[$k2]['son'][] = $v1;
                }
            }
        }
        return $left_big_menu;
    }
}