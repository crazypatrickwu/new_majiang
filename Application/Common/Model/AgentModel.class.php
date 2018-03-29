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
    const   INIT_PASS = 12345;

    public  $table;

    public function _initialize()
    {
        parent::_initialize();
        //获取当前表名
        $this->table    =   self::_getTable(__CLASS__);
    }

    /**
     * 输入数据的验证规则
     */
    protected $_validate = array(
        array('phone', 'require', '手机号码不得为空！'), //默认情况下用正则进行验证
        array('phone', '/^1[34578]{1}\d{9}$/', '请填写正确的手机号码', 1),

        array('is_lock', array(0, 1), '值的范围不正确！', 2, 'in'), // 当值不为空的时候判断是否在一个范围内
        array('nickname', 'require', '代理昵称不得为空'),
        array('wechat_id', 'require', '微信号不得为空'),
        array('id', 'require', '参数丢失'),
    );

    //查看某个代理的信息
    public static function agentInfo($agentId = null)
    {
        $agentId = $agentId ? $agentId : session('agentId');
        if (!$agentId) {
            return false;
        } else {
            $agentInfo = D('agent')->where(['is_lock' => 0, 'is_delete' => 0, 'id' => $agentId])->find();
            if ($agentInfo) {
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
        $where = [];
        if (!empty($this->get['nickname'])) {
            $where['nickname'] = array('LIKE', "%{$this->get['nickname']}%");
        }
        if (!empty($this->get['phone'])) {
            $where['phone'] = array('LIKE', "%{$this->get['nickname']}%");
        }
        if (!empty($this->get['start_time'])) {
            $where['dateline'] = array('EGT', strtotime(search_time_format($this->get['start_time'])));
        }
        if (!empty($this->get['end_time'])) {
            $where['dateline'] = array('ELT', strtotime(search_time_format($this->get['end_time'])));
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

        $count = $AgentModel->where($where)->count();
        $page = new \Think\Page($count, 25, $getData);

        if (iswap()) {
            $page->rollPage = 5;
        }

        $show = $page->show();

        $agentList = $AgentModel->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('dateline DESC')->select();
//        echo $AgentModel->getLastSql();exit;
        foreach ($agentList as $key => $value) {
            //查找上级玩家信息
            $agentList[$key]['p_info'] = M('agent')->where(['is_lock' => 0, 'is_delete' => 0, 'id' => $value['pid']])->field('nickname')->find();

            //下级人数包括子孙级
            $son_agent_count = M('agent')->where(array('pid' => $value['id']))->count();
            $agentList[$key]['son_count'] = intval($son_agent_count);

            //玩家人数
            $player_count = M('gameuser')->where(array('invitation_code' => $value['invitation_code'], 'user_id' => 0))->count();
            $agentList[$key]['player_count'] = intval($player_count);

            //平台和上级的返佣比例
            $player = M('agent_one_rebate_config')->where(['agent_id' => $value['id']])->field('player')->find();
            $agentList[$key]['rebateMoneyPercent2'] = isset($player['player']) ? $player['player'] : 0;
        }

        return [$agentList, $show];
    }

    //添加代理
    public function xadminAddAgent()
    {
        $AgentModel = D('Agent');
        //判断验证是否通过
        if (!$AgentModel->create($this->post, 1)) {
            return [false, $AgentModel->getError()];
        } else {
            $phone_agent_count = M('agent')->where(array('phone' => $this->get['phone'], 'is_delete' => 0))->count();

            if ($phone_agent_count) {
                return [false, '当前手机号码已存在'];
            }

            $data['nickname'] = $this->post['nickname'];
            $data['wechat_id'] = $this->post['wechat_id'];
            $data['phone'] = $this->post['phone'];
            $data['dateline'] = NOW_TIME;
            $data['agent_password'] = think_ucenter_md5(INIT_PASS);
            $data['level'] = 1;
            $res = $AgentModel->add($data);

            if ($res) {
                $invitation_code = 880000 + $res;
                $res2 = $AgentModel->where(array('id' => $res))->setField(array('invitation_code' => $invitation_code));
                if (!$res2) {
                    return [false, '邀请码生成失败'];
                }
                return [true, '添加成功'];
            } else {
                return [false, '添加失败'];
            }
        }


    }

    //修改代理
    public function xadminEditAgent()
    {
        $AgentModel = D('Agent');
        if (!$AgentModel->create($this->post, 2)) {
            return [false, $AgentModel->getError()];
        } else {
            $data['nickname'] = $this->post['nickname'];
            $data['wechat_id'] = $this->post['wechat_id'];
            $data['phone'] = $this->post['phone'];
            $data['dateline'] = NOW_TIME;

            if ($AgentModel->where(array('id' => intval($this->post['id'])))->data($data)->save() >= 0) {
                return [true, '修改成功'];
            } else {
                return [false, '修改失败'];
            }
        }
    }

    //重置代理密码
    public function xadminResetPwd()
    {
        $agentId = intval($this->get['id']);
        if (!$agentId) {
            return [false, '参数缺失'];
        }
        if (D('agent')->where(['id' => $agentId])->save(['agent_password' => think_ucenter_md5(INIT_PASS)]) !== false) {
            return [true, '重置成功'];
        } else {
            return [false, '重置失败'];
        }
    }

    //返佣配置获取
    public function xadminRebateConfig()
    {
        $agent_id = intval($this->get['id']);
        //代理信息
        $agentInfo = self::agentInfo($agent_id);

        $agent_one_rebate_config = M('agent_one_rebate_config')->where(array('agent_id' => $agent_id))->field(true)->find();

        $configList = M('agent_rebate_config')->where(array('agent_id' => $agent_id))->field(true)->order('id ASC')->select();
        $levelData = array();
        foreach ($configList as $key => $value) {
            $levelData[$value['id']] = $value;
        }

        if (!empty($agentInfo['pid'])) {
            $rebateMoneyPercent1 = $levelData[2]['player']; //平台返佣
        } else {
            $rebateMoneyPercent1 = $levelData[1]['player']; //平台返佣
        }
        $agentInfo['rebateMoneyPercent1'] = !empty($agent_one_rebate_config) ? $agent_one_rebate_config['player'] : $rebateMoneyPercent1;

        return [$agentInfo, $agent_one_rebate_config];
    }

    //销售业绩
    public function xadminAgentSalesVolumeRecored()
    {
        $where = array();
        $where['agent_id'] = intval($this->get['agentId']);

        if (!empty($this->get['start_time'])) {
            $where['dateline'] = array('EGT', strtotime(search_time_format($this->get['start_time'])));
        }
        if (!empty($this->get['end_time'])) {
            $where['dateline'] = array('ELT', strtotime(search_time_format($this->get['end_time'])));
        }

        $userid = intval($this->get['userid']);
        if ($userid != 0) {
            $where['uid'] = $userid;
        }

        $count = M('agent_sales_volume')->where($where)->count();
        $page = new \Think\Page($count, 10);

        if (iswap()) {
            $page->rollPage = 5;
        }
        $show = $page->show();

        $agent_sales_volume_recored = M('agent_sales_volume')->where($where)->field(true)->limit($page->firstRow . ',' . $page->listRows)->order('id DESC')->select();

        $agent_sales_volume_total = M('agent_sales_volume')->where($where)->sum('total_price');

        return [$agent_sales_volume_recored, $agent_sales_volume_total, $data, $show];
    }

    //单个代理返佣记录
    public function xadminOneRebateConfig()
    {
        $agent_id = intval($this->post['id']);
        $player = floatval($this->post['player']);

        $agent_one_rebate_config = M('agent_one_rebate_config')->where(array('agent_id' => $agent_id))->field(true)->find();
        if ($player > 100 || $player < 0) {
            return [false, '分佣比例在0到100之间'];
        }
        if (empty($agent_one_rebate_config)) {
            $data = array();
            $data['agent_id'] = $agent_id;
            $data['player'] = $player;
            $data['update_time'] = NOW_TIME;
            $res = M('agent_one_rebate_config')->add($data);
        } else {
            $data = array();
            $data['player'] = $player;
            $data['update_time'] = NOW_TIME;
            $res = M('agent_one_rebate_config')->where(array('agent_id' => $agent_id))->setField($data);
        }
        if ($res) {
            return [true, '操作成功'];
        } else {
            return [false, '操作失败'];
        }
    }

    //代理提现
    public function xadminIncomeReport()
    {
        $where = array();
        if (!empty($this->get['nickname'])) {
            $where['a.nickname'] = array('LIKE', "%{$this->get['nickname']}%");
        }
        if (!empty($this->get['phone'])) {
            $where['a.phone'] = array('LIKE', "%{$this->get['phone']}%");
        }
        if (!empty($this->get['start_time'])) {
            $where['dateline'] = array('EGT', strtotime(search_time_format($this->get['start_time'])));
        }
        if (!empty($this->get['end_time'])) {
            $where['dateline'] = array('ELT', strtotime(search_time_format($this->get['end_time'])));
        }

        $AgentModel = M('agent_withdrawals');

        $count = $AgentModel->alias('w')->join('LEFT JOIN ' . C('DB_PREFIX') . 'agent as a ON w.agent_id=a.id')->where($where)->count();
        $page = new \Think\Page($count, 10, $this->get);

        if (iswap()) {
            $page->rollPage = 5;
        }

        $show = $page->show();

        $join = ' LEFT JOIN ' . C('DB_PREFIX') . 'agent as a ON w.agent_id=a.id';
        $join .= ' LEFT JOIN ' . C('DB_PREFIX') . 'agent_withdrawals_account as awa ON awa.agent_id=a.id';
        $limit = $page->firstRow . ',' . $page->listRows;
        $order = 'w.id DESC';
        $fields = 'w.id,w.money,w.dateline,w.status,a.nickname,a.phone,a.wechat_id,awa.truename,awa.alipay_account,awa.bank_name,awa.bank_card,awa.bank_subbranch';
        $agentList = $AgentModel->alias('w')->join($join)->where($where)->limit($limit)->order($order)->field($fields)->select();

        return [$agentList, $show];
    }

    //查询提现信息
    public function xadminGetIncomeReport()
    {
        $id = intval($this->get['id']);
        if (empty($id)) {
            return [false , '参数错误' , null];
        }

        $sql_prefix = C('DB_PREFIX');

        $where = array('aw.id' => $id);
        $agent_withdrawals = M('agent_withdrawals')->alias('aw')
            ->join($sql_prefix . 'agent AS a ON a.id = aw.agent_id')
            ->join('LEFT JOIN ' . $sql_prefix . 'agent_withdrawals_account AS awa ON awa.agent_id = a.id')
            ->field('a.id as agent_id,a.nickname,a.phone,aw.id,aw.money,aw.dateline,awa.truename,awa.alipay_account,awa.bank_card')
            ->where($where)->find();
        if (empty($agent_withdrawals)) {
            return [false , '信息检索失败' , null];
        }
        if ($agent_withdrawals['status']) {
            return [false , '当前提现信息已处理' , null];
        }
        return [true , '' , $agent_withdrawals];
    }

    //提现处理
    public function xadminIncomeReportHandler()
    {
            $adminId = session('adminId');
            if (!$adminId) {
                return [false , '登录信息已过期' ];
            }
            $admin_info = M('admin')->where($adminId)->field(true)->find();
            if (empty($admin_info)) {
                return [false , '登录信息不存在或已过期'];
            }
            $admin_password =   $this->post['admin_password'];
            if ($admin_password == '') {
                return [false , '请输入登录密码'];
            }

            if ($admin_info['admin_password'] != think_ucenter_md5($admin_password)) {
                return [false , '密码错误'];
            }

            $id = intval($this->post['id']);
            if (empty($id)) {
                return [false , '参数错误'];
            };

            $status = intval($this->post['status']);
            $reason = $this->post['reason'];
            if ($status == 0) {
                return [false , '请选择操作结果'];
            } elseif ($status == 2) {
                if (empty($reason)) {
                    return [false , '请输入驳回理由'];
                }
            }

            $where = array('id' => $id);
            $agent_withdrawals = M('agent_withdrawals')->where($where)->field(true)->find();
            if (empty($agent_withdrawals)) {
                return [false , '信息检索失败'];
            }
            if ($agent_withdrawals['status']) {
                return [false , '当前提现信息已处理'];
            }

            $data = array();
            $data['status'] = $status;
            $data['update_time'] = NOW_TIME;
            $data['admin_id'] = $adminId;
            $data['reason'] = $reason;
            switch ($status) {
                case '1':   //同意提现申请

                    $agent_withdrawals_account = M('agent_withdrawals_account')->where(array('agent_id' => $agent_withdrawals['agent_id']))->field(true)->find();
                    if (empty($agent_withdrawals_account['alipay_account']) && empty($agent_withdrawals_account['bank_card'])) {
                        return [false , '提现账户信息不全'];
                    }
                    $res2 = M('agent_withdrawals')->where($where)->setField($data);

                    //账单流水
                    $agent_bill_data = array();
                    $agent_bill_data['type'] = 3;  //1=会员返利，2=推荐返利，3=提现
                    $agent_bill_data['change_type'] = 2;  //1=收入，2=支出
                    $agent_bill_data['agent_id'] = $agent_withdrawals['agent_id'];

                    $agent_bill_data['money'] = $agent_withdrawals['money'];
                    $agent_bill_data['desc'] = '提现[' . $agent_withdrawals['money'] . ']元';
                    $agent_bill_data['dateline'] = NOW_TIME;
                    $res2003 = M('agent_bill_list')->add($agent_bill_data);
                    break;
                case '2':   //驳回提现申请
                    $agent_info = M('agent')->where(array('id' => $agent_withdrawals['agent_id']))->field(true)->find();
                    if (empty($agent_info)) {
                        return [false , '代理数据有误'];
                    }
                    $agent_Data = array();
                    $agent_Data['available_balance'] = $agent_info['available_balance'] + $agent_withdrawals['money'] + 5;
                    $res1 = M('agent')->where(array('id' => $agent_withdrawals['agent_id']))->setField($agent_Data);
                    if (!$res1) {
                        return [false , '操作失败'];
                    }
                    $res2 = M('agent_withdrawals')->where($where)->setField($data);
                    break;
                default:
                    break;
            }
            if ($res2) {
                return [true , '操作成功'];
            } else {
                return [false , '操作失败'];
            }

    }

    //代理审核
    public function xadminAgentExamine()
    {
        $where = [];
        if (!empty($this->get['nickname'])) {
            $where['nickname'] = array('LIKE', "%{$this->get['nickname']}%");
        }
        if (!empty($this->get['phone'])) {
            $where['phone'] = array('LIKE', "%{$this->get['nickname']}%");
        }
        if (!empty($this->get['start_time'])) {
            $where['dateline'] = array('EGT', strtotime(search_time_format($this->get['start_time'])));
        }
        if (!empty($this->get['end_time'])) {
            $where['dateline'] = array('ELT', strtotime(search_time_format($this->get['end_time'])));
        }
        $AgentModel = M('agent_applying');

        $count = $AgentModel->where($where)->count();
        $page = new \Think\Page($count, 25, $this->get);

        if (iswap()) {
            $page->rollPage = 5;
        }

        $show = $page->show();

        $agentList = $AgentModel->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('status ASC,dateline DESC')->select();

        $apply_type_txt = array('1' => '上级添加', '2' => '个人申请');
        $AGENT_LEVEL = C('AGENT_LEVEL');
        foreach ($agentList as $key => $value) {
            if (!empty($value['pid'])) {
                $parent_agent_info = M('agent')->where(array('id' => $value['pid']))->field('id,nickname')->find();
                $agentList[$key]['p_info'] = $parent_agent_info;
            }
            $agentList[$key]['level_txt'] = !empty($AGENT_LEVEL[$value['level']]['level_name']) ? $AGENT_LEVEL[$value['level']]['level_name'] : '未知';
            $agentList[$key]['from_txt'] = $apply_type_txt[$value['apply_type']];
        }
        return [$agentList, $show];
    }

    //审核处理
    public function xadminAgentExamineHandler()
    {
        //自主申请的代理需要审核才能通过，申请状态和添加代理之后才算成功

        $id =   intval($this->get['id']);
        if (empty($id)) {
            return [false , '参数缺失'];
        }
        $apply  =   M('agent_applying');
        $where = array('id'=>$id);
        $agent_applying = $apply->where($where)->field(true)->find();
        if (empty($agent_applying)) {
            return [false , '信息检索失败'];
        }
        if ($agent_applying['status']) {
            return [false , '当前提现信息已处理'];
        }

        //开启事务，修改审核状态，添加代理，修改代理的邀请码

        $agent  =   M('agent');
        $apply->startTrans();
            $res = M('agent_applying')->where($where)->setField(array('status'=>1,'status_time'=>NOW_TIME));
            if ($agent_applying['apply_type']) {
                $agentData['pid'] = ($agent_applying['apply_type'] == 1) ? $agent_applying['pid'] : 0;
                $agentData['rid'] = ($agent_applying['apply_type'] == 2) ? $agent_applying['rid'] : 0;
                $agentData['agent_password'] = think_ucenter_md5('12345');
                $agentData['nickname'] = $agent_applying['nickname'];
                $agentData['phone'] = $agent_applying['phone'];
                $agentData['wechat_id'] = $agent_applying['wechat_id'];
                $agentData['dateline'] = NOW_TIME;
                $agentData['is_lock'] = 0;
                $agentData['level'] = $agent_applying['level'];
                // $agentData['invitation_code'] = randomString(6);
                $res2 = M('agent')->add($agentData);
                if (!$res2) {
                    $apply->rollback();
                    return [false , '操作失败'];
                }else{
                    $invitation_code = 880000+$res2;
                    $res3 = M('Agent')->where(array('id'=>$res2))->setField(array('invitation_code'=>$invitation_code));
                }
            }
        if($res && $res2 && $res3){
            $apply->commit();
            return [true , '操作成功'];
        }else{
            $apply->rollback();
            return [false , '操作失败'];
        }
    }
}