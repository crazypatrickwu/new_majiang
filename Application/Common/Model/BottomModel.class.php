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
    public static function _getChild($data , $pid = 0 , $level=1)
    {
        static $_data = [];
        foreach($data as $key=>$value){
            if($value['pid'] == $pid){
                $_data[$key]    =   $value;
                $_data[$key]['child'] =   self::_getChild($data , $value['id'] , $level+1);
            }
        }
        exit;
        return $_data;
    }
}