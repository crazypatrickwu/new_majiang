<?php
/**
 * Created by MY_MIND.
 * User: WDJ
 * Date: 2018/3/31 0031
 * Time: 16:52
 */
namespace Api\Controller;

use Api\Model\GameuserModel;
use Api\Model\OrderModel;

class GameController extends CommonController
{
    /**
     * 玩家绑定邀请码
     * @param   $time       服务器时间戳
     * @param   $hash       处理之后的加密字符串
     * @param   $uid        用户ID
     * @param   $invitation_code    邀请码
     */
    public function setUserBind()
    {
        $CheckParam =   [
//            ['time' , 'Int' , 1 , 1],
            ['hash' , 'String' , 1 , 2],
            ['uid' , 'Int' , 1 , 1001],
            ['invitation_code' , 'string' , 1 , 1007],
        ];

        //校验参数
        $BackData   =   $this->CheckData(I('post.') , $CheckParam);

        //检验游戏用户的存在性
        $this->check_user($BackData['uid'] , $BackData['hashid']);

        //检验邀请码正确性
        $agentInfo  =   M('agent')->where(['invitation_code' => strtolower($BackData['invitation_code'])])->find();
        if(empty($agentInfo) || $agentInfo['is_delete'] == 1 || $agentInfo['is_lock'] == 1){
            $this->throwError(1018);
        }

        //玩家绑定邀请码，如果存在就修改，如果不存在就添加，添加之后赠送20张房卡
        $gameuser       =   M('gameuser');
        $gameuserInfo   =   $gameuser->where(['game_user_id' => $BackData['uid'] , 'user_id' => 0])->find();
        if($gameuserInfo){
            $res        =   $gameuser->where(['game_user_id' => $BackData['uid']])->setField(['invitation_code' => $BackData['invitation'] , 'update_time' => NOW_TIME]);
        }else{
            $game_data['invitation_code']       =   $BackData['invitation_code'];
            $game_data['game_user_id']          =   $BackData['uid'];
            $game_data['add_time']               =   NOW_TIME;
            $game_data['update_time']           =   NOW_TIME;
            $res        =   $gameuser->add($game_data);
            $model      =   new GameuserModel();
            //赠送房卡
            $model->giveUser($BackData['uid'] , 20 ,4 , '绑定邀请码');
        }
        //判断是否绑定成功
        if($res != false){
            $this->response(['code' => 0 , 'msg' => '绑定成功' , 'data' => $BackData['invitation_code']]);
        }
        $this->throwError(1018);
    }
}