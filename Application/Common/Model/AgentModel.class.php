<?php
/**
 * Created by MY_MIND.
 * User: WDJ
 * Date: 2018/3/28 0028
 * Time: 11:35
 */
namespace Common\Model;

class AgentModel extends BottomModel
{
    //设置初始密码
    const   INIT_PASS   =   12345;

    /**
     * 输入数据的验证规则
     */
    protected $_validate = array(
        array('phone','require','手机号码不得为空！'), //默认情况下用正则进行验证
        array('phone','/^1[34578]{1}\d{9}$/','请填写正确的手机号码',1),

        array('is_lock',array(0,1),'值的范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内
        array('nickname','require','代理昵称不得为空'),
        array('wechat_id','require','微信号不得为空'),
        array('id' , 'require' , '参数丢失'),
    );

    //查看某个代理的信息
    public static function agentInfo($agentId = null)
    {
        $agentId    =   $agentId    ?   $agentId :  session('agentId');
        if(!$agentId){
            return false;
        }else{
            $agentInfo  =   D('agent')->where(['is_lock' =>  0 , 'is_delete' => 0 , 'id' => $agentId])->find();
            if($agentInfo){
                return $agentInfo;
            }
            return false;
        }
    }

    /**
     * Xadmin模块
     */
    //获取代理列表
    public function xdminGetAgentList()
    {
        $startTime      =   $this->get['start_time'];
        $endTime        =   $this->get['end_time'];
        $nickname       =   $this->get['nickname'];
        $phone          =   $this->get['phone'];

        $where = array();
        $whereStr = '';

        if( !empty($nickname) ) {
            $nickname   = addslashes($nickname);
            $nickname   = urldecode($nickname);
            $whereStr .= "AND o.nickname LIKE \"%{$nickname}%\" ";
            $where['nickname'] = array('LIKE', "%{$nickname}%");
        }
        if( !empty($phone) ) {
            $phone   = addslashes($phone);
            $phone   = urldecode($phone);
            $whereStr .= "AND o.phone LIKE \"%{$phone}%\" ";
            $where['phone'] = array('LIKE', "%{$phone}%");
        }
        if (!empty($startTime) && !empty($endTime)) {
            $startTime = addslashes($startTime);
            $startTime = urldecode($startTime);
            $startTime = str_replace('+', ' ', $startTime);
            $startTime = strtotime($startTime);

            $endTime = addslashes($endTime);
            $endTime = urldecode($endTime);
            $endTime = str_replace('+', ' ', $endTime);
            $endTime = strtotime($endTime);
            $whereStr .= "AND o.dateline BETWEEN {$startTime} AND {$endTime} ";
            $where['dateline'] = array('BETWEEN', array($startTime, $endTime));
        }

        $getData = array();
        $get = $this->get;
        foreach ($get as $key => $value) {
            if (!empty($key)) {
                $getData[$key] = urldecode($value);
            }
        }

        if (!empty($getData['add_time'])) {
            $getData['add_time'] = search_time_format($getData['add_time']);
        }

        if (!empty($getData['end_time'])) {
            $getData['end_time'] = search_time_format($getData['end_time']);
        }

        $AgentModel = M('agent');

        $count    = $AgentModel->where($where)->count();
        $page     = new \Think\Page($count, 25,$getData);

        if (iswap()) {
            $page->rollPage	= 5;
        }

        $show     = $page->show();

        $agentList = $AgentModel->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('dateline DESC')->select();
        dump(self::_getChild($agentList));exit;
        $AGENT_LEVEL = C('AGENT_LEVEL');

        $configList = M('agent_rebate_config')->where('1=1')->field(true)->order('id ASC')->select();
        // $levelData  =   C('AGENT_LEVEL');
        $levelData  =   array();
        foreach ($configList as $key => $value) {
            $levelData[$value['id']] = $value;
        }

        foreach ($agentList as $key => $value) {
            //下级人数
            $son_agent_count = M('agent')->where(array('pid'=>$value['id']))->count();
            $agentList[$key]['son_count'] = intval($son_agent_count);

            //玩家人数
            $player_count = M('gameuser')->where(array('invitation_code'=>$value['invitation_code'],'user_id'=>0))->count();
            $agentList[$key]['player_count'] = intval($player_count);


            if(!empty($value['pid'])){
                $parent_agent_info  =   M('agent')->where(array('id'=>$value['pid']))->field('id,nickname')->find();
                $agentList[$key]['p_info']  =   $parent_agent_info;

                $rebateMoneyPercent1 = $levelData[2]['player']; //平台返佣
            }else{

                $rebateMoneyPercent1 = $levelData[1]['player']; //平台返佣
            }

            $agent_one_rebate_config = M('agent_one_rebate_config')->where(array('agent_id'=>$value['id']))->field(true)->find();


            $agentList[$key]['rebateMoneyPercent1']  =   !empty($agent_one_rebate_config) ? $agent_one_rebate_config['player'] : $rebateMoneyPercent1;
            $agentList[$key]['rebateMoneyPercent2']  =   $levelData[2]['parent_lever'];

            $agentList[$key]['level_txt']  =   !empty($AGENT_LEVEL[$value['level']]['level_name']) ? $AGENT_LEVEL[$value['level']]['level_name'] : '未知';
        }

        return [$agentList , $show];
    }

    //添加代理
    public function xadminAddAgent()
    {
        $AgentModel = D('Agent');
        //判断验证是否通过
        if(!$AgentModel->create($this->post , 1)){
            return [false , $AgentModel->getError()];
        }else{
            $phone_agent_count      =   M('agent')->where(array('phone'=>$this->get['phone'],'is_delete'=>0))->count();

            if($phone_agent_count){
                return [false , '当前手机号码已存在'];
            }
            
            $data['nickname']      =   $this->post['nickname'];
            $data['wechat_id']      =  $this->post['wechat_id'];
            $data['phone']          =   $this->post['phone'];
            $data['dateline']       =   NOW_TIME;
            $data['agent_password']=    think_ucenter_md5(INIT_PASS);
            $data['level']           =   1;
            $res = $AgentModel->add($data);

            if ( $res ) {
                $invitation_code = 880000+$res;
                $res2 = $AgentModel->where(array('id'=>$res))->setField(array('invitation_code'=>$invitation_code));
                if (!$res2) {
                    return [false , '邀请码生成失败'];
                }
                return [true , '添加成功'];
            } else {
                return [false , '添加失败'];
            }
        }


    }

    //修改代理
    public function xadminEditAgent()
    {
        $AgentModel =   D('Agent');
        if(!$AgentModel->create($this->post , 2)){
            return [false , $AgentModel->getError()];
        } else {
            $data['nickname']      =   $this->post['nickname'];
            $data['wechat_id']      =  $this->post['wechat_id'];
            $data['phone']          =   $this->post['phone'];
            $data['dateline']       =   NOW_TIME;

            if ( $AgentModel->where(array('id'=>intval($this->post['id'])))->data($data)->save() >= 0 ) {
                return [true , '修改成功'];
            } else {
                return [false , '修改失败'];
            }
        }
    }

    //重置代理密码
    public function xadminResetPwd()
    {
        $agentId    =   intval($this->get['id']);
        if(!$agentId){
            return [false , '参数缺失'];
        }
        if(D('agent')->where(['id'  =>  $agentId])->save(['agent_password'  => think_ucenter_md5(INIT_PASS)]) !== false){
            return [true , '重置成功'];
        }else{
            return [false , '重置失败'];
        }
    }

    //返佣配置获取
    public function xadminRebateConfig()
    {
        $agent_id = intval($this->get['id']);
        //代理信息
        $agentInfo = self::agentInfo($agent_id);

        $agent_one_rebate_config = M('agent_one_rebate_config')->where(array('agent_id'=>$agent_id))->field(true)->find();

        $configList = M('agent_rebate_config')->where(array('agent_id'=>$agent_id))->field(true)->order('id ASC')->select();
        $levelData  =   array();
        foreach ($configList as $key => $value) {
            $levelData[$value['id']] = $value;
        }

        if(!empty($agentInfo['pid'])){
            $rebateMoneyPercent1 = $levelData[2]['player']; //平台返佣
        }else{
            $rebateMoneyPercent1 = $levelData[1]['player']; //平台返佣
        }
        $agentInfo['rebateMoneyPercent1']  =  !empty($agent_one_rebate_config) ? $agent_one_rebate_config['player'] : $rebateMoneyPercent1;
        
        return [$agentInfo , $agent_one_rebate_config];
    }

    //单个代理返佣记录
    public function xadminOneRebateConfig()
    {
        $agent_id   =   intval($this->post['id']);
        $player     =   floatval($this->post['player']);

        $agent_one_rebate_config = M('agent_one_rebate_config')->where(array('agent_id'=>$agent_id))->field(true)->find();
        if ($player > 100 || $player < 0) {
            return [false , '分佣比例在0到100之间'];
        }
        if (empty($agent_one_rebate_config)) {
            $data = array();
            $data['agent_id'] = $agent_id;
            $data['player']   = $player;
            $data['update_time'] = NOW_TIME;
            $res = M('agent_one_rebate_config')->add($data);
        }else{
            $data = array();
            $data['player']   = $player;
            $data['update_time'] = NOW_TIME;
            $res = M('agent_one_rebate_config')->where(array('agent_id'=>$agent_id))->setField($data);
        }
        if ($res) {
            return [true , '操作成功'];
        }else{
            return [false , '操作失败'];
        }
    }

    //代理提现
    public function xadminIncomeReport()
    {
        $startTime      =   $this->get['start_time'];
        $endTime        =   $this->get['end_time'];
        $nickname       =   $this->get['nickname'];
        $phone          =   $this->get['phone'];

        $where = array();
        $whereStr = '';

        if( !empty($nickname) ) {
            $nickname   = addslashes($nickname);
            $nickname   = urldecode($nickname);
            $where['a.nickname'] = array('LIKE', "%{$nickname}%");
        }
        if( !empty($phone) ) {
            $phone   = addslashes($phone);
            $phone   = urldecode($phone);
            $where['a.phone'] = array('LIKE', "%{$phone}%");
        }

        if (!empty($startTime) && !empty($endTime)) {
            $startTime = addslashes($startTime);
            $startTime = urldecode($startTime);
            $startTime = str_replace('+', ' ', $startTime);
            $startTime = strtotime($startTime);

            $endTime = addslashes($endTime);
            $endTime = urldecode($endTime);
            $endTime = str_replace('+', ' ', $endTime);
            $endTime = strtotime($endTime);
            $where['w.dateline'] = array('BETWEEN', array($startTime, $endTime));
        }


        $getData = array();
        $get = I('get.');
        foreach ($get as $key => $value) {
            if (!empty($key)) {
                $getData[$key] = urldecode($value);
            }
        }

        if (!empty($getData['add_time'])) {
            $getData['add_time'] = search_time_format($getData['add_time']);
        }

        if (!empty($getData['end_time'])) {
            $getData['end_time'] = search_time_format($getData['end_time']);
        }

        $AgentModel = M('agent_withdrawals');

        $count    = $AgentModel->alias('w')->join('LEFT JOIN '.C('DB_PREFIX').'agent as a ON w.agent_id=a.id')->where($where)->count();
        $page     = new \Think\Page($count, 10,$getData);

        if (iswap()) {
            $page->rollPage = 5;
        }

        $show     = $page->show();

        $join = ' LEFT JOIN '.C('DB_PREFIX').'agent as a ON w.agent_id=a.id';
        $join .= ' LEFT JOIN '.C('DB_PREFIX').'agent_withdrawals_account as awa ON awa.agent_id=a.id';
        $limit  = $page->firstRow . ',' . $page->listRows;
        $order = 'w.id DESC';
        $fields = 'w.id,w.money,w.dateline,w.status,a.nickname,a.phone,a.wechat_id,awa.truename,awa.alipay_account,awa.bank_name,awa.bank_card,awa.bank_subbranch';
        $agentList = $AgentModel->alias('w')->join($join)->where($where)->limit($limit)->order($order)->field($fields)->select();

        return [$agentList , $show];

    }

    //代理审核
    public function xadminAgentExamine()
    {
        $startTime      =   $this->get['start_time'];
        $endTime        =   $this->get['end_time'];
        $nickname       =   $this->get['nickname'];
        $phone          =   $this->get['phone'];

        $where = array();
        $whereStr = '';

        if( !empty($nickname) ) {
            $nickname   = addslashes($nickname);
            $nickname   = urldecode($nickname);
            $whereStr .= "AND o.nickname LIKE \"%{$nickname}%\" ";
            $where['nickname'] = array('LIKE', "%{$nickname}%");
        }
        if( !empty($phone) ) {
            $phone   = addslashes($phone);
            $phone   = urldecode($phone);
            $whereStr .= "AND o.phone LIKE \"%{$phone}%\" ";
            $where['phone'] = array('LIKE', "%{$phone}%");
        }
        if (!empty($startTime) && !empty($endTime)) {
            $startTime = addslashes($startTime);
            $startTime = urldecode($startTime);
            $startTime = str_replace('+', ' ', $startTime);
            $startTime = strtotime($startTime);

            $endTime = addslashes($endTime);
            $endTime = urldecode($endTime);
            $endTime = str_replace('+', ' ', $endTime);
            $endTime = strtotime($endTime);
            $whereStr .= "AND o.dateline BETWEEN {$startTime} AND {$endTime} ";
            $where['dateline'] = array('BETWEEN', array($startTime, $endTime));
        }

        $getData = array();
        $get = $this->get;
        foreach ($get as $key => $value) {
            if (!empty($key)) {
                $getData[$key] = urldecode($value);
            }
        }

        if (!empty($getData['add_time'])) {
            $getData['add_time'] = search_time_format($getData['add_time']);
        }
        if (!empty($getData['end_time'])) {
            $getData['end_time'] = search_time_format($getData['end_time']);
        }

        $AgentModel = M('agent_applying');

        $count    = $AgentModel->where($where)->count();
        $page     = new \Think\Page($count, 25,$getData);

        if (iswap()) {
            $page->rollPage = 5;
        }

        $show     = $page->show();

        $agentList = $AgentModel->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('status ASC,dateline DESC')->select();

        $apply_type_txt = array('1'=>'上级添加','2'=>'个人申请');
        $AGENT_LEVEL = C('AGENT_LEVEL');
        foreach ($agentList as $key => $value) {
            if(!empty($value['pid'])){
                $parent_agent_info  =   M('agent')->where(array('id'=>$value['pid']))->field('id,nickname')->find();
                $agentList[$key]['p_info']  =   $parent_agent_info;
            }
            $agentList[$key]['level_txt']  =   !empty($AGENT_LEVEL[$value['level']]['level_name']) ? $AGENT_LEVEL[$value['level']]['level_name'] : '未知';
            $agentList[$key]['from_txt']  =   $apply_type_txt[$value['apply_type']];
        }
       return [$agentList , $show];
    }
}