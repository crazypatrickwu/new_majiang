<?php
/**
 * Created by MY_MIND.
 * User: WDJ
 * Date: 2018/3/30 0030
 * Time: 10:52
 */
namespace Api\Controller;

use Api\Model\OrderModel;

class PayController extends CommonController
{
    /**
     * @param   time    服务器当前时间
     * @param   hash    数组经过排列加密之后的
     * @param   uid     用户id
     * @param   hashid
     * @param   orderid     订单id
     * @param   order_sn    订单号
     * @param   paytype     1:支付宝   2：微信    3：会员
     */
    public function index()
    {
        //校验规则
        $CheckParam =   [
//            ['time' , 'Int' , 1 , 1],
            ['hash' , 'String' , 1 , 2],
            ['uid' , 'Int' , 1 , 1002],
            ['hashid' , 'String' , 1 , 1002],
            ['orderid' , 'Int' , 1 , 1003],
            ['order_sn' , 'String' , 1, 1004],
            ['paytype' , 'Int' , 1 , 1005]
        ];
        //校验过后的参数
        $BackData   =   $this->CheckData(I('post.') , $CheckParam);

        //检测user信息


        //支付类型
        if(!in_array($BackData['paytype'] , array(1,2,3))){
            $this->throwError(1007);
        }

        //检验订单信息
        $order  =   M('order');
        $orderInfo  =   $order->where([
            'id'        =>  $BackData['orderid'],
            'order_sn'  =>  $BackData['order_sn'],
            'uid'       =>  $BackData['uid']
        ])->field(['id' , 'order_sn' , 'pay_status'])->find();
        //订单信息为空
        if(empty($orderInfo)){
            $this->throwError(1010);
        }
        //订单已支付
        if($orderInfo['pay_status'] != 0){
            $this->throwError(1011);
        }

        //支付信息
        $payInfo    =   M('pay')->where(['out_trade_no' => $orderInfo['order_sn']])->field(['id' , 'money' , 'total'])->find();
        if(empty($payInfo)){
            $this->throwError(1010);
        }
        $money  =   $payInfo['money'];
        if($money <= 0 || $money == '0.0' || $money == '0.00'){
            $this->throwError(1012);
        }

        //最终价格
        $free   =   sprintf("%.2f" , $money);
        if($free <= 0.01 || $free == '0.00' || $free == '0.0'){
            //如果总价格不对的话，直接支付一分钱
            $free   =   0.01;
        }

        $body   =   APP_NAME;
        $attach =   APP_NAME;
        //开启支付
        $this->pay($orderInfo['order_sn'] , $body , $attach , $free , $BackData['paytype'] , 0);
    }

    /**
     * 统一支付流程
     */
    public function pay($trade_sn , $body , $attach , $fee , $paytype , $requestType)
    {
        dump(date('Y-m-d H:i:s' , 1522400995));exit;
        //支付回调地址
        $notify_url =   'http://' . WEB_DOMAIN . 'Api/Pay/';
        switch($paytype){
            case 1:     //支付宝支付
                $notify_url =   $notify_url . 'ali_success';
                $res        =   $this->alipay_app($trade_sn , $body , $attach , $fee , $notify_url);
                break;

            case 2:     //微信支付
                $notify_url =   $notify_url . 'wechat_success';
                $res        =   $this->wechat_app($trade_sn , $body , $attach , $fee , $notify_url);
                break;

            case 3:     //会员卡

                break;

            case 4:     //积分

                break;
        }

        if($res['state'] == 1){
            if($requestType == 1){
                return $res['data'];
            }
            $this->response(['code' => 0 , 'msg' => 'ok' , 'data' => $res['data']]);
        }else{
            $this->throwError(1014);
        }
    }

    /**
     * 支付宝支付
     * @param   trade_sn    订单号
     * @param   title       商品名称
     * @param   body        商品详情
     * @param   fee         总金额
     * @param   notify_url  通知地址
     */
    protected function alipay_app($trade_sn , $title , $body , $fee , $notify_url)
    {
        include_once(CONF_PATH . 'alipay.config.php');
        include_once (VENDOR_PATH . '/Alipay/lib/alipay_core.function.php');
        include_once (VENDOR_PATH . '/Alipay/lib/alipay_rsa.function.php');

        //参数
        $para   =   [
            'service'       =>  'mobile.securitypay.pay',
            'partner'       =>  $alipay_config['partner'],
            '_input_charset'=>  'utf-8',
            'sign_type'     =>  'RSA',
            'sign'           =>  '',
            'notify_url'    =>  urlencode($notify_url),
            'out_trade_no'  =>  $trade_sn,
            'subject'        =>  $title,
            'payment_type'  =>  1,
            'seller_id'      =>  $alipay_config['seller_id'],
            'total_fee'      =>  $fee,
            'body'            =>  $body,
        ];
        //排序
        $para   =   argSort($para);
        $str    =   '';
        //过滤不签名数据
        foreach($para as $key => $value){
            if($key == 'sign_type' || $key == 'sign'){
                continue;
            }else{
                if($str == ''){
                    $str = $key . '=' . '"' . $value . '"';
                }else{
                    $str = $str . '&' . $key . '=' . '"' . $value . '"';
                }
            }
        }
        //生成签名
        $sign   =   rsaSign($str , VENDOR_PATH . '/Alipay/key/rsa_private_key.pem');
        $sign   =   urlencode($sign);
        $pay_info   =   $str . '&sign=' . '"' . $sign . '"' . '&sign_type="RSA"';
        return ['state' => 1 , 'data' => $pay_info , 'msg' => '成功'];
    }

    /**
     * 微信支付
     * @param   trade_sn    订单号
     * @param   body        主题
     * @param   attach      附加
     * @param   fee         金额
     * @param   notify_url  回调
     */
    protected function wechat_app($trade_sn , $body , $attach , $fee , $notify_url)
    {
        vendor('Wxpay.WxPayPubHelper');
        $unifieOrder    =   new \UnifiedOrder_pub();
        $unifieOrder->setParameter("attach" , $attach);
        $unifieOrder->setParameter("body" , $body);
        $unifieOrder->setParameter("out_trade_no" , $trade_sn);
        $unifieOrder->setParameter("total_fee" , $fee * 100);
        $unifieOrder->setParameter("notify_url" , $notify_url);
        $unifieOrder->setParameter("trade_type" , "APP");
        $order      =   $unifieOrder->getPrepayId();
        //统一下单
        $prepay_id  =   $order['prepay_id'];
        if($prepay_id){
            $temp   =   [
                'appid'     =>  $order['appid'],
                'nonecestr' =>  $order['nonce_str'],
                'package'   =>  $order['mch_id'],
                'partnerid' =>  $order['prepay_id'],
                'timestamp' =>  NOW_TIME
            ];
            ksort($temp);
            $temp['sign']   =   $unifieOrder->getSign($temp);
            return ['state' => 1 , 'data' => $temp , 'msg' => '成功'];
        }else{
            $this->throwError(6001);
        }
    }

    /**
     * 支付宝回调地址
     */
    public function ali_success()
    {
        include_once (CONF_PATH . 'alipay.config.php');
        include_once (VENDOR_PATH . '/Alipay/lib/alipay_notify.class.php');

        //计算得出通知验证结果
        $alipayNotify   =   new \AlipayNotify();
        $verify_result  =   $alipayNotify->verifyNotify();
        if($verify_result){
            //商户订单号
            $out_trade_no   =   $_POST['out_trade_no'];
            //支付宝交易号
            $trade_no       =   $_POST['trade_no'];
            if($_POST['trade_status'] == 'TRADE_SUCCESS'){
                //更新订单状态
                $model  =   new OrderModel();
                $model->updateOrder($out_trade_no , $trade_no);
            }
        }else{
            echo "fail";
        }
    }

    /**
     * 微信回调地址
     */
    public function wechat_success()
    {
        vendor('Wxpay.WxPayPubHelper');
        $notify     =   new \Notify_pub();
        $xml        =   $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);
        //验证签名
        if($notify->checkSign($this->cfg['wx_key']) == FALSE){
            $notify->setReturnParameter("return_code" , "FAIL");
            $notify->setReturnParameter("return_msg" , "签名失败");
        }else{
            if($notify->data['return_code'] ==  'FAIL'){
                //通信出错，暂不处理
            }elseif($notify->data['result_code'] == "FAIL"){
                //业务出错，暂不处理
            }else{
                $order_sn       =   $notify->data['out_trade_no'];
                $transaction_id =   $notify->data['transaction_id'];
                $model          =   new OrderModel();
                $model->update($order_sn , $transaction_id);
            }
            $notify->setReturnParameter("return_code" , "SUCCESS");
        }
        $returnXml  =   $notify->returnXml();
    }

}