<?php
/**
 * Created by MY_MIND.
 * User: WDJ
 * Date: 2018/3/30 0030
 * Time: 10:57
 */
namespace Api\Model;

use Common\Model\BottomModel;

class OrderModel extends BottomModel
{
    /* @param     $order_sn    商家订单号
     *  更新订单状态
     */
    public function updateOrder($order_sn = '1801251827522167587602')
    {
        $payInfo    =   M('pay')->where(['out_trade_no' =>  $order_sn])->field('id , payfrom , out_trade_no , money')->find();

        if(empty($payInfo))
            return;

        if($payInfo['status'] == 1)
            return;

        //判断来源，修改订单信息
        switch($payInfo['payfrom']){
            case 1:         //充值房卡
                $roomcard   =   M('recharge_roomcard');
                $orderInfo  =   $roomcard->where(['tag' => $order_sn])->find();
                //如果没有订单信息，或者订单已经支付直接返回
                if(empty($orderInfo) || $orderInfo['ispay'] == 1){
                    return;
                }
                //修改状态
                $order_res  =   $roomcard->where(['id' => $orderInfo['id']])->setField(['ispay' => 1 , 'pay_time' => NOW_TIME]);
                //修改成功后，返佣，购卡操作
                if($order_res !== false){
                    //购买房卡
                    $this->addPlayerInsureScore($orderInfo);

                    //返佣
                    $this->commissionToAgentByPlayer($orderInfo);
                }
                break;
            case 2:         //充值旗力币
                $gold   =   M('recharge_gold');
                $orderInfo  =   $gold->where(['tag' => $order_sn])->find();
                $this->commissionToAgentByPlayer($orderInfo);exit;
                //如果没有订单信息，或者订单已经支付直接返回
                if(empty($orderInfo) || $orderInfo['ispay'] == 1){
                    return;
                }
                //修改状态
                $order_res  =   $gold->where(['id' => $orderInfo['id']])->setField(['ispay' => 1 , 'pay_time' => NOW_TIME]);
                //修改成功后，返佣，购卡操作
                if($order_res !== false){
                    //购买旗力币
                    $this->addPlayerScore($orderInfo);

                    //返佣
                    $this->commissionToAgentByPlayer($orderInfo);
                }
                break;
            default:
                break;
        }

        /**
         * 更新支付信息
         */
        M('pay')->where(['id' => $payInfo['id']])->setField(['status' => 1 , 'update_time' => NOW_TIME]);
    }

    /**
     * 玩家充值房卡
     * @param   $orderInfo  订单信息
     */
    public function addPlayerInsureScore($orderInfo)
    {
        $sqlsrv_model   =   self::sqlsrv_model('TreasureDB' , 'GameScoreInfo');
        $playerInfo     =   $sqlsrv_model->table('GameScoreInfo')->where(['UserID' => $orderInfo['uid']])->field("UserID,InsureScore,Score")->find();

        if(empty($playerInfo)){
            return;
        }else{
            //玩家增加房卡数，首次购买当前商品，送相同数量商品
            $hasBuyCurrentGoods  =   M('recharge_gold')->where(['uid' => $orderInfo['uid'] , 'ispay' => 1 , 'goodsid' => $orderInfo['goodsid']])->count();
            $updateData          =   [];
            if($hasBuyCurrentGoods > 1){
                $updateData['InsureScore']  =   $playerInfo['insurescore'] + $orderInfo['nums'];
            }else{
                $updateData['InsureScore']  =   $playerInfo['insurescore'] + $orderInfo['nums'] * 2;
            }
            $buyer_gamescore_update           =    $sqlsrv_model->table('GameScoreInfo')->where(['UserID' => $orderInfo['id']])->setField($updateData);
        }
    }

    /**
     * 玩家充值旗力币
     * @param   $orderInfo  订单信息
     */
    public function addPlayerScore($orderInfo)
    {
        $sqlsrv_model       =   $this->sqlsrv_model('TreasureDB' , 'GameScoreInfo');
        $playerInfo         =   $sqlsrv_model->table('GameScoreInfo')->where()->field("UserID,InsureScore,Score")->find();

        if(empty($playerInfo)){
            return;
        }else{
            //玩家增加房卡数，首次购买当前商品，送相同数量商品
            $hasBuyCurrentGoods  =   M('recharge_gold')->where(['uid' => $orderInfo['uid'] , 'ispay' => 1 , 'goodsid' => $orderInfo['goodsid']])->count();
            $updateData          =   [];
            if($hasBuyCurrentGoods > 1){
                $updateData['InsureScore']  =   $playerInfo['insurescore'] + $orderInfo['nums'];
            }else{
                $updateData['InsureScore']  =   $playerInfo['insurescore'] + $orderInfo['nums'] * 2;
            }

            $buyer_gamescore_update           =    $sqlsrv_model->table('GameScoreInfo')->where(['UserID' => $orderInfo['id']])->setField($updateData);
        }
    }

    /**
     * 分佣给当前代理
     * @param   $orderInfo  订单信息
     */
    public function commissionToAgentByPlayer($orderInfo)
    {
        if(!empty($orderInfo['uid'])){
            //游戏玩家
            $gameuser   =   M('gameuser')->where(['game_user_id' => $orderInfo['uid']])->find();
            if(!empty($gameuser) && !empty($gameuser['invitation_code'])){
                //根据游戏玩家的邀请码找到代理
                $invitation_code_agent  =   M('agent')->where(['invitation_code' => $gameuser['invitation_code']])->find();
                if(!empty($invitation_code_agent)){
                    //首先添加销售记录
                    $agent_sales_volum_data['total_price']      =   $orderInfo['total_price'];
                    $agent_sales_volum_data['dateline']          =   NOW_TIME;
                    $agent_sales_volum_data['agent_id']          =   $invitation_code_agent['id'];
                    $agent_sales_volum_data['uid']                =   $gameuser['game_user_id'];
                    $agent_sales_volum_res                         =    M('agent_sales_volume')->add($agent_sales_volum_data);

                    //不采用系统配置，直接采用单人配置
                    $agent_one_rebate_config    =   M('agent_one_rebate_config')->where(['agent_id' => $invitation_code_agent['id']])->find();
                    /*
                     *查找所属代理的返佣以及所有上级的返佣比例
                     * 形同[
                     *      1   =》  ['rebate'   =>  70 , 'id'   =>  1 , phone => '157123456']
                     *      2   =》  ['rebate'   =>  80 , 'id'   =>  2 , phone => '157123456']
                     *      3   =》  ['rebate'   =>  90 , 'id'   =>  3 , phone => '157123456']
                     * ]
                     */
                    $my_rebate[$invitation_code_agent['level']]['rebate']   =   $agent_one_rebate_config['player'];
                    $my_rebate[$invitation_code_agent['level']]['id']        =   $invitation_code_agent['id'];
                    $my_rebate[$invitation_code_agent['level']]['phone']     =   $invitation_code_agent['phone'];
                    $up_rebate      =   $this->commissionToAgentParent($invitation_code_agent);
                    $total_rebate   =   $my_rebate + $up_rebate;

                    //对所有返佣的数据进行记录流水等
                    $this->record($total_rebate , $orderInfo['total_price'] , $invitation_code_agent['id'] , $gameuser['game_user_id'] , $orderInfo['nums']);
                }
            }
        }
    }

    /**
     * @paran   $agent   所属代理信息
     * 求出所有上级的返佣比例
     */
    public function commissionToAgentParent($agent)
    {
        //存放返佣比例的配置
        static $rebate = [];
        if($agent['pid'] != 0){
             $agent =   M('agent')->where(['id' => $agent['pid']])->find();
             $one_rebate_config  =    M('agent_one_rebate_config')->where(['agent_id' => $agent['id']])->find();
             $rebate[$agent['level']]['rebate']   =    !empty($one_rebate_config['player']) ? $one_rebate_config['player'] : 50;
             $rebate[$agent['level']]['id']        =    $agent['id'];
             $rebate[$agent['level']]['phone']     =    $agent['phone'];
             $this->commissionToAgentParent($agent);
        }
        return $rebate;
    }

    /**
     * @param   $total_rebate   等级的返佣
     * @param   $total_price    订单总价格
     * @param   $agent_id       所属代理商
     * @param   $uid            购买人
     * @param   $nums           购买数量
     * 记录流水等
     */
    public function record($total_rebate , $total_price , $agent_id , $uid , $nums)
    {
        //按代理等级排序
        krsort($total_rebate);
        foreach($total_rebate as $k => $v){
            //后面一个
            $behind     =  isset($total_rebate[$k + 1]) ? 1 -($total_rebate[$k + 1]['rebate'] / 100) : 1;
            //前面一个
            $front      =  isset($total_rebate[$k - 1]) ? ($total_rebate[$k - 1]['rebate'] / 100) : 1;
            //前面两个
            $_front     =  isset($total_rebate[$k - 2]) ? ($total_rebate[$k - 2]['rebate'] / 100)  :   1;
            $money      =  ($total_rebate[$k]['rebate'] / 100 ) * $_front * $front * $behind * $total_price;
            //保留2位小数
            $money      =   round($money , 2);

            /*
             * 所属代理的描述和上级代理的描述不同
             * 所以需要判断
             */
            if($v['id'] == $agent_id){
                //所属代理返利描述
                $recored_type    =  3;
                $recored_desc    =   '玩家[' . $uid . ']购买旗力币：[' . $nums . ']，所属代理[' . $v['phone'] . ']获得返利[' . $money . ']元';

                $bill_type      =   1;
                $bill_desc       =  '获得会员返利[' . $money . ']元';;
            }else{
                //上级代理返利描述
                $recored_type    =  1;
                $recored_desc    =   '玩家[' . $uid . ']购买旗力币：[' . $nums . ']，上级代理推荐人[' . $v['phone'] . ']获得返利[' . $money . ']元';

                $bill_type       =   2;
                $bill_desc       =   '获得推荐返利[' . $money . ']元';
            }

            //返利记录
            $agent_rebate_recored_data['type']      =   $recored_type;      //1：下级返利、2:推销人返利、3：会员返利
            $agent_rebate_recored_data['agent_id']  =   $v['id'];
            $agent_rebate_recored_data['uid']        =   $uid;
            $agent_rebate_recored_data['money']      =   $money;
            $agent_rebate_recored_data['desc']       =   $recored_desc;
            $agent_rebate_recored_data['dateline']   =   NOW_TIME;
            M('agent_rebate_recored')->add($agent_rebate_recored_data);

            //账单流水
            $agent_bill_data['type']                =   $bill_type;         //1：会员返利，2：推荐返利，3：提现
            $agent_bill_data['change_type']        =    1;                  //1：收入，2：支出
            $agent_bill_data['agent_id']            =   $v['id'];
            $agent_bill_data['money']               =   $money;
            $agent_bill_data['desc']                =   $bill_desc;
            $agent_bill_data['dateline']            =   NOW_TIME;
            M('agent_bill_list')->add($agent_bill_data);

            //修改所属代理商的收入与余额
            M('agent')->where(['id' => $v['id']])->setInc('available_balance' , $money);
            M('agent')->where(['id' => $v['id']])->setInc('accumulated_income' , $money);

        }
    }

    /**
     * @param   $uid             游戏用户id
     * @param   $recharge_money  充值旗力币金额
     * @param   $rechage_goods_res  充值订单的id
     * @param   $nums           充值数量
     *  利用旗力币充值房卡
     */
    public function rechargeRoomcardByQilibi($uid , $recharge_money , $rechage_goods_res , $nums)
    {
        $sqlsrv_model       =   self::sqlsrv_model('TreasureDB' , 'GameScoreInfo');
        $playerInfo         =   $sqlsrv_model->table('GameScoreInfo')->where(['UserId' => $uid])->find();
        if(empty($playerInfo)){
            return [false , 2001];
        }else{
            if($playerInfo['score'] < $recharge_money){
                return [false , 2002];
            }

            //游戏玩家增加房卡数，并且减少旗力币数量
            $updateData['Score']        =   $playerInfo['score'] - $recharge_money;
            $updateData['InsureScore'] =    $playerInfo['insurescore'] + $nums;
            $buyer_gameruser_update      =   $sqlsrv_model->table('GameScoreInfo')->where(['UserID' => $uid])->setField($updateData);
            if($buyer_gameruser_update != false){
                //如果跟新成功的话就更新订单状态
                M('order')->where(['id' => $rechage_goods_res])->setField(['ispay' => 1 , 'pay_time' => NOW_TIME]);
                return [true , 0];
            }
            return [false , 2003];
        }
        
    }
}