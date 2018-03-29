<?php
/**
 * Created by MY_MIND.
 * User: WDJ
 * Date: 2018/3/29 0029
 * Time: 15:05
 */
namespace Xadmin\model;

use Common\Model\BottomModel;

class ConfigModel extends BottomModel
{
    //获取系统设置
    public function xadminGetSetting()
    {
        $config 	= M('config');
        $configList = $config->where(array('status'=>1))->order('sort,id')->select();
        return $configList;
    }

    //修改系统设置
    public function xadminSetting()
    {
        $config = M('config');
        foreach ($this->post as $key => $value) {
            $config->where(array('config_sign'=>$key))->data(array('config_value'=>$value))->save();
        }
        return [true , '保存成功'];
    }

    //小喇叭的获取与修改
    public function xadminNotice($type = 1)
    {
        //当前用户代理agent_id
        $sqlsrv_model   =   self::sqlsrv_model('PlatformDB', 'WeiXinInfo');
        //判断type是展示还是修改
        if($type == 1){
            $where          =   array('1=1');
            $GameWeixin    = $sqlsrv_model->table('WeiXinInfo')->where($where)->getField('GameWeixin');
            return [true , $GameWeixin];
        }elseif($type == 2){
            $GameWeixin   =   $this->post['GameWeixin'];
            $where          =   array('1=1');
            $WeiXinInfo    = $sqlsrv_model->table('WeiXinInfo')->where($where)->find();
            if(!empty($WeiXinInfo)){
                $res    =   $sqlsrv_model->table('WeiXinInfo')->where($where)->setField(array('GameWeixin'=>$GameWeixin));
                return [true , '保存成功'];
            }  else {
                return [false , '保存失败'];
            }
        }
    }

    //系统消息列表
    public function xadminMessageList()
    {
        $where = [];
        if(!empty($this->get['id'])){
            $where['ID'] =  intval($this->get['id']);
        }
        //当前用户代理agent_id
        $sqlsrv_model   =  self::sqlsrv_model('PlatformDB','SystemMessage');
        $where    = array('1=1');
        $count    = $sqlsrv_model->table('SystemMessage')->where($where)->count();
        $page     = new \Think\Page($count, 10);

        if (iswap()) {
            $page->rollPage	= 5;
        }

        $show     = $page->show();
        $MessageList = $sqlsrv_model->table('SystemMessage')->where($where)->field(true)->limit($page->firstRow . ',' . $page->listRows)->order('ID DESC')->select();
        return [$MessageList , $show];
    }

    //系统消息删除
    public function xadminDelMessage()
    {
        $id =   $this->get('id');
        $sqlsrv_model   =   self::sqlsrv_model('PlatformDB','SystemMessage');
        $res = $sqlsrv_model->table('SystemMessage')->where(array('ID'=>$id))->delete();
        if($res){
            return [true , '删除成功'];
        }else{
            return [false , '删除失败'];
        }
    }

    //分享配置的展示与修改
    public function xadminShare($type = 1)
    {
        $sqlsrv_model   =   self::sqlsrv_model('PlatformDB', 'WeiXinInfo');
        if($type == 1){
            $where          =   array('1=1');
            $WeiXinInfo    = $sqlsrv_model->table('WeiXinInfo')->where($where)->field('FangkaWeixin,AdviceWeixin,ProxyWeixin')->find();
            if(!empty($WeiXinInfo)){
                foreach ($WeiXinInfo as $key => $value) {
                    $WeiXinInfo[$key]   = rtrim($value);
                }
            }
            return [true , '' , $WeiXinInfo];
        }elseif($type == 2){
            $FangkaWeixin   =   $this->post['FangkaWeixin'];
            $AdviceWeixin   =   $this->post['AdviceWeixin'];
            $ProxyWeixin   =   $this->post['ProxyWeixin'];
            $where          =   array('1=1');
            $WeiXinInfo    = $sqlsrv_model->table('WeiXinInfo')->where($where)->find();
            if(!empty($WeiXinInfo)){
                $data   =   array();
                $data['FangkaWeixin']   =   $FangkaWeixin;
                $data['AdviceWeixin']   =   $AdviceWeixin;
                $data['ProxyWeixin']    =   $ProxyWeixin;
                $res    =   $sqlsrv_model->table('WeiXinInfo')->where($where)->setField($data);
                return [true , '保存成功' , null];
            }  else {
                return [false , '保存失败' , null];
            }
        }
    }

    //分享活动配置的展示与修改
    public function xadminShareActivity($type = 1)
    {
        if($type == 1){
            $where  =   array();
            $where['StatusName']    =   array('in',array('ShareAppReward','ShareRewardCnt','ShareRewardInsure','ShareRewardScore'));
            $sqlsrv_model   =   self::sqlsrv_model('AccountsDB', 'SystemStatusInfo');
            $SystemStatusInfo    = $sqlsrv_model->table('SystemStatusInfo')->where($where)->field(true)->select();

            $share_activity_config =   array();
            if (!empty($SystemStatusInfo)) {
                foreach ($SystemStatusInfo as $key => $value) {
                    $share_activity_config[$value['statusname']] = $value['statusvalue'];
                }
            }
            return [true , '' , $share_activity_config];
        }elseif($type == 2){
            $data   = array();
            $ShareAppReward     =   intval($this->post['ShareAppReward']);
            $ShareRewardCnt     =   intval($this->post['ShareRewardCnt']);
            $ShareRewardInsure     =   intval($this->post['ShareRewardInsure']);
            $ShareRewardScore     =   intval($this->post['ShareRewardScore']);

            $sqlsrv_model   =   self::sqlsrv_model('AccountsDB', 'SystemStatusInfo');

            // ShareAppReward    //用户微信分享游戏赠送金币功能    分享赠送    键值：1-开启，0-关闭
            // ShareRewardCnt   //用户微信分享游戏赠送金币每天次数  分享赠送    键值：每天赠送次数
            // ShareRewardInsure//用户微信分享游戏赠送房卡数量  分享赠送    键值：每次赠送房卡数量
            // ShareRewardScore //用户微信分享游戏赠送金币数量    分享赠送    键值：每次赠送金币数量

            $where['StatusName']    =   array('in',array('ShareAppReward','ShareRewardCnt','ShareRewardInsure','ShareRewardScore'));
            $SystemStatusInfo    = $sqlsrv_model->table('SystemStatusInfo')->where($where)->field(true)->select();
            if (!empty($SystemStatusInfo)) {
                if (count($SystemStatusInfo) !=4) {
                    return [false , '保存失败！' , null];
                }

                foreach ($SystemStatusInfo as $key => $value) {
                    $sqlsrv_model->table('SystemStatusInfo')->where(array('StatusName'=>$value['statusname']))->setField(array('StatusValue'=>$$value['statusname']));
                    dump($ShareAppReward);
//                    echo $sqlsrv_model->getLastSql();exit;
                }
                return [true , '保存成功！' , null];
            }else{
                return [false , '保存失败！' , null];
            }
        }


    }
}