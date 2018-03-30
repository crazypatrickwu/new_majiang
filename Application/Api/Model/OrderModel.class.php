<?php
/**
 * Created by MY_MIND.
 * User: WDJ
 * Date: 2018/3/30 0030
 * Time: 10:57
 */
namespace Api\Model;

use Think\Model;

class OrderModel extends Model
{
    /* @param     $order_sn    商家订单号
     * @param      $trade_no   网络支付订单号
     *  更新订单状态
     */
    public function updateOrder($order_sn , $trade_no)
    {
        $payInfo    =   M('pay')->where(['out_trade_no' =>  $order_sn])->field('id , payfrom , out_trade_no , money')->find();
        if(empty($payInfo))
            return;

        if($payInfo['status'] == 1)
            return;

        switch($payInfo['payfrom']){
            case 1:         //充值房卡
                
        }
    }
}