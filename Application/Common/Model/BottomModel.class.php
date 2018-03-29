<?php
/**
 * Created by MY_MIND.
 * User: WDJ
 * Date: 2018/3/27 0027
 * Time: 17:25
 */
namespace Common\Model;

use Think\Model;

class BottomModel extends Model
{
    protected $get;
    protected $post;
    /**
     * 初始化并获取get和post数据
     */
    public function _initialize()
    {
        parent::_initialize();

        if(IS_GET){
            $this->get  =   self::_safeData(I('get.'));
        }elseif(IS_POST){
            $this->post =   self::_safeData(I('post.'));
        }
    }

    /**
     * 获取当前表名
     */
    public static function _getTable($_class)
    {
        $prefix =   C('DB_PREFIX');
        //首先获取当前分割截取
        $class  =   explode('\\' , $_class);
        $table  =   strtolower(substr($class[2] , 0, -5));
        return $prefix . $table;
    }

    /**
     * 连接sql_server数据库
     */
    public static function sqlsrv_model($db_name,$db_table){
        $sqlsrv_config    =   C('SQLSRV_CONFIG');
        $connectiont = array(
            'db_type' => 'sqlsrv',
            'db_host' => $sqlsrv_config['DB_HOST'],//'139.196.214.241',
            'db_user' => $sqlsrv_config['DB_USER'],
            'db_pwd' => $sqlsrv_config['DB_PWD'],
            'db_port' => $sqlsrv_config['DB_PORT'],
            'db_name' => $sqlsrv_config['DB_PREFIX'].$db_name,
            'db_charset' => 'utf8',
        );
        $sqlsrv_model   =   M($db_table , NULL , $connectiont);
        return $sqlsrv_model;
    }

    /**
     * 过滤数据的方法
     */
    public static function _safeData($data)
    {
        $_data = [];
        if($data){
            if(is_array($data)){
                foreach($data as $key=>$value){
                    if(is_array($value)){
                        $_data[$key]    =   self::_safeData($value);
                    }else{
                        $_data[$key]    =   htmlspecialchars(addslashes(trim($value)));
                    }
                }
            }else{
                $_data    =   htmlspecialchars(addcslashes(trim($data)));
            }
        }

        return $_data;
    }

    /**
     * 递归查询子孙信息
     */
    public static function _getChild($data , $pid = 0 , $level = 1)
    {
        static $_data = [];
        foreach($data as $key => $value){
            if($value['pid'] == $pid){
                $_data[$key]    =   $value;
                unset($data[$key]);
                self::_getChild($data , $value['id'] , $level+1);
            }
        }
        return $_data;
    }

    /**
     * 递归获取父类信息
     */
    public static function _getFather($data , $pid = 3 , $level = 3)
    {
        static $_data = [];
        foreach($data as $key => $value){
            if($value['id'] = $pid){
                $_data[$key]    =   $value;
                unset($data[$key]);
//                if()
            }
        }
    }
}