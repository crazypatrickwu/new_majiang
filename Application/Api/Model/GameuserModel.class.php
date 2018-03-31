<?php
/**
 * Created by MY_MIND.
 * User: WDJ
 * Date: 2018/3/31 0031
 * Time: 17:24
 */
namespace Api\Model;

use Common\Model\BottomModel;

class GameuserModel extends BottomModel
{
    /**
     * 绑定邀请码赠送用户房卡
     * @param       $uid       游戏玩家id
     * @param       $num       赠送房卡的数量
     * @param       $type      赠送类型     3：玩家分享  4：绑定邀请码 5：实名认证
     * @param       $txt        赠送说明
     */
    public function giveUser($uid , $nums = 20 , $type = 4 , $txt = '绑定邀请码')
    {
        //查看当前房卡数量
        $sqlsrv_model       =   self::sqlsrv_model('TreasureDB' , 'GameScoreInfo');
        $playerCard         =   $sqlsrv_model->table('GameScoreInfo')->where(['UserID' => $uid])->field("UserID,InsureScore,Score")->find();

        if(empty($playerCard)){
            return [false , 1019];
        }

        //玩家增加房卡
        $buyer_gamescore_update     =   $sqlsrv_model->table('GameScoreInfo')->where(['UserID' => $playerCard['userod']])->setField(['InsureScore' => $playerCard['Insurescore'] + $nums]);

        //游戏玩家信息
        $sqlsrv_model2              =   self::sqlsrv_model('AccountDB' , 'AccountsInfo');
        $player_accounts_info       =   $sqlsrv_model2->table("AccountsInfo")->where(['UserID' => $uid])->field("UserID , Nickname , LastLogonDate , RegisterDate")->find();

        //充值记录
        $user_recharge_recored_data['user_id']      =   $player_accounts_info['UserID'];
        $user_recharge_recored_data['pay_nums']     =   $nums;
        $user_recharge_recored_data['user_id']      =   '玩家[' . $player_accounts_info['NickName'] . ']' . $txt . '，赠送房卡：' . $nums . '张';
        $user_recharge_recored_data['add_time']     =   NOW_TIME;
        $user_recharge_recored_data['type']         =   $type;
        $res = M('user_recharge_recored')->add();
        return [true , ];
    }
}






















