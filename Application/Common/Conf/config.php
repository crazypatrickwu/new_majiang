<?php
/**
 * Created by MY_MIND
 * User: WDJ
 * Date: 2018/3/27 0027
 * Time: 17:07
 */
return array(
    /**
     * 数据库配置
     */
    'DB_TYPE'               => 'mysql',
    'DB_HOST'               => '127.0.0.1',
    'DB_NAME'               => 'majiang',
    'DB_USER'               => 'root',
    'DB_PWD'                => 'root',
    'DB_PORT'               => 3306,
    'DB_PREFIX'             => 'jianjiao_',
    'DB_CHARSET'            => 'utf8',

    /**
     * 模块设置

    'MODULE_ALLOW_LIST'     =>  array(
        'Agent',
        'Pcweb',
        'Home',
        'Xadmin',
    ), */

    'DEFAULT_MODULE'        => 'Xadmin',

    /**
     * AUTH认证
     */
    /*'AUTH_CONFIG'           =>  array(
        'AUTH_ON'           => true,                      // 认证开关
        'AUTH_TYPE'         => 1,                         // 认证方式，1为实时认证；2为登录认证。
        // 用户组数据表名
        'AUTH_GROUP' => 'admin_auth_group',
        // 用户-用户组关系表
        'AUTH_GROUP_ACCESS' => 'admin_auth_group_access',
        // 权限规则表
        'AUTH_RULE' => 'admin_auth_rule',
        // 用户信息表
        'AUTH_USER' => 'admin'
    ),*/

    'URL_MODEL'              => 2,       //隐藏index.php
    'TMPL_PARSE_STRING'     => array(
        '__PUBLIC__'         => '/Public'
    ),

    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => 'k+_b}yC2Hx~:uZ/O=a9g-0{6^B|LhfwFlG@I?1MY', //默认数据加密KEY
    'PLATFORM_INVITATION_CODE'=>'123456',   //平台默认邀请码

//    'TAGLIB_BUILD_IN'     => 'cx,diy',
    'SESSION_AUTO_START'  => true,
    'TMPL_ACTION_ERROR'   => './Public/MsgPage/PC/dispatch_jump.html',   // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => './Public/MsgPage/PC/dispatch_jump.html',   // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE' => './Public/MsgPage/PC/think_exception.html', // 异常页面的模板文件

    'SQLSRV_CONFIG' =>  array(
        'DB_HOST'   =>  '192.168.8.234',
        'DB_USER'   =>  'sa',
        'DB_PWD'    =>  'wzEcbx3IV7j4',
        'DB_PORT'   =>  1433,
        'DB_PREFIX' =>  'QP',
    ),

    'AGENT_LEVEL' => array(
        1=>array('level_name'=>'一级','agency_fee'=>0,'give_num'=>0,'pid_commission_rate'=>0.6,'rid_commission_rate1'=>0,'rid_commission_rate2'=>0.1,'rid_commission_rate3'=>0.1),
        2=>array('level_name'=>'二级','agency_fee'=>0,'give_num'=>0,'pid_commission_rate'=>0.5,'rid_commission_rate1'=>0.1,'rid_commission_rate2'=>0.1,'rid_commission_rate3'=>0.1),
        3=>array('level_name'=>'三级','agency_fee'=>0,'give_num'=>0,'pid_commission_rate'=>0.4,'rid_commission_rate1'=>0.1,'rid_commission_rate2'=>0.1,'rid_commission_rate3'=>0.1),
    ),
);
