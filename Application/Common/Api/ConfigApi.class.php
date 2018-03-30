<?php
/**
 * Created by MY_MIND.
 * User: WDJ
 * Date: 2018/3/30 0030
 * Time: 09:06
 */
namespace Common\Api;

class ConfigApi
{
    /**
     * 获取数据库中配置文件列表
     * @return  array   配置数组
     */
    public static function lists()
    {
        $data   =   M('config_list')->where(['status' => 1])->field('type,name,value')->select();

        //拼接组装数据
        $config =   [];
        if(!empty($data)){
            foreach($data as $key => $value){
                $config[$value['name']] =   self::parse($value['type'] , $value['value']);
            }
        }
        return $config;
    }

    /**
     * 只有当配置类型为3的时候，需要解析配置
     */
    private static function parse($type , $value)
    {
        switch($type){
            case 3 : //解析数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if(strpos($value , ':')){
                    $value = [];
                    foreach($array as $val){
                        list($k , $v) = explode(':' , $val);
                        $value[$k]  =   $v;
                    }
                }else{
                    $value  =   $array;
                }
            break;
        }
        return $value;
    }

}