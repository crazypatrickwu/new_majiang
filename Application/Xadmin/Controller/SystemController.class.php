<?php
/**
 * Created by MY_MIND.
 * User: WDJ
 * Date: 2018/3/29 0029
 * Time: 14:59
 */
namespace Xadmin\Controller;

use Xadmin\Model\ConfigModel;

class SystemController extends BaseController
{
    //系统设置
    public function setting()
    {
        $model  =   new ConfigModel();
        if(IS_POST){
            list($flag , $message)  =   $model->xadminSetting();
            if($flag){
                $this->success($message);
            }else{
                $this->error($message);
            }
        }else{
            $configList =   $model->xadminGetSetting();
            $this->assign('configList', $configList);
            $this->display('setting');
        }
    }

    //小喇叭
    public function notice()
    {
        $model  =   new ConfigModel();
        if(IS_POST){
            list($flag , $message)   =   $model->xadminNotice(2);
            if($flag){
                $this->success($message);
            }else{
                $this->error($message);
            }
        }else{
            list($flag , $GameWeixin)   =   $model->xadminNotice();
            $this->assign('GameWeixin', rtrim($GameWeixin));
            $this->display();
        }
    }

    //系统消息的展示
    public function messageList()
    {
        $config =   new ConfigModel();
        list($MessageList , $show)  =   $config->xadminMessageList();
        $this->assign('list', $MessageList);
        $this->assign('show', $show);
        $this->display();
    }

    //系统消息的删除
    public function messageDel()
    {
        $config =   new ConfigModel();
        list($flag , $message)  = $config->xadminDelMessage();
        $way    =   $flag   ?   'success'   :   'error';
        $this->$way($message);
    }

    //分享配置
    public function share()
    {
        $model = new ConfigModel();
        if(IS_POST){
            list($flag , $message , $data)    =   $model->xadminShare(2);
            $way    =   $flag   ?   'success'   :   'error';
            $this->$way($message);
        }else{
            list($flag , $message , $WeiXinInfo)    =   $model->xadminShare();
            $this->assign('WeiXinInfo', $WeiXinInfo);
            $this->display();
        }
    }

    //分享活动配置
    public function shareActivity()
    {
        $model  =   new ConfigModel();
        if(IS_POST){
            list($flag , $message , $data)    =   $model->xadminShareActivity(2);
            $way    =   $flag   ?   'success'   :   'error';
            $this->$way($message);
        }else{
            list($flag , $message , $share_activity_config) =   $model->xadminShareActivity();
            $this->assign('info', $share_activity_config);
            $this->display();
        }
    }
}