<?php
/**
 * Created by MY_MIND.
 * User: WDJ
 * Date: 2018/3/30 0030
 * Time: 08:55
 */
namespace Api\Controller;

use Think\Controller\RestController;

/**
 * Class CommonController   API接口的公共基础控制器
 * @package Api\Controller
 */
class CommonController  extends RestController
{
    protected $ApiHash  =   '';
    protected $ApiParam =   [];
    /**
     * 模型初始化
     */
    public function _initialize()
    {
        /**
         * 读取数据库中的配置
         * 将配置缓存起来
         */
        $config = S('DB_CONFIG_DATA');
        if (!$config) {
            $config = api('Config/lists');
            S('DB_CONFIG_DATA', $config);
        }
        C($config);
    }

    /**
     * 错误码合集
     * @var array
     */
    public $errorCode = [
        '-1'  =>    '系统错误',
        '1'   =>    '服务器时间错误',
        '2'   =>    '签名错误',
        '3'   =>    '参数校验失败',
        '4'   =>    '用户ID不合法',
        '5'   =>    '用户不存在',
        '1000' =>   '参数不能为空',
        '1001' =>   '用户ID不能为空',
        '1002' =>   'hashid不能为空',
        '1003' =>   '订单ID不能为空',
        '1004' =>   '订单号不能为空',
        '1005' =>   '支付类型不能为空',
        '1006' =>   '店铺ID',
        '1007' =>   '非法支付类型',
        '1008' =>   '店铺不存在或被禁用',
        '1009' =>   '非法支付类型',
        '1010' =>   '订单不存在',
        '1011' =>   '订单已经支付,无需再支付',
        '1012' =>   '支付金额有误',
        '1013' =>   '支付类型错误',
        '1014' =>   '支付系统提示：以返回结果为准',
        '1015' =>   '订单无商品购买',
        '6001' =>   '微信获取prepay_id失败'
    ];

    /**
     * 抛出错误
     */
    public function throwError($code = '-1')
    {
        $this->response(['code' => $code , 'msg' => $this->errorCode[$code]]);
    }

    /**
     * 返回JSON格式
     */
    protected function response($data = [] , $type = 'json', $status = 200)
    {
        //设置http状态
        $this->sendHttpStatus($status);
        $this->setContentType($type);
        $ApiHash    =   empty($this->ApiHash) ? '' : $this->ApiHash;
        $base       =   [
            'code'  =>   '-1',
            'msg'   =>  '系统错误',
            'time'  =>  date('Y-m-d H:i:s' , NOW_TIME),
            'apiurl'=>  U(),
            'ApiHash'=> $ApiHash,
            'explain'=> '',
            'data'   => ''
        ];
        $BackData      =    array_merge($base , $data);
        if(C('IS_EXPLAIN') !== true)
            unset($BackData['explain']);        //删除接口说明
        $jsondata      =    json_encode($BackData);
        $IsLogs        =    C('IS_API_LOGS');
        //添加API接口调用日志
        if($IsLogs){
            $Logs['api_url']    =   U();
            $Logs['create_time']=   NOW_TIME;
            $Logs['ip']          =  get_client_ip();
            $Logs['msg']         =  $BackData['msg'];
            $Logs['parames']    =   json_encode($this->GetApiParam());
            M('api_logs')->add($Logs);
        }
        die($jsondata);
    }

    /**
     * 数据安全加密
     */
    protected function ArraySort($data = [] , $hash)
    {
        if(empty($data)){
            return md5(rand(1,5) . C('DATA_AUTH_KEY')) === $hash ? true : false;
        }
        ksort($data);
        $value = '';
        foreach($data as $v){
            $value .= $v;
        }
        $this->ApiHash  =   md5($value . C('DATA_AUTH_KEY'));
        return $this->ApiHash   === $hash ? true : false;
    }

    /**
     * 数据安全校验
     */
    protected function CheckData($PostData = [] , $Fields = [])
    {
        //参数或者要检验的参数不能为空
        if(empty($PostData) || empty($Fields))
            $this->throwError('1000');

        $sign   =   $BackData   =   [];
        //判断传递数据是否完整合法
        foreach($Fields as $val){
            //判断传递参数的类型并且不能为空
            if($val[2] == 1){
                if(($val[1] == 'Int' && intval($PostData[$val[0]]) <= 0) || ($val[1] == 'String' && empty($PostData[$val[0]]))){
                    $this->throwError($PostData[$val[3]]);
                }
            }

            //hash不参与签名
            if($val[0] != 'hash'){
                $sign[] =   $val[0];
            }else{
                $hash   =   $PostData[$val[0]];
            }
            $BackData[$val[0]]  =   $val[1] == 'Int' ? intval($PostData[$val[0]]) : trim($PostData[$val[0]]);
        }

        //判断签名校验是否通过
        if(!$this->ArraySort($sign , $hash)){
            $this->throwError('3');
        }

        return $this->SetApiParam($BackData);
    }

    /**
     *用户ID校验
     */
    protected function check_user($uid , $hashid)
    {
        if(md5($uid . C('DATA_AUTH_KEY')) != $hashid)
        {
            $this->throwError(4);
        }
        /**
         * 校验用户存在性
         */
    }

    /**
     * 加密校验串
     */
    protected function check_hashid($str , $hashid , $msg='')
    {
        $msg  =   empty($msg) ? '数据串校验错误' : $msg;
        if(md5($str . C('DATA_AUTH_KEY')) != $hashid || empty($str) || empty($hashis))
        {
            $msg  =   "校验串：".$msg;
            $this->response([
                'code'    =>  '6',
                'msg'     =>  $msg
            ]);
        }
    }

    /**
     * 生成校验串
     */
    protected function make_hashid($str)
    {
        return md5($str . C('DATA_AUTH_KEY'));
    }

    /**
     * 设置接口参数
     */
    protected function SetApiParam($param)
    {
        $this->ApiParam     =   $param;
        return $this->ApiParam;
    }

    /**
     * 获取接口参数
     */
    protected function GetApiParam()
    {
        return $this->ApiParam;
    }

    /**
     * 通用分页列表数据集获取
     */
    protected function lists($model,$where=array(),$order='',$field=true,$page=1)
    {
        $options    = array();
        $REQUEST    = (array)I('request.');
        //数据对象初始化
        $model  	= is_string($model) ? M($model) : $model;
        $OPT        = new \ReflectionProperty($model,'options');
        $OPT->setAccessible(true);
        //获取主键
        $pk         = $model->getPk();
        if($order===null){
            //order置空
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }

        $where  			= empty($where) ?  array() : $where;
        $options['where']   = $where;
        $options      		= array_merge( (array)$OPT->getValue($model), $options );
        $total        		= $model->where($options['where'])->count();
        $defLimit			= C('LIST_ROWS');
        $listLimit 			= $defLimit > 0 ? $defLimit : 10;
        $remainder			= intval($total-$listLimit*$page);
        //分页
        $model->setProperty('options',$options);
        $this->remainder	= $remainder >= 0 ? $remainder : 0;
        $this->total		= $total;
        return $model->field($field)->limit($listLimit)->page($page)->select();
    }
}